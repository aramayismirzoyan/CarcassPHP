<?php

namespace Test\Integration;

use App\Validators\NewUserValidator;
use PHPUnit\Framework\TestCase;
use Test\Helpers\StringGenerator;

class NewUserValidatorTest extends TestCase
{
    public function test_new_user_rules()
    {
        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = NewUserValidator::create($data);

        $this->assertTrue($validator->validate());
    }

    public function test_new_user_rules_when_is_given_wrong_data()
    {
        $data = [
            'full_name' => StringGenerator::simpleText(101),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(101),
            'efficiency' => 10
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => '10'
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());
    }

    public function test_new_user_rules_when_absent_required_field()
    {
        $data = [
            'role' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(100)
        ];

        $validator = NewUserValidator::create($data);

        $this->assertFalse($validator->validate());
    }
}
