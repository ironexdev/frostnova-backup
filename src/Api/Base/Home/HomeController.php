<?php

namespace Frostnova\Api\Base\Home;

use Frostnova\Api\Base\AbstractController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function default(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->response((object) [], $response);
    }
}
