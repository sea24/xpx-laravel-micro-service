<?php

namespace Gzoran\LaravelMicroService\Services\Exceptions;

use Gzoran\LaravelMicroService\Exceptions\MicroServiceException;
use Throwable;

/**
 * 服务异常
 * Class ServiceException
 *
 * @package Gzoran\LaravelMicroService\Services\Exceptions
 */
class ServiceException extends MicroServiceException
{
    /**
     * ServiceException constructor.
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