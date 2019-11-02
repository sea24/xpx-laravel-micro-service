<?php

namespace Gzoran\LaravelMicroService\Servers;

use Gzoran\LaravelMicroService\Servers\Filters\EncryptFilter;

class Kernel
{
    /**
     * 全局服务
     *
     * @var array
     */
    protected $services = [];

    /**
     * 全局中间件
     *
     * @var array
     */
    protected $middleware = [];

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
        $this->services = config('microservice.server_services', []);
        $this->middleware = config('microservice.server_middleware', []);
        $this->filters = config('microservice.server_filters', []);
    }

    /**
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getServices()
    {
        return $this->services;
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
    public function getFilters()
    {
        return $this->filters;
    }
}