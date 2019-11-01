<?php

namespace Gzoran\LaravelMicroService\Servers;

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
     * Kernel constructor.
     */
    public function __construct()
    {
        $this->services = config('microservice.server_services', []);
        $this->middleware = config('microservice.server_middleware', []);
    }

    /**
     * 注册全局服务
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getServices()
    {
        // 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
        return [
            //
        ];
    }

    /**
     * 全局中间件
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}