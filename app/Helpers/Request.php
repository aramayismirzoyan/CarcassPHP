<?php

namespace App\Helpers;

class Request
{
    public static function getParam($data, $key)
    {
        return $data[$key] ?? '';
    }

    public static function getJson(): array
    {
        $json = file_get_contents('php://input');

        if($json !== '') {
            return json_decode(file_get_contents('php://input'), true);
        }

        return [];
    }

    public static function isValidType($type): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $type;
    }
}