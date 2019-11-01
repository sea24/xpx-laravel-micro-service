<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

/**
 * 客户端接口
 * Interface ServiceContract
 *
 * @package Gzoran\LaravelMicroService\Contracts
 */
interface ClientContract
{
    /**
     * 调度器
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function scheduler();

    /**
     * 远程调用器
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function remote();
}