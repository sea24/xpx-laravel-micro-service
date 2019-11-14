<?php

namespace Gzoran\LaravelMicroService\Servers\Exceptions;

use Gzoran\LaravelMicroService\Exceptions\MicroServiceException;
use Throwable;

/**
 * 服务端异常基类
 * Class ServerException
 *
 * @package Gzoran\LaravelMicroService\Servers\Exceptions
 */
class ServerException extends MicroServiceException
{
    /**
     * ServerException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}