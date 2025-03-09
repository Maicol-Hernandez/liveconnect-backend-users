<?php

namespace App;

use stdClass;

class Request
{
    private array $data = [];
    private array $files = [];
    private array $headers = [];
    private ?stdClass $authUser = null;

    public function __construct()
    {
        $this->files = $_FILES;
        $this->headers = getallheaders();
        $this->data = $this->mergeInputs();
    }

    /**
     * Merge all input sources (GET, POST, JSON)
     * 
     * @return array
     */
    private function mergeInputs(): array
    {
        $input = [];

        // Process POST data
        if (!empty($_POST)) {
            $input = $_POST;
        }

        // Process JSON data if content-type is application/json
        if ($this->isJson()) {
            $jsonData = $this->parseJsonInput();
            if (is_array($jsonData)) {
                $input = array_merge($input, $jsonData);
            }
        }

        // Add GET parameters last (lower priority than POST/JSON)
        return array_merge($input, $_GET);
    }

    /**
     * Parse JSON input safely
     * 
     * @return array|null
     */
    private function parseJsonInput(): ?array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return null;
        }

        $data = json_decode($input, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
    }

    /**
     * Get all request data
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Get a specific input value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Alias for input()
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->input($key, $default);
    }

    /**
     * Get a file from the request
     * 
     * @param string $key
     * @return array|null
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if input exists
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Check if request has any of the specified keys
     * 
     * @param array $keys
     * @return bool
     */
    public function hasAny(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if content type is JSON
     * 
     * @return bool
     */
    public function isJson(): bool
    {
        $contentType = $this->header('Content-Type');
        return $contentType && strpos($contentType, 'application/json') !== false;
    }

    /**
     * Get a header value
     * 
     * @param string $headerName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function header(string $headerName, $defaultValue = null): mixed
    {
        return $this->headers[$headerName] ?? $defaultValue;
    }

    /**
     * Get Authorization Bearer token if present
     * 
     * @return string|null
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization', '');
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return null;
    }

    /**
     * Set authenticated user data
     * 
     * @param stdClass $user
     * @return void
     */
    public function setAuthUser(stdClass $user): void
    {
        $this->authUser = $user;
    }

    /**
     * Get authenticated user data
     * 
     * @return stdClass|null
     */
    public function authUser(): ?stdClass
    {
        return $this->authUser;
    }

    /**
     * Get authenticated user ID
     * 
     * @return int|null
     */
    public function userId(): ?int
    {
        return $this->authUser->id ?? null;
    }

    /**
     * Get request method (GET, POST, etc.)
     * 
     * @return string
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if request is using a specific method
     * 
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($this->method()) === strtoupper($method);
    }

    /**
     * Get the current request URL
     * 
     * @return string
     */
    public function url(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get request IP address
     * 
     * @return string
     */
    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
