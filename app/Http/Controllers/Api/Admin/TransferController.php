<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Transfer\Services\TransferService;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockTransferResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TransferService $transferService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $transfers = $this->transferService->paginate(
            $request->only(['search', 'status', 'from_store_id', 'to_store_id']),
            $request->integer('limit', 20),
        );
        // TODO: Implement transfer timeline visualization

        return $this->paginated($transfers, 'StockTransferResource', 'Transfers retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        /* Log::debug('TransferController show - fetching transfer details'); */
        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        $transfer->load(['fromStore', 'toStore', 'initiator', 'approver']);

        return $this->success(new StockTransferResource($transfer), 'Transfer retrieved successfully');
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:approve,reject'],
        ]);

        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        if ($transfer->status->value !== 'REQUESTED') {
            return $this->error('Transfer can only be approved/rejected when status is REQUESTED', 422);
        }

        $admin = request()->user();

        if ($request->input('action') === 'approve') {
            $transfer->update([
                'status' => 'APPROVED',
                'approved_by' => $admin->id,
            ]);
            $message = 'Transfer approved successfully';
        } else {
            $transfer->update([
                'status' => 'REJECTED',
                'approved_by' => $admin->id,
            ]);
            $message = 'Transfer rejected successfully';
        }

        return $this->success(new StockTransferResource($transfer->fresh()), $message);
    }

    public function analytics(): JsonResponse
    {
        $stats = [
            'total' => DB::table('stock_transfers')->count(),
            'by_status' => [
                'requested' => DB::table('stock_transfers')->where('status', 'REQUESTED')->count(),
                'approved' => DB::table('stock_transfers')->where('status', 'APPROVED')->count(),
                'rejected' => DB::table('stock_transfers')->where('status', 'REJECTED')->count(),
                'shipped' => DB::table('stock_transfers')->where('status', 'SHIPPED')->count(),
                'received' => DB::table('stock_transfers')->where('status', 'RECEIVED')->count(),
                'cancelled' => DB::table('stock_transfers')->where('status', 'CANCELLED')->count(),
            ],
            'this_month' => DB::table('stock_transfers')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'top_stores' => DB::table('stock_transfers')
                ->select('from_store_id', DB::raw('COUNT(*) as transfer_count'))
                ->groupBy('from_store_id')
                ->orderByDesc('transfer_count')
                ->limit(10)
                ->get(),
        ];

        return $this->success($stats, 'Transfer analytics retrieved successfully');
    }
}
