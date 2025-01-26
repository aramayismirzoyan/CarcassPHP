<?php

namespace App\Validators;

use App\Helpers\Validator;

interface ValidatorFactory
{
    public static function create(array $data): Validator;
}
