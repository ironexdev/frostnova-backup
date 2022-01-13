<?php

use App\Api\Base\Home\HomeController;
use App\Enum\RequestMethodEnum;

return [
    "/" => [
        RequestMethodEnum::GET => [
            "handler" => HomeController::class . "::default"
        ]
    ]
];
