<?php

namespace App\Validators;

use App\Helpers\Request;
use App\Helpers\Validator;

class UpdateUserValidator implements ValidatorFactory
{
    public static function create(array $data): Validator
    {
        $validator = new Validator([
            'full_name' => (new Request())->getParam($data, 'full_name'),
            'role' => (new Request())->getParam($data, 'role'),
            'efficiency' => (new Request())->getParam($data, 'efficiency'),
        ]);

        $validator->setLength('full_name', 100);
        $validator->setLength('role', 100);
        $validator->setAsInt('efficiency');

        return $validator;
    }

}
