<?php

namespace Gzoran\LaravelMicroService\Exceptions;

use Throwable;

/**
 * 服务异常基类
 * Class ServiceException
 *
 * @package Gzoran\LaravelMicroService\Exceptions
 */
class MicroServiceException extends \Exception
{
    /**
     * ServiceException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}