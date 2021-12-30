<?php

use App\Api\Base\IndexController;
use App\Enum\RequestMethodEnum;

return [
    "/" => [
        RequestMethodEnum::GET => [
            "handler" => IndexController::class . "::read"
        ]
    ],
];
