<?php

namespace App\Api\Base;

use App\Enum\ContentTypeEnum;
use App\Enum\RequestHeaderEnum;
use App\Enum\ResponseHeaderEnum;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class AbstractController
{
    public function __construct(
        private StreamFactoryInterface $streamFactory
    )
    {
    }

    protected function response(
        object            $parameters,
        RequestInterface  $request,
        ResponseInterface $response
    ): ResponseInterface
    {
        if (!$response->getHeaderLine(ResponseHeaderEnum::CONTENT_TYPE)) {
            $response = $response->withHeader(
                ResponseHeaderEnum::CONTENT_TYPE,
                $request->getHeaderLine(RequestHeaderEnum::ACCEPT) ?? ContentTypeEnum::JSON
            );
        }

        $responseContentType = $response->getHeaderLine(ResponseHeaderEnum::CONTENT_TYPE);

        if ($responseContentType === ContentTypeEnum::HTML) {
            $responseBody = $this->streamFactory->createStream(
                $this->html($parameters)
            );
        } else {
            $responseBody = $this->streamFactory->createStream(
                $this->json($parameters)
            );
        }

        return $response->withBody($responseBody);
    }

    private function html(object $parameters): string
    {
        return "<html lang='en'><head><title>Frostnova</title></head><body>" . var_export($parameters, true) . "</body></html>";
    }

    private function json(object $parameters): string
    {
        return json_encode($parameters);
    }
}
