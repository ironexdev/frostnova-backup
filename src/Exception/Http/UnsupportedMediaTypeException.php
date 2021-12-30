<?php declare(strict_types=1);

namespace App\Exception\Http;

class UnsupportedMediaTypeException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 415;
}
