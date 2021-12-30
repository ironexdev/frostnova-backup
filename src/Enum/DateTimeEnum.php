<?php declare(strict_types=1);

namespace App\Enum;

enum DateTimeEnum: int
{
    use EnumTrait;

    public const MINUTE = 60;
    public const HOUR = self::MINUTE * 60;
    public const DAY = self::HOUR * 24;
    public const WEEK = self::DAY * 7;
}
