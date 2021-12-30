<?php declare(strict_types=1);

use App\Enum\ContentTypeEnum;
use App\Enum\RequestHeaderEnum;
use App\Enum\ResponseStatusCodeEnum;
use Psr\Log\LoggerInterface;
use App\Enum\EnvironmentEnum;
use App\Core\Kernel;
use DI\ContainerBuilder;

if ($_ENV["ERROR_REPORTING"] === "true") {
    error_reporting(E_ALL);
    ini_set("display_errors", "On");
}

if ($_ENV["FORCE_HTTPS"] === "true") {
    if (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] === "off") {
        echo "Website can only be accessed via HTTPS protocol";
        exit;
    }
}

const APP_DIRECTORY = __DIR__;

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

date_default_timezone_set("UTC");

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config-di.php");
if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::PRODUCTION) {
    $containerBuilder->enableCompilation(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "php-di");
}

$container = $containerBuilder->build();

try {
    $container->make(Kernel::class);
} catch (Throwable $e) {
    $errorCode = $e->getCode() ?: ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR;

    $logger = $container->get(LoggerInterface::class);
    $logger->error($e->getMessage(), $e->getTrace());

    if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::DEVELOPMENT) {
        if ($errorCode >= ResponseStatusCodeEnum::BAD_REQUEST && $errorCode < ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR) {
            errorResponse($errorCode, $e->getMessage());
        } else {
            throw $e;
        }
    } else {
        if ($errorCode >= ResponseStatusCodeEnum::BAD_REQUEST && $errorCode < ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR) {
            errorResponse($errorCode, $e->getMessage());
        } else {
            errorResponse(ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }
}

function errorResponse(int $code, string $message = null)
{
    if ($message) {
        if($_SERVER["CONTENT_TYPE"] === ContentTypeEnum::JSON)
        {
            http_response_code($code); // Show any >= 400 && < 500 error code to client (which should hide it from end-user)
            header(RequestHeaderEnum::CONTENT_TYPE . ":" . ContentTypeEnum::JSON);
            echo json_encode([
                "errors" => [
                    "message" => $message
                ]
            ]);
        }
        else
        {
            http_response_code(ResponseStatusCodeEnum::NOT_FOUND); // Only show error 404 to end-user
            header(RequestHeaderEnum::CONTENT_TYPE . ":" . ContentTypeEnum::HTML);

            echo json_encode([
                "errors" => [
                    "message" => $message
                ]
            ]);
        }
    }
}
