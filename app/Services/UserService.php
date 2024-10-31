<?php

namespace App\Services;

use App\Helpers\Request;
use App\Helpers\Validator;

class UserService
{
    public function getNewUserValidator($data) :Validator
    {
        $validator = new Validator([
            'full_name' => Request::getParam($data, 'full_name'),
            'role' => Request::getParam($data, 'role'),
            'efficiency' => Request::getParam($data, 'efficiency'),
        ]);

        $validator->setRequired('full_name');
        $validator->setRequired('role');
        $validator->setRequired('efficiency');

        $validator->setLength('full_name', 100);
        $validator->setLength('role', 100);
        $validator->setAsInt('efficiency');

        return $validator;
    }

    public function getUpdateUserValidator($data) :Validator
    {
        $validator = new Validator([
            'full_name' => Request::getParam($data, 'full_name'),
            'role' => Request::getParam($data, 'role'),
            'efficiency' => Request::getParam($data, 'efficiency'),
        ]);

        $validator->setLength('full_name', 100);
        $validator->setLength('role', 100);
        $validator->setAsInt('efficiency');

        return $validator;
    }
}