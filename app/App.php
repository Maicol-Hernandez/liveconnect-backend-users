<?php

namespace App;

use Throwable;
use App\Router;
use App\Response;
use App\Exceptions\ApiException;
use App\Exceptions\HttpException;
use App\Exceptions\ValidationException;

class App
{
    public function __construct()
    {
        $this->checkEnv();
    }

    private function checkEnv(): void
    {
        if (empty($_ENV['ROOT_PROJECT'])) {
            throw new ApiException("You must define ROOT_PROJECT environment variable with root path of the project");
        }

        if (!is_dir($_ENV['ROOT_PROJECT'])) {
            throw new ApiException("ROOT_PROJECT environment variable dir not exists");
        }

        $last_char = substr($_ENV['ROOT_PROJECT'], -1);

        if (in_array($last_char, ['/', '\\'])) {
            $_ENV['ROOT_PROJECT'] = substr_replace($_ENV['ROOT_PROJECT'], "", -1);
        }

        if (!isset($_ENV['DEBUG_MODE'])) {
            throw new ApiException('You must define DEBUG_MODE environment variable env with value "true" or "false"');
        }

        if (is_bool($_ENV['DEBUG_MODE'])) {
            return;
        }

        if (!is_string($_ENV['DEBUG_MODE'])) {
            throw new ApiException('Invalid format in DEBUG_MODE environment variable, valid values "true" or "false"');
        }

        $debug_mode = strtolower($_ENV['DEBUG_MODE']);
        if ($debug_mode === 'true') {
            $_ENV['DEBUG_MODE'] = true;
        } elseif ($debug_mode === 'false') {
            $_ENV['DEBUG_MODE'] = false;
        } else {
            throw new ApiException('Invalid value in DEBUG_MODE environment variable, only valid values "true" or "false"');
        }
    }

    public function send(): void
    {
        try {
            $route_info = Router::getRouteInfo();
            $path_params = $route_info['path_params'];
            $handler = $route_info['handler'];

            if (is_callable($handler) || !preg_match('/@/', $handler)) {
                $response = call_user_func_array($handler, array_values($path_params));
            } else {

                list($class, $method) = explode('@', $handler, 2);
                $response = call_user_func_array([new $class, $method], array_values($path_params));
            }

            if (!($response instanceof Response)) {
                throw new ApiException("Invalid response format");
            }

            $response->returnData();
        } catch (HttpException $e) {
            $response = new Response('json', $e->getMessage(), $e->getCode());
            $response->returnData();
        } catch (ValidationException $e) {
            $response = new Response(
                'json',
                ["message" => $e->getMessage(), 'errors' => $e->getErrors()],
                $e->getCode()
            );
            $response->returnData();
        } catch (Throwable $e) {
            if ($_ENV['DEBUG_MODE']) {
                throw $e;
            }

            saveLog($e);
            $response = new Response('json', 'Internal Server Error', 500);
            $response->returnData();
        }
    }
}
