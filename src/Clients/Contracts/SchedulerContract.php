<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

use Gzoran\LaravelMicroService\Clients\Node;

/**
 * 调度器接口
 * Interface SchedulerContract
 *
 * @package Gzoran\LaravelMicroService\Contracts
 */
interface SchedulerContract
{
    /**
     * 设置或获取服务端名称
     *
     * @param string|null $serverName
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function serverName(string $serverName = null);

    /**
     * 注册节点列表
     *
     * @param bool $cacheable
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerNodes($cacheable = true);

    /**
     * 获取节点
     *
     * @return Node
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNode() : Node;

    /**
     * 调度策略
     *
     * @return Node
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function policy() : Node;
}