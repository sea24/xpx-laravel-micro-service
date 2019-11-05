<?php

namespace Gzoran\LaravelMicroService\Servers;

use stdClass;

/**
 * 服务端请求类
 * Class Request
 *
 * @package Gzoran\LaravelMicroService\Servers
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
     * 服务名称
     *
     * @var string
     */
    protected $serviceName;

    /**
     * 参数
     *
     * @var array
     */
    public $args;

    /**
     * 上下文
     *
     * @var stdClass
     */
    protected $context;

    /**
     * Request constructor.
     * @param string $serverName
     * @param string $serviceName
     * @param array $args
     * @param stdClass $context
     */
    public function __construct(string $serverName, string $serviceName, array &$args, stdClass $context)
    {
        $this->serverName = $serverName;
        $this->serviceName = $serviceName;
        $this->args = $args;
        $this->context = $context;
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
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return stdClass
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getContext()
    {
        return $this->context;
    }
}