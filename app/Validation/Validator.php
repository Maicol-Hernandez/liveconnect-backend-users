<?php

namespace App\Validation;

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
                if ($value === null || $value === '') {
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
        }
    }
}
