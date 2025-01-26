<?php

namespace Test\Unit\Framework;

use App\Enums\ValidatorMessages;
use App\Helpers\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function test_validate_when_field_is_required()
    {
        $validator = new Validator([
                'name' => 'John'
        ]);

        $validator->setRequired('name');

        $this->assertTrue($validator->validate());
    }

    public function test_validate_is_false_when_field_does_not_exist()
    {
        $validator = new Validator([]);

        $validator->setRequired('name');

        $this->assertFalse($validator->validate());
    }

    public function test_validate_when_field_has_a_valid_length()
    {
        $validator = new Validator([
            'name' => 'John'
        ]);

        $validator->setLength('name', 5);

        $this->assertTrue($validator->validate());
    }

    public function test_validate_is_false_when_field_length_is_invalid()
    {
        $validator = new Validator([
            'name' => 'John'
        ]);

        $validator->setLength('name', 3);

        $this->assertFalse($validator->validate());
    }

    public function test_validate_when_field_is_int()
    {
        $validator = new Validator([
            'count' => 5
        ]);

        $validator->setAsInt('count');

        $this->assertTrue($validator->validate());
    }

    public function test_validate_is_false_when_number_sringable()
    {
        $validator = new Validator([
            'count' => '5'
        ]);

        $validator->setAsInt('count');

        $this->assertFalse($validator->validate());
    }

    public function test_validate_is_false_when_field_is_string()
    {
        $validator = new Validator([
            'count' => 'John'
        ]);

        $validator->setAsInt('count');

        $this->assertFalse($validator->validate());
    }

    public function test_getErrors_when_field_is_required()
    {
        $validator = new Validator([]);

        $validator->setRequired('name');

        $errors = $validator->getErrors();

        $this->assertEquals($errors['name'][0], ValidatorMessages::REQUIRED->toString());
    }

    public function test_getErrors_when_field_is_not_valid_length()
    {
        $validator = new Validator([
            'name' => 'John'
        ]);

        $length = 3;

        $validator->setLength('name', $length);

        $errors = $validator->getErrors();

        $this->assertEquals($errors['name'][0], ValidatorMessages::LENGTH->getLength($length));
    }

    public function test_getErrors_when_field_is_not_valid_int()
    {
        $validator = new Validator([
            'count' => 'John'
        ]);

        $validator->setAsInt('count');

        $errors = $validator->getErrors();

        $this->assertEquals($errors['count'][0], ValidatorMessages::INT->toString());
    }

    public function test_getErrors_rule_replace_error_when_set_twice()
    {
        $validator = new Validator([]);

        $validator->setRequired('name');
        $validator->setRequired('name');

        $errors = $validator->getErrors();

        $this->assertEquals($errors['name'], [ValidatorMessages::REQUIRED->toString()]);
    }

    public function test_hasAtLeastOneInData_method()
    {
        $data = [
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ];

        $fields = ['full_name', 'role', 'efficiency'];

        $hasFields = Validator::hasAtLeastOneInData($data, $fields);

        $this->assertTrue($hasFields);

        $hasFields = Validator::hasAtLeastOneInData($data, []);

        $this->assertFalse($hasFields);

        unset($data['full_name']);

        $hasFields = Validator::hasAtLeastOneInData($data, $fields);

        $this->assertTrue($hasFields);

        $data['type'] = 'user';

        $hasFields = Validator::hasAtLeastOneInData($data, $fields);

        $this->assertTrue($hasFields);

        unset($data['role']);
        unset($data['efficiency']);

        $hasFields = Validator::hasAtLeastOneInData($data, $fields);

        $this->assertFalse($hasFields);
    }

}
