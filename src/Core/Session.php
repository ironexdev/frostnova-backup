<?php declare(strict_types=1);

namespace App\Core;

use App\Enum\EnvironmentEnum;
use JetBrains\PhpStorm\ArrayShape;

class Session
{
    private static string $expiration = "expiration";

    public static function start()
    {
        if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::DEVELOPMENT) {
            ini_set("session.cookie_secure", "Off");
        }

        session_start();
    }

    public static function destroy()
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", -1,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );

        session_unset();
        session_destroy();
    }

    #[ArrayShape(["id" => "false|string", "data" => "array"])]
    public static function export(): array
    {
        return [
            "id" => session_id(),
            "data" => $_SESSION
        ];
    }

    public static function regenerate()
    {
        static::setExpiration(Utils::expiration(60));

        session_regenerate_id();

        static::unsetExpiration();
    }

    public static function setExpiration(int $expiration): void
    {
        $_SESSION["expiration"] = $expiration;
    }

    public static function unsetExpiration()
    {
        unset($_SESSION[static::$expiration]);
    }
}
