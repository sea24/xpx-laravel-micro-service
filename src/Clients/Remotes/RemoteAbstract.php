<?php

namespace Gzoran\LaravelMicroService\Clients\Remotes;

use Gzoran\LaravelMicroService\Clients\Filters\EncryptFilter;
use Gzoran\LaravelMicroService\Clients\Contracts\RemoteContract;
use Gzoran\LaravelMicroService\Clients\Contracts\SchedulerContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Node;
use Gzoran\LaravelMicroService\Clients\Request;
use Hprose\Client;

/**
 * 远程调用器抽象类
 * Class RemoteAbstract
 *
 * @package Gzoran\LaravelMicroService\Remotes
 */
abstract class RemoteAbstract implements RemoteContract
{
    /**
     * 节点
     *
     * @var Node
     */
    protected $node;

    /**
     * 调度器
     *
     * @var SchedulerContract
     */
    protected $scheduler;

    /**
     * 失败计数
     *
     * @var int
     */
    protected $networkFailCounter = 0;

    /**
     * 客户端实例
     *
     * @var Client
     */
    private $client;

    /**
     * 超时时间（毫秒）
     *
     * @var int
     */
    protected $timeout = 30000;

    /**
     * RemoteAbstract constructor.
     */
    public function __construct()
    {
        $this->timeout = config('microservice.remote_timeout', 30000);
    }

    /**
     * @return Client
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function client() : Client
    {
        if (!$this->client) {
            $scheme = $this->node->getScheme();
            if (!in_array($scheme, ['tcp', 'http', 'https'])) {
                throw new ClientException('The scheme is not support:' . $scheme);
            }
            $url = trim("{$scheme}://{$this->node->getHost()}:{$this->node->getPort()}/{$this->node->getPath()}", '/');
            // 建立同步客户
            $this->client = Client::create($url, false);
            $this->client->addFilter(new EncryptFilter());
            $this->client->timeout = $this->timeout;
        }

        return $this->client;
    }

    /**
     * 调用
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function invoke(Request $request)
    {
        $this->node = $this->scheduler->getNode();
        $method = $request->getMethod();

        try {
            return $this->$method(...$request->arguments);
        } catch (\Exception $exception) {
            $methodRetry = $request->getRetry();
            if ($this->networkFailCounter < $methodRetry) {
                $this->networkFailCounter++;
                logs()->warning('Remote invoke retry times ' . $this->networkFailCounter . '; service name:' . $this->scheduler->serverName() . '; method:' . $method);
                // 将节点失败计数 + 1
                $this->node->increaseFailCounter();
                // 重新来一次
                return $this->invoke($request);
            }

            throw $exception;
        }
    }

    /**
     * 设置调度器
     *
     * @param SchedulerContract $scheduler
     * @return $this|RemoteContract
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setScheduler(SchedulerContract $scheduler)
    {
        $this->scheduler = $scheduler;

        return $this;
    }
}