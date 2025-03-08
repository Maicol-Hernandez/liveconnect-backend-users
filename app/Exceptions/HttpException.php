<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class HttpException extends Exception
{
    public function __construct(
        string $message = 'Internal Server Error',
        int $status_http_code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $status_http_code, $previous);
    }
}
