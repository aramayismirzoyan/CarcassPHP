<?php

namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function __construct(
        private array $params
    )
    {

    }

    private function hasField($field): bool
    {
        if(isset($this->params[$field])) {
            return ! empty($this->params[$field]);
        }

        return false;
    }

    private function addError($field, $error): void
    {
        $this->errors[$field][] = $error;
    }

    public function setRequired($field): void
    {
        if(!$this->hasField($field)) {
            $this->addError($field, 'This field is required');
        }
    }

    public function setLength($field, $length): void
    {
        if(!$this->hasField($field)) return;

        if(strlen($this->params[$field]) > $length) {
            $this->addError($field, "Length no more $length symbols");
        }
    }

    public function setAsInt($field) :void
    {
        if(!$this->hasField($field)) return;

        if(!is_int($this->params[$field])) {
            $this->addError($field, "This field must be integer");
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public  function validate(): bool
    {
        return empty($this->errors);
    }

    public static function hasAtLeastInData($data, $fields): bool
    {
        $fields = array_fill_keys($fields, '');
        $common = array_intersect_key($fields, $data);

        return !empty($common);
    }
}