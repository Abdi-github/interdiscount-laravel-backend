<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Store\Models\Store;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreInventoryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StoreInventory::with(['store', 'product.brand']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->integer('store_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->input('stock_status') === 'low') {
            $query->lowStock();
        } elseif ($request->input('stock_status') === 'out') {
            $query->outOfStock();
        }

        $inventory = $query->orderBy('updated_at', 'desc')->paginate(20);

        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Inventory/Index', [
            'inventory' => StoreInventoryResource::collection($inventory),
            'stores' => $stores,
            'filters' => $request->only(['search', 'store_id', 'stock_status']),
        ]);
    }
}
