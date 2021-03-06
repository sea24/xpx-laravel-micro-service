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
     * @param array $nodes
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerServer(string $serverName, array $nodes): bool
    {
        return true;
    }

    /**
     * 注销服务端
     *
     * @param string $serverName
     * @param array $nodes
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function logoutServer(string $serverName, array $nodes): bool
    {
        return true;
    }

    /**
     * 报告服务端心跳
     *
     * @param string $serverName
     * @param array $node
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function reportServer(string $serverName, array $node): bool
    {
        return true;
    }

    /**
     * 跟踪请求
     *
     * @param string $jsonRpc
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function traceRequest(string $jsonRpc): bool
    {
        return true;
    }

    /**
     * 跟踪响应
     *
     * @param string $jsonRpc
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function traceResponse(string $jsonRpc): bool
    {
        return true;
    }
}