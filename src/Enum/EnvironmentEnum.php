<?php declare(strict_types=1);

namespace App\Enum;

enum EnvironmentEnum: string
{
    use EnumTrait;

    const DEVELOPMENT = "development";
    const TEST = "test";
    const PRODUCTION = "production";
}
