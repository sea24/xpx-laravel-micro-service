<?php

namespace Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers;

use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Exceptions\EnableNodeNotFoundException;
use Gzoran\LaravelMicroService\Clients\Filters\EncryptFilter;
use Gzoran\LaravelMicroService\Clients\Node;
use Gzoran\LaravelMicroService\Clients\Request;
use Hprose\Client;
use Illuminate\Pipeline\Pipeline;
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
    protected $retry = 3;

    /**
     * 失败计数
     *
     * @var int
     */
    protected $failCounter = 0;

    /**
     * 全局中间件
     * 
     * @var array 
     */
    protected $middleware = [];

    /**
     * 全局过滤器
     *
     * @var array
     */
    protected $filters = [];

    /**
     * RemoteServiceCenterDriver constructor.
     */
    public function __construct()
    {
        $this->nodesCacheKey = config('microservice.nodes_cache_key', 'service_nodes');
        $this->nodesCacheExpire = config('microservice.nodes_cache_expire', 3600);
        $this->filters = config('microservice.client_filters', []);
        $this->middleware = config('microservice.client_middleware', []);

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
        // 注册过滤器
        foreach ($this->filters as $filter) {
            $client->addFilter(new $filter);
        }
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
            throw new EnableNodeNotFoundException('There are no enable service center node in list.');
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
            $hash = $this->invokeThroughPipelines('remoteNodesHash', $serviceCenterNode);
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
            return $this->invokeThroughPipelines('remoteGetNodes', $serviceCenterNode);
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

    /**
     * 注册服务端
     *
     * @param string $serverName
     * @param array $nodes
     * @return bool
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function registerServer(string $serverName, array $nodes): bool
    {

        $serviceCenterNode = $this->getServiceCenterNode();
        
        return $this->invokeThroughPipelines('remoteRegisterServer', $serviceCenterNode, $serverName, $nodes);
    }

    /**
     * 注销服务端
     *
     * @param string $serverName
     * @param array $nodes
     * @return bool
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function logoutServer(string $serverName, array $nodes): bool
    {
        $serviceCenterNode = $this->getServiceCenterNode();

        return $this->invokeThroughPipelines('remoteLogoutServer', $serviceCenterNode, $serverName, $nodes);
    }

    /**
     * 报告服务端心跳
     *
     * @param string $serverName
     * @param array $node
     * @return bool
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function reportServer(string $serverName, array $node): bool
    {
        $serviceCenterNode = $this->getServiceCenterNode();

        return $this->invokeThroughPipelines('remoteReportServer', $serviceCenterNode, $serverName, $node);
    }

    /**
     * 通过管道调用
     *
     * @param $method
     * @param mixed ...$arguments
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function invokeThroughPipelines($method, ...$arguments)
    {
        // 管道
        $request = new Request('service_center_server', static::class, $method, $arguments, $this->retry);
        $result = app(Pipeline::class)
            ->send($request)
            ->through($this->middleware)
            ->then(function (Request $request) use ($method) {
                // 执行调用
                return $this->$method(...$request->arguments);
            });

        return $result;
    }

    /**
     * @param Node $serviceCenterNode
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteNodesHash(Node $serviceCenterNode)
    {
        return $this->client($serviceCenterNode)->service->nodesHash($this->serverName);
    }

    /**
     * @param Node $serviceCenterNode
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteGetNodes(Node $serviceCenterNode) {
        return $this->client($serviceCenterNode)->service->getNodes($this->serverName);
    }

    /**
     * @param Node $serviceCenterNode
     * @param string $serverName
     * @param array $node
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteRegisterServer(Node $serviceCenterNode, string $serverName, array $node)
    {
        return $this->client($serviceCenterNode)->service->registerServer($serverName, $node);
    }

    /**
     * @param Node $serviceCenterNode
     * @param string $serverName
     * @param array $node
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteLogoutServer(Node $serviceCenterNode, string $serverName, array $node)
    {
        return $this->client($serviceCenterNode)->service->logoutServer($serverName, $node);
    }

    /**
     * @param Node $serviceCenterNode
     * @param string $serverName
     * @param array $node
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteReportServer(Node $serviceCenterNode, string $serverName, array $node)
    {
        return $this->client($serviceCenterNode)->service->reportServer($serverName, $node);
    }

    /**
     * 跟踪请求
     *
     * @param string $jsonRpc
     * @return bool
     * @throws EnableNodeNotFoundException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function traceRequest(string $jsonRpc): bool
    {
        $serviceCenterNode = $this->getServiceCenterNode();

        return $this->invokeThroughPipelines('remoteTraceRequest', $serviceCenterNode, $jsonRpc);
    }

    /**
     * @param Node $serviceCenterNode
     * @param string $jsonRpc
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteTraceRequest(Node $serviceCenterNode, string $jsonRpc)
    {
        return $this->client($serviceCenterNode)->trace->traceRequest($jsonRpc);
    }

    /**
     * 跟踪响应
     *
     * @param string $jsonRpc
     * @return bool
     * @throws EnableNodeNotFoundException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function traceResponse(string $jsonRpc): bool
    {
        $serviceCenterNode = $this->getServiceCenterNode();

        return $this->invokeThroughPipelines('remoteTraceResponse', $serviceCenterNode, $jsonRpc);
    }

    /**
     * @param Node $serviceCenterNode
     * @param string $jsonRpc
     * @return mixed
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function remoteTraceResponse(Node $serviceCenterNode, string $jsonRpc)
    {
        return $this->client($serviceCenterNode)->trace->traceResponse($jsonRpc);
    }
}