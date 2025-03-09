<?php

namespace App;

class Request
{
    private array $data;
    private array $files;
    private array $headers;

    public function __construct()
    {
        $this->files = $_FILES;
        $this->headers = getallheaders();
        $this->data = $this->mergeInputs();
    }

    private function mergeInputs(): array
    {
        $input = [];

        if (!empty($_POST)) {
            $input = $_POST;
        }

        if ($this->isJson()) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        return array_merge($input, $_GET);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function get(string $key, $default = null)
    {
        return $this->input($key, $default);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function isJson(): bool
    {
        return strpos($this->headers['Content-Type'] ?? '', 'application/json') !== false;
    }

    public function header(string $headerName, $defaultValue = null): mixed
    {
        return $this->headers[$headerName] ?? $defaultValue;
    }
}
