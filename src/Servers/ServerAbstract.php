<?php

namespace Gzoran\LaravelMicroService\Servers;

use Gzoran\LaravelMicroService\Servers\Contracts\HproseServerContract;
use Gzoran\LaravelMicroService\Servers\Contracts\ServerContract;
use Gzoran\LaravelMicroService\Servers\Filters\EncryptFilter;

/**
 * RPC 服务端抽象类
 * Class ServerAbstract
 */
abstract class ServerAbstract implements ServerContract
{
    /**
     * 服务端名称，在服务中心中唯一注册
     *
     * @var string
     */
    protected $serverName;

    /**
     * @var HproseServerContract
     */
    protected $server;

    /**
     * 服务注册
     *
     * @var array
     */
    protected $services = [
        //
    ];

    /**
     * 中间件
     *
     * @var array
     */
    protected $middleware = [
        //
    ];

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * ServerAbstract constructor.
     */
    public function __construct()
    {
        $this->kernel = new Kernel();
    }

    /**
     * 开启
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function start()
    {
        $this->server = $this->server();

        $this->register();

        $this->server->addFilter(new EncryptFilter());

        $middleware = array_merge($this->kernel->middleware(), $this->middleware);
        $this->server->setMiddleware($middleware);

        $this->server->start();
    }

    /**
     * 注册服务
     *
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function register()
    {
        $services = array_merge($this->kernel->services(), $this->services);

        foreach ($services as $prefix => $service) {
            $this->server->addInstanceMethods(new $service, '', $prefix);
        }
    }
}