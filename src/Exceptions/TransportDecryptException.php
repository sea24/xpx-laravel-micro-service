<?php

namespace Gzoran\LaravelMicroService\Exceptions;

use Throwable;

/**
 * 传输无法被解密异常
 * Class DataCanNotDecryptException
 *
 * @package Gzoran\LaravelMicroService\Exceptions
 */
class TransportDecryptException extends MicroServiceException
{
    /**
     * DataCanNotDecryptException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}