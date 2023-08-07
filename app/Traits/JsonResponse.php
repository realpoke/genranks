<?php

namespace App\Traits;

trait JsonResponses
{
    protected function success($data, $message = null, $code = 200, $status = 'Request was successful.')
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data, $message = null, $code = 500)
    {
        return $this->success($data, $message, $code, 'Error has occurred...');
    }
}
