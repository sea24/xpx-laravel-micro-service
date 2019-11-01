<?php

namespace Gzoran\LaravelMicroService\Servers\Hprose;

use Gzoran\LaravelMicroService\Servers\Contracts\HproseServerContract;
use Hprose\Http\Server;

/**
 * 重写 Hprose Http 服务端
 * Class HttpServer
 *
 * @package Gzoran\LaravelMicroService\Servers\Hprose
 */
class HttpServer extends Server implements HproseServerContract
{
    use ThroughPipelines;
}