<?php

namespace Api\Maicoldev;

class Request
{

    private array $data = [];

    public function setData(string $key, $value): void
    {
        # code...
        $this->data[$key] = $value;
    }

    public function getData(string $key): array
    {
        # code...
        return $this->data[$key] ?? null;
    }
}
