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

if (!function_exists('saveLog')) {
    function saveLog(Throwable $exception): void
    {
        $log_dir = $_ENV['ROOT_PROJECT'] . '/logs/';

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $data = [
            'DATE' => date('Y-m-d H:i:s'),
            'ENDPOINT'  => $_SERVER['REQUEST_URI'] ?? '',
            'METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
            'MESSAGE_ERROR' => $exception->getMessage(),
            'TRACE' => $exception->getTrace()
        ];

        $log_file = $log_dir . 'logs-' . date('Y-m-d') . '.log';

        file_put_contents($log_file, json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }
}
