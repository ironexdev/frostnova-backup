<?php declare(strict_types=1);

namespace App\Exception\Http;

class UnprocessableEntityException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 422;
}
