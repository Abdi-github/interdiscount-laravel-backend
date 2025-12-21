<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Store\Models\Store;
use App\Domain\Transfer\Models\StockTransfer;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockTransferResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StockTransfer::with(['fromStore', 'toStore', 'initiator']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('store_id')) {
            $storeId = $request->integer('store_id');
            $query->where(function ($q) use ($storeId) {
                $q->where('from_store_id', $storeId)
                  ->orWhere('to_store_id', $storeId);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('transfer_number', 'like', "%{$search}%");
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);

        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Transfers/Index', [
            'transfers' => StockTransferResource::collection($transfers),
            'stores' => $stores,
            'filters' => $request->only(['search', 'status', 'store_id']),
        ]);
    }
}
