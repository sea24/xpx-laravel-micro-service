<?php

namespace Gzoran\LaravelMicroService\Servers\Http;

use Gzoran\LaravelMicroService\Servers\Hprose\HttpServer;
use Gzoran\LaravelMicroService\Servers\ServerAbstract;

/**
 * RPC HTTP 服务端
 * Class Server
 * 请将 start 方法注册到路由
 *
 * @package Gzoran\LaravelMicroService\Servers\Http
 */
class Server extends ServerAbstract
{
    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function server()
    {
        return new HttpServer();
    }
}