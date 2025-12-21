<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function created(mixed $data, string $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function paginated(LengthAwarePaginator $paginator, string $resource, string $message = 'Success'): JsonResponse
    {
        $resourceClass = "App\\Http\\Resources\\{$resource}";

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => class_exists($resourceClass)
                ? $resourceClass::collection($paginator->items())
                : $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    protected function error(string $message, int $status = 400, ?string $code = null, mixed $details = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($code || $details) {
            $error = [];
            if ($code) {
                $error['code'] = $code;
            }
            if ($details) {
                $error['details'] = $details;
            }
            $response['error'] = $error;
        }

        return response()->json($response, $status);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404, 'NOT_FOUND');
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401, 'UNAUTHORIZED');
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403, 'FORBIDDEN');
    }
}
