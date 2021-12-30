<?php declare(strict_types=1);

namespace App\Exception\Http;

class ForbiddenException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 403;
}
