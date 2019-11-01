<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

use Gzoran\LaravelMicroService\Clients\Request;
use Closure;

/**
 * 服务中间件接口
 * Interface MiddlewareContract
 *
 * @package Gzoran\LaravelMicroService\Contracts
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