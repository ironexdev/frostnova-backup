<?php

namespace App\Enum;

use ReflectionClass;

trait EnumTrait
{
    public static function casesToArray(): array
    {
        $array = [];

        foreach(static::cases() as $const)
        {
            $array[] = $const->value;
        }

        return $array;
    }

    public static function constantsToArray(): array
    {
        $array = [];

        $constants = (new ReflectionClass(static::class))->getConstants();

        foreach($constants as $const)
        {
            $array[] = $const;
        }

        return $array;
    }
}
