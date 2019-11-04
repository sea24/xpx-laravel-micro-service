<?php

namespace Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers;

use Illuminate\Support\Arr;

/**
 * 本地服务中心驱动
 * Class LocalServiceCenterDriver
 *
 * @package Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers
 */
class LocalServiceCenterDriver extends ServiceCenterDriverAbstract
{

    /**
     * 节点是否更新
     *
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function isNodesUpdate(): bool
    {
        return true;
    }

    /**
     * 获取节点列表
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNodes(): array
    {
        $env = config('app.env');
        $serverNodes = config('microservice.server_nodes.' . $env, []);
        $serverNodes = collect($serverNodes)->where('server_name', $this->serverName)->first() ?? [];
        $nodes = Arr::get($serverNodes, 'nodes', []);

        return $nodes;
    }

    /**
     * 注册服务端
     *
     * @param string $serverName
     * @param array $node
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerServer(string $serverName, array $node): bool
    {
        return true;
    }

    /**
     * 注销服务端
     *
     * @param string $serverName
     * @param array $node
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function logoutServer(string $serverName, array $node): bool
    {
        return true;
    }
}