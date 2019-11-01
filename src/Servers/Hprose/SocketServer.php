<?php

namespace Gzoran\LaravelMicroService\Servers\Hprose;

use Gzoran\LaravelMicroService\Servers\Contracts\HproseServerContract;
use Hprose\Socket\Server;

/**
 * 重写 Hprose Socket 服务端
 * Class HttpServer
 *
 * @package Gzoran\LaravelMicroService\Servers\Hprose
 */
class SocketServer extends Server implements HproseServerContract
{
    use ThroughPipelines;
}