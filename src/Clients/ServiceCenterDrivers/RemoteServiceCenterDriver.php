<?php

namespace Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers;

use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Exceptions\EnableNodeNotFoundException;
use Gzoran\LaravelMicroService\Clients\Filters\EncryptFilter;
use Gzoran\LaravelMicroService\Clients\Node;
use Hprose\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * 远程服务中心驱动
 * Class RemoteServiceCenterDriver
 *
 * @package Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers
 */
class RemoteServiceCenterDriver extends ServiceCenterDriverAbstract
{
    /**
     * 服务中心节点列表
     *
     * @var array
     */
    protected $serviceCenterNodes = [];

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
     * 请求超时（毫秒）
     *
     * @var int
     */
    protected $timeout = 30000;

    /**
     * 重试次数
     *
     * @var int
     */
    protected $retry = 5;

    /**
     * 失败计数
     *
     * @var int
     */
    protected $failCounter = 0;

    /**
     * RemoteServiceCenterDriver constructor.
     */
    public function __construct()
    {
        $this->nodesCacheKey = config('microservice.nodes_cache_key', 'service_nodes');
        $this->nodesCacheExpire = config('microservice.nodes_cache_expire', 3600);
        $this->initServiceCenterNodes();
    }

    /**
     * 初始化服务中心节点列表
     *
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function initServiceCenterNodes()
    {
        $env = config('app.env');
        $nodes = config('microservice.service_center_nodes.' . $env, []);

        foreach ($nodes as $node) {
            $this->serviceCenterNodes[] = new Node(
                Arr::get($node, 'scheme'),
                Arr::get($node, 'host'),
                Arr::get($node, 'port'),
                Arr::get($node, 'path')
            );
        }
    }

    /**
     * 获取 Hprose 客户端
     *
     * @param Node $node
     * @return Client
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function client(Node $node): Client
    {
        $scheme = $node->getScheme();
        if (!in_array($scheme, ['tcp', 'http', 'https'])) {
            throw new ClientException('The scheme is not support:' . $scheme);
        }
        $url = trim("{$scheme}://{$node->getHost()}:{$node->getPort()}/{$node->getPath()}", '/');
        // 建立同步客户
        /**
         * @var Client $client
         */
        $client = Client::create($url, false);
        $client->addFilter(new EncryptFilter());
        $client->timeout = $this->timeout;

        return $client;
    }

    /**
     * 获取服务中心节点（随机策略）
     *
     * @return Node
     * @throws EnableNodeNotFoundException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function getServiceCenterNode()
    {
        $nodes = collect($this->serviceCenterNodes)->filter(function (Node $node) {
            return $node->getStatus() == true;
        });

        if ($nodes->isEmpty()) {
            throw new EnableNodeNotFoundException('There are no enable node in list.');
        }

        return $nodes->random();
    }

    /**
     * 节点是否更新
     *
     * @return bool
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function isNodesUpdate(): bool
    {
        $serviceCenterNode = $this->getServiceCenterNode();

        try {
            $hash = $this->client($serviceCenterNode)->service->nodesHash($this->serverName);
            // 对比缓存中的哈希
            $cacheKey = $this->serverName . '_' . $this->nodesCacheKey . '_hash';
            if (Cache::get($cacheKey) == $hash) {
                return false;
            }
            Cache::put($cacheKey, $hash, $this->nodesCacheExpire);

            return true;
        } catch (\Exception $exception) {
            if ($this->failCounter <= $this->retry) {
                $this->failCounter++;
                logs()->warning('Get nodes hash from service center fail! Now retry ' . $this->failCounter);
                // 标记节点失效
                $serviceCenterNode->disable();
                return $this->isNodesUpdate();
            }
            throw $exception;
        }
    }

    /**
     * 获取节点列表
     *
     * @return array
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getNodes(): array
    {
        $serviceCenterNode = $this->getServiceCenterNode();
        try {
            return $this->client($serviceCenterNode)->service->getNodes($this->serverName);
        } catch (\Exception $exception) {
            if ($this->failCounter <= $this->retry) {
                $this->failCounter++;
                logs()->warning('Get nodes from service center fail! Now retry ' . $this->failCounter);
                report($exception);
                // 标记节点失效
                $serviceCenterNode->disable();
                return $this->getNodes();
            }
            throw $exception;
        }
    }
}