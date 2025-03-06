<?php

namespace App;

use App\Exceptions\ApiException;


class Response
{

    private array $headers = [];
    private $response = "";
    private int $status_code = 0;

    public function __construct(
        string $type,
        $data = null,
        int $status_code = 200,
        array $headers = []
    ) {
        $this->status_code = $status_code;

        Response::headersTolower($headers);
        Response::validateStatusCode($this->status_code);
        Response::validateType($type, $data);
    }

    private static function headersTolower(array $headers): void
    {
        foreach ($headers as $header_name  => $header_value) {
            self::$headers[strtolower($header_name)] = $header_value;
        }
    }

    private static function validateStatusCode($status_code): void
    {
        # validamos el codigo de estado de la respuesta 

        if ($status_code < 100 || $status_code > 599) {
            # Invalid status code, must be a number between 100 and 599
            throw new ApiException("Invalid status code {$status_code}, must be a number between 100 and 599");
        }
    }

    private function validateType(string $type, $data): void
    {
        switch ($type) {
            case 'raw':
                $this->raw($data);
                break;
            case 'json':
                $this->json($data);
                break;

            case 'html';
                $this->html($data);
                break;

            default:
                # we respond with an exception
                throw new ApiException("Invalid Response Type {$type}, only valids are raw, json and html");
                break;
        }
    }

    private function raw(string $data): void
    {
        # validamos que tenga un content-type
        if (empty($this->headers['content-type'])) {
            # text/plain; charset=utf-8
            $this->headers['content-type'] = 'text/plain; charset=utf-8';
        }

        $this->response = $data;
    }

    private function json($data): void
    {
        # definimos los headers en el content-type con el application/json; charset=utf-8
        $this->headers['content-type'] = 'application/json; charset=utf-8';

        if ($this->status_code > 399) {
            $response = [
                'status' => 'error',
                'error' => $data
            ];
        } else {
            $response = [
                'status' => 'success',
                'data' => $data
            ];
        }

        $this->response = $response;
    }

    private function html(string $data): void
    {
        # text/html; charset=utf-8
        $this->headers['content-type'] = 'text/html; charset=utf-8';

        $this->response = $data;
    }

    public function returnData()
    {
        foreach ($this->headers as $header_name => $header_value) {
            header("$header_name: $header_value");
        }

        http_response_code($this->status_code);

        echo json_encode($this->response);
    }
}
