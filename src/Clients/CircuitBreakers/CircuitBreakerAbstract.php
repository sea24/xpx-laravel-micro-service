<?php

namespace Gzoran\LaravelMicroService\Clients\CircuitBreakers;

use Gzoran\LaravelMicroService\Clients\Contracts\CircuitBreakerContract;
use Gzoran\LaravelMicroService\Clients\Request;

/**
 * 熔断器抽象类
 * Class TimeoutCircuitBreaker
 *
 * @package Gzoran\LaravelMicroService\CircuitBreakers
 */
abstract class CircuitBreakerAbstract implements CircuitBreakerContract
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * 设置客户端请求
     *
     * @param Request $request
     * @return mixed|void
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}