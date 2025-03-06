<?php

namespace Api\Maicoldev;

use Api\Maicoldev\Router;
use Api\Maicoldev\Response;
use Api\Maicoldev\Exceptions\HttpException;
use Api\Maicoldev\Exceptions\ApiException;

use Throwable;


class App
{
    public function __construct()
    {
        $this->checkEnv();
    }

    /**
     * 
     */
    private function checkEnv(): void
    {
        # code...
        if (empty($_ENV['ROOT_PROJECT'])) {
            # code...
            throw new ApiException("You must define ROOT_PROJECT environment variable with root path of the project");
        }

        if (!is_dir($_ENV['ROOT_PROJECT'])) {
            # code...
            throw new ApiException("ROOT_PROJECT environment variable dir not exists");
        }

        $last_char = substr($_ENV['ROOT_PROJECT'], -1);

        if (in_array($last_char, ['/', '\\'])) {
            # code...
            $_ENV['ROOT_PROJECT'] = substr_replace($_ENV['ROOT_PROJECT'], "", -1);
        }

        if (!isset($_ENV['DEBUG_MODE'])) {
            # code...
            throw new ApiException('You must define DEBUG_MODE environment variable env with value "true" or "false"');
        }

        if (is_bool($_ENV['DEBUG_MODE'])) {
            # code...
            return;
        }

        if (!is_string($_ENV['DEBUG_MODE'])) {
            # code...
            throw new ApiException('Invalid format in DEBUG_MODE environment variable, valid values "true" or "false"');
        }

        $debug_mode = strtolower($_ENV['DEBUG_MODE']);
        if ($debug_mode === 'true') {
            # code...
            $_ENV['DEBUG_MODE'] = true;
        } elseif ($debug_mode === 'false') {
            # code...
            $_ENV['DEBUG_MODE'] = false;
        } else {
            throw new ApiException('Invalid value in DEBUG_MODE environment variable, only valid values "true" or "false"');
        }
    }

    /**
     * 
     */
    public function send(): void
    {
        # code...
        try {
            //code...
            $route_info = Router::getRouteInfo();
            $path_params = $route_info['path_params'];
            $handler = $route_info['handler'];

            if (is_callable($handler) || !preg_match('/@/', $handler)) {
                # code...
                $response = call_user_func_array($handler, array_values($path_params));
            } else {

                list($class, $method) = explode('@', $handler, 2);
                $response = call_user_func_array([new $class, $method], array_values($path_params));
            }

            if (!($response instanceof Response)) {
                # code...
                throw new ApiException("Invalid response format");
            }

            $response->returnData();
        } catch (HttpException $e) {
            $response = new Response('json', $e->getMessage(), $e->getCode());
            $response->returnData();
        } catch (Throwable $e) {
            //throw $th;
            if ($_ENV['DEBUG_MODE']) {
                # code...
                throw $e;
            }

            $this->saveLog($e);
            $response = new Response('json', 'Internal Server Error', 500);
            $response->returnData();
        }
    }

    /**
     * 
     */
    private function saveLog($exception): void
    {
        # code...

        $log_dir = $_ENV['ROOT_PROJECT'] . '/logs/';

        if (!is_dir($log_dir)) {
            # create directory 
            mkdir($log_dir);
        }

        $data = [
            'DATE' => date('Y-m-d H:i:s'),
            'ENDPOINT'  => $_SERVER['REQUEST_URI'] ?? '',
            'METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
            'MESSAGE_ERROR' => $exception->getMessage(),
            'TRACE' => $exception->getTrace()[0]
        ];

        $log_file = $log_dir . 'logs-' . date('Y-m-d') . '.log';

        file_put_contents($log_file, json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }
}
