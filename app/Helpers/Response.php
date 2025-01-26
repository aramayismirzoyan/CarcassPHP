<?php

namespace App\Helpers;

class Response
{
    private static function setJsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function send($data): string
    {
        self::setJsonHeader();
        http_response_code(200);
        return json_encode($data);
    }

    public static function sendValidatorError($data): string
    {
        self::setJsonHeader();
        http_response_code(422);
        return json_encode($data);
    }

    public static function sendNotFoundError(): string
    {
        self::setJsonHeader();
        http_response_code(404);
        return json_encode([
            'success' => false,
            'result' => [
                'error' => "Not found"
            ]
        ]);
    }

    public static function sendServerError(): string
    {
        self::setJsonHeader();
        http_response_code(500);
        return json_encode([
            'success' => false,
            'result' => [
                'error' => "Some problems with server"
            ]
        ]);
    }
}
