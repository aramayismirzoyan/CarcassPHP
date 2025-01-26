<?php

namespace App\Enums;

use Messages\Handler;

enum ValidatorMessages
{
    case REQUIRED;
    case INT;
    case LENGTH;

    public function toString(): string
    {
        return match ($this) {
            self::REQUIRED => Handler::get('validator')['required'],
            self::INT => Handler::get('validator')['integer'],
            default => ''
        };
    }

    public function getLength($length): string
    {
        if ($this === self::LENGTH) {
            $end = $length > 1 ? Handler::get('validator')['length_end_plural'] : Handler::get('validator')['length_end'];

            return Handler::get('validator')['length_start'] . ' ' . $length . ' ' . $end;
        }

        return '';
    }
}
