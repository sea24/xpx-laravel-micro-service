<?php

namespace Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers;

use Gzoran\LaravelMicroService\Clients\Contracts\ServiceCenterDriverContract;

/**
 * 服务中心驱动抽象类
 * Class LocalServiceCenterDriver
 *
 * @package Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers
 */
abstract class ServiceCenterDriverAbstract implements ServiceCenterDriverContract
{
    /**
     * 服务端名称
     *
     * @var string
     */
    protected $serverName;

    /**
     * 设置服务端名称
     *
     * @param string $serverName
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setServerName(string $serverName)
    {
        return $this->serverName = $serverName;
    }
}