<?php

namespace App\Utils;

use Illuminate\Pagination\LengthAwarePaginator;

class ResponseJson {
    public static function success(
        $data = [],
        string $message = null,
        $code = 200
    ) {
        return response()->json([
            'success' => true,
            'message' => $message ??  __('response.success'),
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    public static function failed(
        $data = [],
        string $message = null,
        $code = 400
    ) {
        return response()->json([
            'success' => false,
            'message' => $message ?? __('response.failed'),
            'code' => $code,
            'data' => $data,
        ], $code);
    }
}
