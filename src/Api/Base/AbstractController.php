<?php

namespace App\Api\Base;

use App\Enum\ContentTypeEnum;
use App\Enum\ResponseHeaderEnum;
use App\Enum\ResponseStatusCodeEnum;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class AbstractController
{
    public function __construct(private StreamFactoryInterface $streamFactory)
    {}

    protected function htmlResponse(
        string            $html,
        ResponseInterface $response,
        int               $status = ResponseStatusCodeEnum::OK,
        array             $headers = []
    ): ResponseInterface
    {
        foreach ($headers as $key => $value) {
            $response->withHeader($key, $value);
        }

        $responseBody = $this->streamFactory->createStream($html);

        return $response
            ->withStatus($status)
            ->withHeader(ResponseHeaderEnum::CONTENT_TYPE, ContentTypeEnum::HTML)
            ->withBody($responseBody);
    }

    protected function jsonResponse(
        object            $parameters,
        ResponseInterface $response,
        int               $status = ResponseStatusCodeEnum::OK,
        array             $headers = []
    ): ResponseInterface
    {
        foreach ($headers as $key => $value) {
            $response->withHeader($key, $value);
        }

        $responseBody = $this->streamFactory->createStream(json_encode($parameters));

        return $response
            ->withStatus($status)
            ->withHeader(ResponseHeaderEnum::CONTENT_TYPE, ContentTypeEnum::JSON)
            ->withBody($responseBody);
    }
}
