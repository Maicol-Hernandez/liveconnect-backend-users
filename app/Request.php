<?php

namespace Api\Maicoldev;

class Request
{

    private array $data = [];

    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getData(string $key): array
    {
        return $this->data[$key] ?? null;
    }
}
