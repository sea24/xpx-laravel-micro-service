<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

use Gzoran\LaravelMicroService\Clients\Request;
use Closure;

/**
 * 熔断器接口
 * Interface CircuitBreakerContract
 *
 * @package Gzoran\LaravelMicroService\Contracts
 */
interface CircuitBreakerContract
{
    /**
     * @param Closure $process
     * @param Closure|null $failback
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function process(Closure $process, Closure $failback = null);

    /**
     * 设置客户端请求
     *
     * @param Request $request
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setRequest(Request $request);
}