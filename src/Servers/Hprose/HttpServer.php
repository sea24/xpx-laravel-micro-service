<?php

namespace Gzoran\LaravelMicroService\Servers\Hprose;

use Gzoran\LaravelMicroService\Servers\Contracts\HproseServerContract;
use Gzoran\LaravelMicroService\Servers\Hprose\Traits\ServerTrait;
use Gzoran\LaravelMicroService\Servers\Hprose\Traits\ThroughPipelinesTrait;
use Hprose\Http\Server;

/**
 * 重写 Hprose Http 服务端
 * Class HttpServer
 *
 * @package Gzoran\LaravelMicroService\Servers\Hprose
 */
class HttpServer extends Server implements HproseServerContract
{
    // 公共方法
    use ServerTrait;
    // 管道
    use ThroughPipelinesTrait;
}