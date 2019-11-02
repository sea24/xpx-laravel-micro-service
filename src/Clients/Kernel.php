<?php

namespace Gzoran\LaravelMicroService\Clients;

class Kernel
{
    /**
     * 全局中间件
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * 中间件组
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * 全局过滤器
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Kernel constructor.
     */
    public function __construct()
    {
        $this->middleware = config('microservice.client_middleware', []);
        $this->middlewareGroups = config('microservice.client_middleware_groups', []);
        $this->filters = config('microservice.client_filters', []);
    }

    /**
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getFilters()
    {
        return $this->filters;
    }
}