<?php

namespace Gzoran\LaravelMicroService\Clients;

/**
 * 客户端请求类
 * Class Request
 *
 * @package Gzoran\LaravelMicroService\Clients
 */
class Request
{
    /**
     * 服务端名称
     *
     * @var string
     */
    protected $serverName;

    /**
     * 服务端名称
     * 静态属性用以在任何地方调用
     * 临时值会在调用发生时变更
     *
     * @var string
     */
    public static $tempServerName;

    /**
     * 组 id
     * 用以将一个请求中的一系列服务调用归为一组
     *
     * @var string
     */
    public static $groupId;

    /**
     * 服务类
     *
     * @var string
     */
    protected $class;

    /**
     * 方法
     *
     * @var string
     */
    protected $method;

    /**
     * 参数
     *
     * @var array
     */
    public $arguments;

    /**
     * 重试次数
     *
     * @var int
     */
    protected $retry;

    /**
     * Request constructor.
     *
     * @param string $serverName
     * @param string $class
     * @param string $method
     * @param array $arguments
     * @param int $retry
     */
    public function __construct(string $serverName, string $class, string $method, array $arguments, int $retry = 0)
    {
        $this->serverName = $serverName;
        static::$tempServerName = $this->serverName;
        $this->class = $class;
        $this->method = $method;
        $this->arguments = $arguments;
        $this->retry = $retry;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return int
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getRetry()
    {
        return $this->retry;
    }
}