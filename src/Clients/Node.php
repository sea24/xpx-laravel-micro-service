<?php

namespace Gzoran\LaravelMicroService\Clients;

/**
 * 节点类
 * Class Node
 *
 * @package Gzoran\LaravelMicroService\Clients
 */
class Node
{
    /**
     * 协议
     *
     * @var string
     */
    protected $scheme;

    /**
     * 主机地址
     *
     * @var string
     */
    protected $host;

    /**
     * 端口
     *
     * @var int
     */
    protected $port;

    /**
     * 路径
     *
     * @var string
     */
    protected $path;

    /**
     * 可用状态
     *
     * @var bool
     */
    protected $status = true;

    /**
     * 失败计数
     *
     * @var int
     */
    protected $failCounter = 0;

    /**
     * Node constructor.
     *
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @param string $path
     */
    public function __construct(string $scheme, string $host, int $port, $path = null)
    {
        $this->scheme = $scheme;
        $this->host = trim($host, '/');
        $this->port = $port;
        $this->path = trim($path, '/');
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getFailCounter()
    {
        return $this->failCounter;
    }

    /**
     * @param int $inc
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function increaseFailCounter(int $inc = 1)
    {
        $this->failCounter += $inc;
    }

    /**
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function resetFailCounter()
    {
        $this->failCounter = 0;
    }

    /**
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function enable()
    {
        return $this->status = true;
    }

    /**
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function disable()
    {
        $this->status = false;
    }

    /**
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function toArray()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'status' => $this->status,
            'fail_counter' => $this->failCounter,
        ];
    }
}