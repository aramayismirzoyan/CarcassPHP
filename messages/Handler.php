<?php

namespace Messages;

class Handler
{
    private static Handler $object;

    private array $lang;

    final private function __construct($messagesDomain)
    {
        $this->lang = include_once(__DIR__ . "/en/{$messagesDomain}.php");
    }

    public static function get($messagesDomain)
    {
        if (!isset(self::$object)) {
            self::$object = new Handler($messagesDomain);
        }

        return self::$object->lang;
    }
}
