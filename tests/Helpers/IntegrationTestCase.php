<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use Providers\PDOProvider;

class IntegrationTestCase extends TestCase
{
    public function setUp(): void
    {
        PDOProvider::create()->truncateTable('users');
    }

    public function tearDown(): void
    {
        PDOProvider::create()->truncateTable('users');
    }
}
