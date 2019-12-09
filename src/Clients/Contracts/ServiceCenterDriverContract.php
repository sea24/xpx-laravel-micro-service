<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

/**
 * 服务中心驱动接口
 * Interface ServiceCenterDriverContract
 *
 * @package Gzoran\LaravelMicroService\Servers\Contracts
 */
interface ServiceCenterDriverContract
{
    /**
     * 设置服务端名称
     *
     * @param string $serverName
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setServerName(string $serverName);

    /**
     * 节点是否更新
     *
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function isNodesUpdate(): bool;

    /**
     * 获取节点列表
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNodes(): array;

    /**
     * 注册服务端
     *
     * @param string $serverName
     * @param array $nodes
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerServer(string $serverName, array $nodes): bool;

    /**
     * 注销服务端
     *
     * @param string $serverName
     * @param array $nodes
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function logoutServer(string $serverName, array $nodes): bool;

    /**
     * 报告服务端心跳
     *
     * @param string $serverName
     * @param array $node
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function reportServer(string $serverName, array $node): bool;
}