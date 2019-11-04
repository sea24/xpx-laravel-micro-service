<?php

namespace Gzoran\LaravelMicroService\Clients\Exceptions;

use Throwable;

/**
 * 远程调用异常
 * Class RemoteInvokeException
 *
 * @package Gzoran\LaravelMicroService\Clients\Exceptions
 */
class RemoteInvokeException extends ClientException
{
    /**
     * RemoteInvokeException constructor.
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