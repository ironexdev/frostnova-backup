<?php declare(strict_types=1);

namespace App\Exception\Http;

class NotFoundException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 404;
}
