<?php

namespace App\Http\shared;
use Illuminate\Http\JsonResponse;
class Respond
{
    /**
     * @param  int         $statusCode
     * @param  string|null $message
     * @param  mixed       $data
     * @return JsonResponse
     */
    public static function Success(int $statusCode, ?string $message, mixed $data = null): JsonResponse
    {
        $responseData = ["success" => true, "message" => $message, "data" => $data ?? []];
        return response()->json($responseData, $statusCode);
    }

    /**
     * @param  int         $statusCode
     * @param  string|null $message
     * @param  mixed       $error
     * @return JsonResponse
     */
    public static function Error(int $statusCode, ?string $message, mixed $error = null): JsonResponse
    {
        $responseData = ["success" => false, "message" => $message, "errors" => $error ?? []];
        return response()->json($responseData, $statusCode);
    }
}
