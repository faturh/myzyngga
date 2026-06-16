<?php

namespace App\Shared\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public static function paginated(LengthAwarePaginator $paginator, array $data): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'errors' => null,
        ]);
    }

    public static function error(string $message, int $status = 422, array $context = []): JsonResponse
    {
        return response()->json([
            'data' => null,
            'errors' => [
                'message' => $message,
                'context' => $context,
            ],
        ], $status);
    }
}
