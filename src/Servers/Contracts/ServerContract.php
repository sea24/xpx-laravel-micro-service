<?php

namespace Gzoran\LaravelMicroService\Servers\Contracts;

/**
 * RPC 服务端契约
 * Class ServerContract
 *
 * @package Gzoran\LaravelMicroService\Servers\Contracts
 */
interface ServerContract
{
    /**
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function server();

    /**
     * 开启
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function start();
}