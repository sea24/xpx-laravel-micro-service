<?php

namespace Gzoran\LaravelMicroService\Servers;

use Gzoran\LaravelMicroService\Servers\Contracts\HproseServerContract;
use Gzoran\LaravelMicroService\Servers\Contracts\ServerContract;

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
     * 过滤器
     *
     * @var array
     */
    protected $filters = [];

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
        $this->serverName = $this->serverName();
    }

    /**
     * 服务端名称
     *
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function serverName()
    {
        return $this->serverName;
    }

    /**
     * 开启
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function start()
    {
        if (!file_get_contents('php://input')) {
            echo <<<TEXT
　　　┏┓ 　　┏┓+ +
 　┏┛┻━━━┛┻┓ + +
 　┃　　　　　　　┃ 　
 　┃　　　━　　　┃ ++ + + +
  ████━████ ┃+
 　┃　　　　　　　┃ +
 　┃　　　┻　　　┃
 　┃　　　　　　　┃ + +
 　┗━┓　　　┏━┛
 　　　┃　　　┃　　　　　　　　　　　
 　　　┃　　　┃ + + + +
 　　　┃　　　┃
 　　　┃　{$this->serverName} as your service!
 　　　┃　　　┃    　　
 　　　┃　　　┃　　+　　　　　　　　　
 　　　┃　 　　┗━━━┓ + +
 　　　┃ 　　　　　　　┣┓
 　　　┃ 　　　　　　　┏┛
 　　　┗┓┓┏━┳┓┏┛ + + + +
 　　　　┃┫┫　┃┫┫
 　　　　┗┻┛　┗┻┛+ + + +
TEXT;
;
            return;
        }

        $this->server = $this->server();

        $this->server->setServerName($this->serverName);

        $this->register();

        $filters = array_merge($this->filters, $this->kernel->getFilters());
        // foreach ($filters as $filter) {
        //     $this->server->addFilter(new $filter);
        // }
        $this->server->addFilter(new \Gzoran\LaravelMicroService\Servers\Filters\Filter());

        $middleware = array_merge($this->kernel->getMiddleware(), $this->middleware);

        $this->server->onSendError=function($err){info($err);};//调试用 打印错误到日志里面
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
       
        $services = array_merge($this->kernel->getServices(), $this->services);

        foreach ($services as $prefix => $service) {
            $this->server->addInstanceMethods(new $service, '', $prefix);
        }
    }
}