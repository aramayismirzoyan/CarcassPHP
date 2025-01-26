<?php

namespace App\Helpers;

use App\Enums\ValidatorMessages;

class Validator
{
    private array $errors = [];

    private array $errorCache = [];

    public function __construct(
        private array $params
    ) {
    }

    private function hasField($field): bool
    {
        if (isset($this->params[$field])) {
            return ! empty($this->params[$field]);
        }

        return false;
    }

    private function addError($field, $error, $ruleName): void
    {
        $key = $this->hasErrorInCache($field, $ruleName);

        if ($key !== false) {
            $this->errors[$field][$key] = $error;
        } else {
            $this->errors[$field][] = $error;

            $lastKey = array_key_last($this->errors[$field]);
            $this->cacheErrorMessageByRule($field, $ruleName, $lastKey);
        }
    }

    private function cacheErrorMessageByRule($field, $ruleName, $keyInArray)
    {
        $this->errorCache[$field][$ruleName] = $keyInArray;
    }

    private function hasErrorInCache($field, $ruleName): int|false
    {
        return $this->errorCache[$field][$ruleName] ?? false;
    }

    public function setRequired($field): void
    {
        if (!$this->hasField($field)) {
            $this->addError($field, ValidatorMessages::REQUIRED->toString(), 'required');
        }
    }

    public function setLength($field, $length): void
    {
        if (!$this->hasField($field)) {
            return;
        }

        if (strlen($this->params[$field]) > $length) {
            $this->addError($field, ValidatorMessages::LENGTH->getLength($length), 'length');
        }
    }

    public function setAsInt($field): void
    {
        if (!$this->hasField($field)) {
            return;
        }

        if (!is_int($this->params[$field])) {
            $this->addError($field, ValidatorMessages::INT->toString(), 'int');
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        return empty($this->errors);
    }

    public static function hasAtLeastOneInData(array $data, array $fields): bool
    {
        $fields = array_fill_keys($fields, '');
        $common = array_intersect_key($fields, $data);

        return !empty($common);
    }
}
