<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}

