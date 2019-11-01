<?php

namespace Gzoran\LaravelMicroService\Servers\Contracts;

use Hprose\Filter;

/**
 * 重写 Hprose 服务端的接口
 * Class HproseServerContract
 *
 * @package Gzoran\LaravelMicroService\Servers\Contracts
 */
interface HproseServerContract
{
    /**
     * 设置中间件
     *
     * @param array $middleware
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setMiddleware(array $middleware = []);

    /**
     * 开启
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function start();

    /**
     * 注册类示例为服务
     *
     * @param $object
     * @param string $class
     * @param string $aliasPrefix
     * @param array $options
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function addInstanceMethods($object, $class = '', $aliasPrefix = '', array $options = array());

    /**
     * 设置过滤器
     *
     * @param Filter $filter
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function addFilter(Filter $filter);
}