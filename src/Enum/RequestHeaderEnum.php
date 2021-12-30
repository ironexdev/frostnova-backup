<?php declare(strict_types=1);

namespace App\Enum;

enum RequestHeaderEnum: string
{
    use EnumTrait;
    const CONTENT_TYPE = "Content-Type";
}
