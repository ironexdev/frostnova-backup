<?php declare(strict_types=1);

namespace App\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements MiddlewareInterface
{
    public function __construct(private ContainerInterface $container, private ResponseFactoryInterface $responseFactory, private array $routes)
    {
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $resolver = $this->routes[$request->getUri()->getPath()][$request->getMethod()]["handler"] ?? null;

        if(!$resolver)
        {
            return $handler->handle($request);
        }

        list($controller, $method) = explode("::", $resolver);

        return $this->container->call([$controller, $method], ["request" => $request, "response" => $response]);
    }
}
