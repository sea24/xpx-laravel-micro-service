<?php

namespace Gzoran\LaravelMicroService\Servers\Contracts;

use Gzoran\LaravelMicroService\Servers\Request;
use Closure;

/**
 * 服务端中间件接口
 * Interface MiddlewareContract
 *
 * @package Gzoran\LaravelMicroService\Servers\Contracts
 */
interface MiddlewareContract
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function handle(Request $request, Closure $next);
}