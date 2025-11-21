<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Search\Services\SearchService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponse;

    public function __construct(
        private SearchService $searchService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return $this->error('Search query is required', 422, 'VALIDATION_ERROR');
        }

        $filters = $request->only([
            'category_id', 'brand_id', 'min_price', 'max_price', 'availability_state',
        ]);

        $perPage = (int) $request->input('limit', 20);
        $results = $this->searchService->search($query, $filters, $perPage);

        return $this->paginated($results, 'ProductResource', 'Search results retrieved successfully');
    }
}
