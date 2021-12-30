<?php declare(strict_types=1);

namespace App\Enum;

enum ContentTypeEnum: string
{
    use EnumTrait;

    const HTML = "text/html";
    const JSON = "application/json";
}
