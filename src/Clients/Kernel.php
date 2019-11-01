<?php

namespace Gzoran\LaravelMicroService\Clients;

class Kernel
{
    /**
     * 全局中间件
     *
     * @var array
     */
    protected $middleware = [
        //
    ];

    /**
     * 中间件组
     *
     * @var array
     */
    protected $middlewareGroups = [
        // 组名 => 中间件数组
    ];

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
}