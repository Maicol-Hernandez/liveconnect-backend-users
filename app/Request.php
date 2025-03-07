<?php

namespace App;

class Request
{

    private array $data = [];

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function all(?string $key = null): array
    {
        return $this->data[$key] ?? null;
    }
}
