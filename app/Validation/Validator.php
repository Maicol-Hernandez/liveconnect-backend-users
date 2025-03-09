<?php

namespace App\Validation;

use App\Models\User;
use App\Exceptions\ValidationException;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): void
    {
        foreach ($this->rules as $field => $validations) {
            foreach ($validations as $validation) {
                $this->applyValidation($field, $validation);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException(
                'Validation errors: ' . implode(', ', $this->errors),
                422,
                $this->errors
            );
        }
    }

    private function applyValidation(string $field, string $validation): void
    {
        $value = $this->data[$field] ?? null;

        switch ($validation) {
            case 'required':
                if (is_null($value) || empty($value)) {
                    $this->errors[] = "Field {$field} is required";
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = "Invalid email format for {$field}";
                }
                break;

            case 'password':
                if (strlen($value ?? "") < 6 || strlen($value ?? "") > 64) {
                    $this->errors[] = "Password must be between 6 and 64 characters";
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    $this->errors[] = "Field {$field} must be an array";
                }
                break;
            case 'exists':
                if (!User::existsByEmail($value)) {
                    $this->errors[] = "{$field} does not exist";
                }
                break;
            case 'unique':
                if (User::existsByEmail($value)) {
                    $this->errors[] = "{$field} already exists";
                }
                break;
            case 'confirmed':
                if ($value !== $this->data["{$field}_confirmation"] ?? null) {
                    $this->errors[] = 'Passwords do not match';
                }
        }
    }
}
