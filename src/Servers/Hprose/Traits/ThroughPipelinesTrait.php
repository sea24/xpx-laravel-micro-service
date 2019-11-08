<?php

namespace Gzoran\LaravelMicroService\Servers\Hprose\Traits;

use Gzoran\LaravelMicroService\Servers\Request;
use Exception;
use Hprose\Future;
use function Hprose\Future\promise;
use Illuminate\Pipeline\Pipeline;
use stdClass;

/**
 * 管道
 * Trait ThroughPipelines
 *
 * @package Gzoran\LaravelMicroService\Servers\Hprose
 */
trait ThroughPipelinesTrait
{
    /**
     * 服务端名称
     *
     * @var string
     */
    protected $serverName;

    /**
     * 中间件
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * 将原来的方法拆开两个，并加上管道以应用中间件
     *
     * @param $serviceName
     * @param array $args
     * @param stdClass $context
     * @return Future|mixed|null
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function invokeHandler($serviceName, array &$args, stdClass $context) {
        // 管道
        $request = new Request($this->serverName, $serviceName, $args, $context);
        $result = app(Pipeline::class)
            ->send($request)
            ->through($this->middleware)
            ->then(function (Request $request) {
                // 执行调用
                return $this->invokeExecute($request->getServiceName(), $request->args, $request->getContext());
            });

        return $result;
    }

    /**
     * @param $name
     * @param array $args
     * @param stdClass $context
     * @return Future|mixed|null
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function invokeExecute($name, array &$args, stdClass $context)
    {
        if ($context->isMissingMethod) {
            $args = array($name, $args);
        }
        $passContext = $context->passContext;
        if ($passContext === null) {
            $context->passContext = $passContext = $this->passContext;
        }
        if ($context->async) {
            $self = $this;
            return promise(function($resolve, $reject) use ($self, $passContext, &$args, $context) {
                if ($passContext) $args[] = $context;
                $args[] = function($value) use ($resolve, $reject) {
                    if ($value instanceof Exception || $value instanceof Throwable) {
                        $reject($value);
                    }
                    else {
                        $resolve($value);
                    }
                };
                $self->callService($args, $context);
            });
        }
        else {
            if ($passContext) $args[] = $context;
            return $this->callService($args, $context);
        }
    }

    /**
     * 设置服务端名称
     *
     * @param string $serverName
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setServerName(string $serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * 设置中间件
     *
     * @param array $middleware
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setMiddleware(array $middleware = [])
    {
        $this->middleware = $middleware;
    }
}