<?php

use App\Core\MiddlewareStack\MiddlewareStack;
use App\Enum\ContentTypeEnum;
use App\Enum\RequestMethodEnum;
use App\Enum\ResponseHeaderEnum;
use App\Enum\ResponseStatusCodeEnum;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use App\Core\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tuupola\Middleware\CorsMiddleware;

const DS = DIRECTORY_SEPARATOR;

return [
    /* Custom ****************************************************************/
    /*************************************************************************/
    // Implements PSR-15
    Router::class => DI\factory(function (ContainerInterface $container, ResponseFactoryInterface $responseFactory) {
        $routes = require_once(APP_DIRECTORY . DS . ".." . DS . "config" . DS . "api" . DS . "base" . DS . "routes.php");
        return new Router($container, $responseFactory, $routes);
    }),

    // Implements PSR-15
    RequestHandlerInterface::class => DI\factory(function (
        ResponseInterface        $defaultResponse,
        CorsMiddleware           $corsMiddleware,
        Router                   $router
    ): RequestHandlerInterface {
        return new MiddlewareStack(
            $defaultResponse->withStatus(ResponseStatusCodeEnum::NOT_FOUND), // Default/fallback response
            $corsMiddleware, // Add CORS headers to the response if needed
            // Add other middlewares
            $router
        );
    }),

    /* 3rd Party *************************************************************/
    /*************************************************************************/
    // Tuupola, CORS
    CorsMiddleware::class => DI\factory(function () {

        return new CorsMiddleware([
            "origin" => [ // Access-Control-Allow-Origin
                "http://client.local"
            ],
            "methods" => [ // Access-Control-Allow-Methods
                RequestMethodEnum::DELETE,
                RequestMethodEnum::GET,
                RequestMethodEnum::HEAD,
                RequestMethodEnum::OPTIONS,
                RequestMethodEnum::PATCH,
                RequestMethodEnum::POST,
                RequestMethodEnum::PUT
            ],
            "headers.allow" => [ // Access-Control-Allow-Headers

            ],
            "headers.expose" => [ // Access-Control-Expose-Headers

            ],
            "credentials" => true, // Access-Control-Allow-Credentials
            "cache" => 0
        ]);
    }),

    // PSR interfaces ********************************************************/
    /*************************************************************************/

    // PSR-7
    ResponseInterface::class => DI\factory(function (ResponseFactoryInterface $responseFactory) {
        return $responseFactory->createResponse();
    }),
    ResponseFactoryInterface::class => DI\autowire(Psr17Factory::class),

    // PSR-15
    ServerRequestInterface::class => DI\factory(function (
        ServerRequestFactoryInterface $serverRequestFactory,
        StreamFactoryInterface        $streamFactory,
        UploadedFileFactoryInterface  $uploadedFileFactory,
        UriFactoryInterface           $uriFactory
    ) {
        $creator = new ServerRequestCreator(
            $serverRequestFactory,
            $uriFactory,
            $uploadedFileFactory,
            $streamFactory
        );

        $serverRequest = $creator->fromGlobals();

        $contentType = $serverRequest->getHeaderLine(ResponseHeaderEnum::CONTENT_TYPE);

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

    // PSR-17
    ServerRequestFactoryInterface::class => DI\autowire(Psr17Factory::class),
    StreamFactoryInterface::class => DI\autowire(Psr17Factory::class),
    UploadedFileFactoryInterface::class => DI\autowire(Psr17Factory::class),
    UriFactoryInterface::class => DI\autowire(Psr17Factory::class)
];
