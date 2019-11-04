<?php

namespace Gzoran\LaravelMicroService\Clients\Schedulers;

use Gzoran\LaravelMicroService\Clients\Contracts\SchedulerContract;
use Gzoran\LaravelMicroService\Clients\Contracts\ServiceCenterDriverContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Exceptions\EnableNodeNotFoundException;
use Gzoran\LaravelMicroService\Clients\Node;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\LocalServiceCenterDriver;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\RemoteServiceCenterDriver;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * 调度器抽象类
 * Class BaseScheduler
 *
 * @package Gzoran\LaravelMicroService\Schedulers
 */
abstract class SchedulerAbstract implements SchedulerContract
{
    /**
     * @var string
     */
    protected $serverName;

    /**
     * 服务中心驱动
     *
     * @var ServiceCenterDriverContract
     */
    protected $serviceCenterDriver;

    /**
     * 服务节点列表
     *
     * @var array
     */
    protected $nodes = [];

    /**
     * 启用节点列表缓存
     *
     * @var bool
     */
    protected $nodesCacheEnable = false;

    /**
     * 节点列表缓存键
     *
     * @var string
     */
    protected $nodesCacheKey = 'service_nodes';

    /**
     * 节点列表缓存有效期（秒）
     *
     * @var int
     */
    protected $nodesCacheExpire = 3600;

    /**
     * 获取节点列表重试次数
     *
     * @var int
     */
    protected $getNodesRetry = 3;

    /**
     * 获取节点失败计数
     *
     * @var int
     */
    protected $getNodeFailCounter = 0;

    /**
     * SchedulerAbstract constructor.
     *
     * @throws ClientException
     */
    public function __construct()
    {
        $this->nodesCacheEnable = config('microservice.nodes_cache_enable', false);
        $this->nodesCacheKey = config('microservice.nodes_cache_key', 'service_nodes');
        $this->nodesCacheExpire = config('microservice.nodes_cache_expire', 3600);
        $this->getNodesRetry = config('microservice.get_nodes_retry', 3);

        // 服务中心驱动
        switch ($driver = config('microservice.service_center_driver.default', 'local')) {
            case 'remote':
                $driverClass = config('microservice.service_center_driver.remote', RemoteServiceCenterDriver::class);
                break;
            case 'local':
                $driverClass = config('microservice.service_center_driver.local', LocalServiceCenterDriver::class);
                break;
            default:
                throw new ClientException('Cat not match service center driver like:' . $driver);
        }
        $this->serviceCenterDriver = new $driverClass;
    }

    /**
     * 设置或获取服务端名称
     *
     * @param string|null $serverName
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function serverName(string $serverName = null)
    {
        if ($serverName) {
            $this->serverName = $serverName;
            $this->serviceCenterDriver->setServerName($this->serverName);
        }

        return $this->serverName;
    }

    /**
     * 注册节点
     *
     * @param bool $cacheable
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerNodes($cacheable = true)
    {
        $cacheKey = $this->serverName . '_' . $this->nodesCacheKey;
        if ($cacheable && $this->nodesCacheEnable && !$this->isNodesUpdate()) {
            if (!$nodes = Cache::get($cacheKey)) {
                $nodes = $this->getNodes();
            }
        } else {
            $nodes = $this->getNodes();
        }
        Cache::put($cacheKey, $nodes, $this->nodesCacheExpire);

        foreach ($nodes as $node) {
            $this->nodes[] = new Node(
                Arr::get($node, 'scheme'),
                Arr::get($node, 'host'),
                Arr::get($node, 'port'),
                Arr::get($node, 'path')
            );
        }
    }

    /**
     * 获取节点
     *
     * @return Node
     * @throws EnableNodeNotFoundException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNode() : Node
    {
        try {
            return $this->policy();
        } catch (EnableNodeNotFoundException $exception) {
            // 小于重试次数且节点有更新，则重新注册节点列表，并返回节点
            if (($this->getNodeFailCounter < $this->getNodesRetry) && $this->isNodesUpdate()) {
                $this->getNodeFailCounter++;
                logs()->warning('Get node retry times ' . $this->getNodeFailCounter);
                $this->registerNodes(false);
                return $this->policy();
            }

            throw $exception;
        }
    }

    /**
     * 节点列表是否更新
     *
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function isNodesUpdate() : bool
    {
        return $this->serviceCenterDriver->isNodesUpdate();
    }

    /**
     * 从服务注册中心获取节点列表
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNodes() : array
    {
        return $this->serviceCenterDriver->getNodes();
    }
}