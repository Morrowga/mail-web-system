<?php

namespace App\Traits;

trait ApiResponses
{
    public function success($message, $data = [], $token = null, $status = 200)
    {
        if ($token != null) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'token' => $token
            ], $status);
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function error($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
        ], $status);
    }
}
