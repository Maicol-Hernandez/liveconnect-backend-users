<?php

namespace App\Exceptions;

use Exception;
use Throwable;


class ValidationException extends Exception
{
    protected array $errors = [];

    public function __construct(
        string $message = "",
        $code = 422,
        array $errors = [],
        ?Throwable $previous = null

    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
