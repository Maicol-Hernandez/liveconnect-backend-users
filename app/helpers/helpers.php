<?php

use App\Response;

if (!function_exists('view')) {
    function view(
        string $type,
        $data = null,
        int $status_code = 200,
        array $headers = []
    ): Response {
        return new Response($type, $data, $status_code, $headers);
    }
}
