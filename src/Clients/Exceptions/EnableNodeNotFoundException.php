<?php

namespace Gzoran\LaravelMicroService\Clients\Exceptions;

use Throwable;

/**
 * 可用节点未找到 异常
 *
 * Class EnableNodeNotFoundException
 *
 * @package Gzoran\LaravelMicroService\Clients\Exception
 */
class EnableNodeNotFoundException extends ClientException
{
    /**
     * EnableNodeNotFoundException constructor.
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