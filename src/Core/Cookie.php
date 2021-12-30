<?php declare(strict_types=1);

namespace App\Core;

class Cookie
{
    public static function export(): array
    {
        return $_COOKIE;
    }
}
