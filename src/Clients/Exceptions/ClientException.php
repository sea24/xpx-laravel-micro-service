<?php

namespace Gzoran\LaravelMicroService\Clients\Exceptions;

use Gzoran\LaravelMicroService\Exceptions\MicroServiceException;
use Throwable;

/**
 * 客户端异常
 * Class ClientException
 *
 * @package Gzoran\LaravelMicroService\Clients\Exception
 */
class ClientException extends MicroServiceException
{
    /**
     * ClientException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}