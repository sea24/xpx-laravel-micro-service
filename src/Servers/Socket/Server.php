<?php

namespace Gzoran\LaravelMicroService\Servers\Socket;

use Gzoran\LaravelMicroService\Servers\Hprose\SocketServer;
use Gzoran\LaravelMicroService\Servers\ServerAbstract;

/**
 * RPC Socket 服务端
 * Class Server
 * 请确保在 Cli 模式运行
 *
 * @package Gzoran\LaravelMicroService\Servers\Http
 */
class Server extends ServerAbstract
{
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
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function server()
    {
        return new SocketServer("tcp://{$this->host}:{$this->port}");
    }
}