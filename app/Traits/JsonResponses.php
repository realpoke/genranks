<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponses
{
    protected function success(
        string $message,
        ?array $data = null,
        int $code = 200,
        string $status = 'Request was successful.'
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(
        string $message,
        ?array $data = null,
        int $code = 500,
        string $status = 'Error has occurred...'
    ): JsonResponse {
        return $this->success($message, $data, $code, $status);
    }
}
