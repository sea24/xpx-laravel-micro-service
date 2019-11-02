<?php

namespace Gzoran\LaravelMicroService\Clients;

use Gzoran\LaravelMicroService\Clients\CircuitBreakers\BaseCircuitBreaker;
use Gzoran\LaravelMicroService\Clients\Contracts\CircuitBreakerContract;
use Gzoran\LaravelMicroService\Clients\Contracts\RemoteContract;
use Gzoran\LaravelMicroService\Clients\Contracts\SchedulerContract;
use Gzoran\LaravelMicroService\Clients\Contracts\ClientContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Schedulers\RandomScheduler;
use Gzoran\LaravelMicroService\Exceptions\MicroServiceException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;

/**
 * 客户端抽象类
 * Class ClientAbstract
 *
 * @package Gzoran\LaravelMicroService\Clients
 */
abstract class ClientAbstract implements ClientContract
{
    /**
     * 服务端名称，在服务中心中唯一注册
     *
     * @var string
     */
    protected $serverName;

    /**
     * 远程调用实现
     *
     * @var RemoteContract
     */
    protected $remote;

    /**
     * 调度器
     *
     * @var SchedulerContract
     */
    protected $scheduler;

    /**
     * 客户端中间件
     *
     * @var array
     */
    protected $clientMiddleware = [
        //
    ];

    /**
     * 方法中间件
     *
     * @var array
     */
    protected $methodMiddleware = [
        //
    ];

    /**
     * 方法重试次数
     *
     * @var array
     */
    protected $methodRetry = [
        //
    ];

    /**
     * 客户端熔断器
     *
     * @var string
     */
    protected $clientCircuitBreaker = BaseCircuitBreaker::class;

    /**
     * 方法熔断器
     *
     * @var array
     */
    protected $methodCircuitBreakers = [
        // 方法 => 熔断器
    ];

    /**
     * 降级方法后缀
     *
     * @var string
     */
    protected $failbackPostfix = 'Fallback';

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * ServiceAbstract constructor.
     * @throws ClientException
     */
    public function __construct()
    {
        $this->kernel = new Kernel();

        if (!$remote = $this->remote()) {
            throw new ClientException('Remote has not been set.');
        }
        $this->remote = new $remote;

        if (!$scheduler = $this->scheduler()) {
            throw new ClientException('Scheduler has not been set.');
        }
        $this->scheduler = new $scheduler;
        $this->scheduler->serverName($this->serverName);
        $this->scheduler->registerNodes();
    }

    /**
     * 设置调度器
     *
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function scheduler()
    {
        return RandomScheduler::class;
    }

    /**
     * 获取管道中间件
     *
     * @param string $method
     * @return array
     * @throws MicroServiceException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function getPipes(string $method)
    {
        // 获取全局中间价
        $pipes = $this->kernel->getMiddleware();
        // 中间件组
        $middlewareGroups = $this->kernel->getMiddlewareGroups();
        // 客户端中间件
        $middleware = $this->clientMiddleware;
        // 方法中间件
        foreach ($this->methodMiddleware as $middlewareClass => $methods) {
            if (in_array($method, $methods)) {
                $middleware[] = $middlewareClass;
            }
        }

        foreach ($middleware as $middlewareClass) {
            if ($pipeGroups = Arr::get($middlewareGroups, $middlewareClass)) {
                $pipes = array_merge($pipes, $pipeGroups);
                continue;
            }
            if (!class_exists($middlewareClass)) {
                throw new MicroServiceException('Middleware not found:' . $middlewareClass);
            }
            $pipes[] = $middlewareClass;
        }

        return array_unique($pipes);
    }

    /**
     * 执行远程调用
     *
     * @param Request $request
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function remoteInvoke(Request $request)
    {
        $this->remote->setScheduler($this->scheduler)
            ->setFilters($this->kernel->getFilters());

        // 若客户端或方法配置了熔断器, 则使用熔断器执行
        $circuitBreakerClass = null;
        if ($this->clientCircuitBreaker) {
            $circuitBreakerClass = $this->clientCircuitBreaker;
        }
        // 方法熔断器优先
        if ($methodCircuitBreaker = Arr::get($this->methodCircuitBreakers, $request->getMethod())) {
            $circuitBreakerClass = $methodCircuitBreaker;
        }

        if ($circuitBreakerClass) {
            /**
             * @var CircuitBreakerContract $circuitBreaker
             */
            $circuitBreaker = new $circuitBreakerClass;
            $circuitBreaker->setRequest($request);

            $failback = null;
            $failbackMethod = $request->getMethod() . $this->failbackPostfix;
            if (method_exists($this, $failbackMethod)) {
                $failback = function (\Exception $exception) use ($request, $failbackMethod) {
                    return $this->$failbackMethod($exception, $request);
                };
            }

            $result = $circuitBreaker->process(function () use ($request) {
                return $this->remote->invoke($request);
            }, $failback);

            return $result;
        }

        // 没配置熔断器则直接执行
        return $this->remote->invoke($request);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws MicroServiceException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function __call(string $method, array $arguments)
    {
        // 管道
        $pipes = $this->getPipes($method);
        $methodRetry = Arr::get($this->methodRetry, $method, 0);
        $request = new Request($this->serverName, static::class, $method, $arguments, $methodRetry);
        $result = app(Pipeline::class)
            ->send($request)
            ->through($pipes)
            ->then(function (Request $request) use ($method) {
                // 执行调用
                return $this->remoteInvoke($request);
            });

        return $result;
    }
}