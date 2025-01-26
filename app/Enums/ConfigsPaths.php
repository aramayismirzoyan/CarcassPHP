<?php

namespace App\Enums;

enum ConfigsPaths
{
    case DB;
    case ROUTES;

    public function get(): string
    {
        $rootPath = dirname(dirname(__DIR__));
        return match ($this) {
            self::DB => $rootPath . DIRECTORY_SEPARATOR. 'config' . DIRECTORY_SEPARATOR . 'db.php',
            self::ROUTES => $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php',
            default => ''
        };
    }

}
