<?php

namespace Gzoran\LaravelMicroService\Clients\Exceptions;

use Illuminate\Support\Arr;
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
     * 消息数组
     *
     * @var array
     */
    protected $messageArray;

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

        $this->messageArray = \json_decode($this->message, true) ?? [];
    }

    /**
     * 获取消息数组
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getMessageArray()
    {
        return $this->messageArray;
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteMessage()
    {
        return Arr::get($this->messageArray, 'message');
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteCode()
    {
        return Arr::get($this->messageArray, 'code');
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteErrors()
    {
        return Arr::get($this->messageArray, 'errors');
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteFile()
    {
        return Arr::get($this->messageArray, 'file');
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteLine()
    {
        return Arr::get($this->messageArray, 'line');
    }

    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRemoteFrom()
    {
        return Arr::get($this->messageArray, 'from');
    }
}