<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponses
{
    protected function success(
        $message,
        $data = null,
        $code = 200,
        $status = 'Request was successful.'
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(
        $message = null,
        $data = null,
        $code = 500,
        $status = 'Error has occurred...'
    ): JsonResponse {
        return $this->success($data, $message, $code, $status);
    }
}
