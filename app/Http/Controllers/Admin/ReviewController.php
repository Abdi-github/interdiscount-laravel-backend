<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Review\Services\ReviewService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'is_approved', 'rating']);
        $reviews = $this->reviewService->paginate(
            $filters,
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Reviews/Index', [
            'reviews' => ReviewResource::collection($reviews),
            'filters' => $request->only(['search', 'is_approved', 'rating', 'per_page']),
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $this->reviewService->approve($id);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review approved successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->reviewService->delete($id);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully');
    }
}
