<?php declare(strict_types=1);

namespace App\Enum;

enum ResponseHeaderEnum: string
{
    use EnumTrait;
    const CONTENT_LENGTH = "Content-Length";
    const CONTENT_TYPE = "Content-Type";
}
