<?php

namespace Test\Unit;

use App\Validators\UpdateUserValidator;
use PHPUnit\Framework\TestCase;
use Test\Helpers\StringGenerator;

class UpdateUserValidatorTest extends TestCase
{
    public function test_update_user_rules()
    {
        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = UpdateUserValidator::create($data);

        $this->assertTrue($validator->validate());
    }

    public function test_update_user_rules_when_is_given_wrong_data()
    {
        $data = [
            'full_name' => StringGenerator::simpleText(101),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => 10
        ];

        $validator = UpdateUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(101),
            'efficiency' => 10
        ];

        $validator = UpdateUserValidator::create($data);

        $this->assertFalse($validator->validate());

        $data = [
            'full_name' => StringGenerator::simpleText(100),
            'role' => StringGenerator::simpleText(100),
            'efficiency' => '10'
        ];

        $validator = UpdateUserValidator::create($data);

        $this->assertFalse($validator->validate());
    }
}
