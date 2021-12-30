<?php

namespace App\Api\Base;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexController extends AbstractController
{
    public function read(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->htmlResponse("Hello World", $response);

        // return $this->jsonResponse((object) ["Hello" => "World"], $response);
    }
}
