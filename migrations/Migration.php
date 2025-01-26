<?php

namespace Migrations;

class Migration
{
    public static function turnOnTestMode()
    {
        define('PHPUNIT_INTEGRATION_TESTSUITE', true);
    }
}