<?php

namespace Gzoran\LaravelMicroService\Clients\CircuitBreakers;

use Gzoran\LaravelMicroService\Clients\CircuitBreakers\CircuitBreakerAbstract;
use Closure;

/**
 * 基础熔断器
 * Class BaseCircuitBreaker
 *
 * @package Gzoran\LaravelMicroService\CircuitBreakers
 */
class BaseCircuitBreaker extends CircuitBreakerAbstract
{
    /**
     * @param Closure $process
     * @param Closure|null $failback
     * @return mixed
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function process(Closure $process, Closure $failback = null)
    {
        try {
            return $process();
        } catch (\Exception $exception) {
            if (!$failback) {
                throw $exception;
            }

            logs()->warning("[Circuit Breaker Failback] {$this->request->getClass()}::{$this->request->getMethod()} invoke fail：{$exception->getMessage()}");

            return $failback($exception);
        }
    }
}