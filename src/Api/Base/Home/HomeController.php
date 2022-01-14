<?php

namespace App\Api\Base\Home;

use App\Api\Base\AbstractController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function default(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // $response = $response->withHeader("Content-Type", "text/html");

        return $this->response((object) [
            "Frost" => "Nova"
        ], $request, $response);
    }
}
