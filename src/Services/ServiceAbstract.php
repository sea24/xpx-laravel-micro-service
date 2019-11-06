<?php

namespace Gzoran\LaravelMicroService\Services;

use Gzoran\LaravelMicroService\Services\Contracts\ServiceContract;

/**
 * RPC 服务抽象类
 * Class ServiceAbstract
 *
 * @package Gzoran\LaravelMicroService\Services
 */
abstract class ServiceAbstract implements ServiceContract
{
    /**
     * @return array|mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function ping()
    {
        return [
            'message' => 'ok',
            'time' => time(),
        ];
    }
}