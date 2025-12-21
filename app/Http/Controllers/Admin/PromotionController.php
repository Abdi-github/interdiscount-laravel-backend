<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Promotion\Models\StorePromotion;
use App\Domain\Store\Models\Store;
use App\Http\Controllers\Controller;
use App\Http\Resources\StorePromotionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StorePromotion::with(['store', 'product', 'category', 'creator']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->integer('store_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(20);

        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Promotions/Index', [
            'promotions' => StorePromotionResource::collection($promotions),
            'stores' => $stores,
            'filters' => $request->only(['search', 'store_id', 'is_active']),
        ]);
    }
}
