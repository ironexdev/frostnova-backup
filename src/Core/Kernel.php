<?php declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Enum\ResponseStatusCodeEnum;

class Kernel
{
    public function __construct(
        ResponseInterface                $defaultResponse,
        Router                           $router,
        ServerRequestInterface           $serverRequest
    )
    {
        Session::start();
        Session::regenerate();

        $response = $this->processRequest($defaultResponse, $router, $serverRequest);

        $this->sendResponse($response->getStatusCode(), $response->getHeaders(), $response->getBody());
    }

    private function processRequest(
        ResponseInterface        $defaultResponse,
        Router                   $router,
        ServerRequestInterface   $serverRequest): ResponseInterface
    {
        $middlewareStack = new MiddlewareStack(
            $defaultResponse->withStatus(ResponseStatusCodeEnum::NOT_FOUND), // default/fallback response
            // ...middlewares
            $router
        );

        return $middlewareStack->handle($serverRequest);
    }

    /**
     * @param int $code
     * @param array $headers
     * @param StreamInterface $body
     */
    private function sendResponse(int $code, array $headers, StreamInterface $body)
    {
        http_response_code($code);

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header(sprintf("%s: %s", $name, $value), false);
            }
        }

        echo $body;
    }
}
