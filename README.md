
<h1 align="center"> Laravel 微服务拓展包 </h1>

> 该模块为 Laravel 提供微服务支持，实现服务中间件、熔断器、远程调用器、调度器等，
> 底层 RPC 使用 Hprose 实现，支持 HTTP 或 Socket 协议（推荐 HTTP），
> 客户端与服务端传输使用对称加密以保证数据安全

## 安装

> 拓展包尚未在 composer packagist 中发布，安装方式采用本地安装

- 请将拓展包克隆到项目的相近目录

```shell

git clone git@xxx

```

- 将拓展包的相对路径（或绝对路径）配置到项目的 composer.json 中

```shell

composer config repositories.weather path ../../../laravel-micro-service

```

安装拓展包

```shell

$ composer require gzoran/laravel-micro-service:dev-master

```

## 发布配置文件

以下命令将会把配置文件 microservice.php 发布到 config 目录

```shell

php artisan vendor:publish --provider="Gzoran\LaravelMicroService\LaravelMicroServiceProvider"

```

## 设置服务中心

> 只有设置服务中心之后，你的服务才能正确发布到服务中心，客户端才能正确的找到服务端节点

- 设置服务中心驱动默认使用配置项 default

```php

···

'service_center_driver' => [
    // 默认使用
    'default' => env('MICRO_SERVICE_SERVICE_CENTER_DRIVER_DEFAULT', 'local'),
    // 本地驱动
    'local' => \Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\LocalServiceCenterDriver::class,
    // 远程驱动
    'remote' => \Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\RemoteServiceCenterDriver::class,
],

···

```

- 当你使用 local 驱动时，则代表服务端节点列表将在你的配置文件中读取，请根据项目环境配置好服务端节点（server_nodes）配置项

```php

···

'server_nodes' => [

    ···
    
    // 开发环境
    'local' => [
        [
            // 服务端名称
            'server_name' => 'demo_server',
            // 节点列表
            'nodes' => [
                [
                    'scheme' => 'http', // 协议
                    'host' => 'www.demo.com', // 主机地址
                    'port' => 80, // 端口
                    'path' => 'rpc/demo-server', // 路径
                ],
            ],
        ],
    ],
    
    ···
    
],

···

```

- 当你使用 remote 驱动时，则代表服务端节点列表将在服务中心获取，请根据环境配置好服务中心节点（service_center_nodes）配置项

```php

···

'service_center_nodes' => [

    ···
    
    // 开发环境
    'local' => [
        [
            'scheme' => 'http', // 协议
            'host' => 'www.demo.com', // 主机地址
            'port' => 80, // 端口
            'path' => 'rpc/demo-server', // 路径
        ],
    ],
    
    ···
    
],

···

```

## 目录

- <a href="./docs/Server.md">服务端（Server）</a>
- <a href="./docs/Client.md">客户端（Client）</a>

## License

MIT