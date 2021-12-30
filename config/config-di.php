<?php

use Monolog\Formatter\JsonFormatter;
use Monolog\Logger as MonologLogger;
use App\Enum\ContentTypeEnum;
use App\Logger\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use App\Core\Router;
use Monolog\Handler\StreamHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

const DS = DIRECTORY_SEPARATOR;

return [
    /* Custom Interfaces *****************************************************/
    /*************************************************************************/

    // Implements PSR-15
    Router::class => DI\factory(function (ContainerInterface $container, ResponseFactoryInterface $responseFactory) {
        $routes = require_once(APP_DIRECTORY . DS . ".." . DS . "config" . DS . "api" . DS . "base" . DS . "routes.php");
        return new Router($container, $responseFactory, $routes);
    }),

    // PSR interfaces ********************************************************/
    /*************************************************************************/

    // PSR-3
    LoggerInterface::class => DI\factory(function () {
        $logger = new Logger("debug");
        $fileHandler = new StreamHandler($_ENV["DEBUG_LOG"], MonologLogger::DEBUG);
        $formatter = new JsonFormatter();
        $formatter->includeStacktraces();
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        return $logger;
    }),

    // PSR-7
    ResponseInterface::class => DI\factory(function (ResponseFactoryInterface $responseFactory) {
        return $responseFactory->createResponse();
    }),
    ResponseFactoryInterface::class => DI\autowire(Psr17Factory::class),

    // PSR-15
    ServerRequestInterface::class => DI\factory(function (Psr17Factory $psr17Factory) {
        $creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        $serverRequest = $creator->fromGlobals();

        $contentType = $serverRequest->getHeaderLine("Content-Type");

        // Parse request body, because Nyholm\Psr7Server doesn't parse JSON requests
        if ($contentType === ContentTypeEnum::JSON) {
            if (!$serverRequest->getParsedBody()) {
                $content = $serverRequest->getBody()->getContents();
                $data = json_decode($content, true);

                if ($data === false || json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException(json_last_error_msg() . " in body: '" . $content . "'");
                }

                $serverRequest = $serverRequest->withParsedBody($data);
            }
        }

        return $serverRequest;
    }),
    StreamFactoryInterface::class => DI\autowire(Psr17Factory::class),
];
