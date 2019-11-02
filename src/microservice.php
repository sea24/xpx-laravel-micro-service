<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 启用节点列表缓存
    |--------------------------------------------------------------------------
    |
    | 启用缓存可以减少请求服务中心次数，在调试环境可以关闭缓存，以免产生不必要的疑惑
    |
    */

    'nodes_cache_enable' => env('MICRO_SERVICE_NODES_CACHE_ENABLE', false),

    /*
    |--------------------------------------------------------------------------
    | 节点列表缓存键
    |--------------------------------------------------------------------------
    |
    | 参与节点列表缓存键的构造，配置会在使用时拼接上服务名称作为真正的键
    |
    */

    'nodes_cache_key' => env('MICRO_SERVICE_CACHE_KEY', 'service_nodes'),

    /*
    |--------------------------------------------------------------------------
    | 节点列表缓存有效期（秒）
    |--------------------------------------------------------------------------
    |
    | 节点列表缓存会在该配置时间之后时效，届时将重新请求服务注册中心获取节点列表
    |
    */

    'nodes_cache_expire' => env('MICRO_SERVICE_NODES_CACHE_EXPIRE', 3600),

    /*
    |--------------------------------------------------------------------------
    | 获取节点列表重试次数
    |--------------------------------------------------------------------------
    |
    | 从服务器获取节点列表失败后的重试次数，到达限制后会抛出节点列表无可以节点异常
    |
    */

    'get_nodes_retry' => env('MICRO_SERVICE_GET_NODES_RETRY', 3),

    /*
    |--------------------------------------------------------------------------
    | 远程调用器超时时间（毫秒）
    |--------------------------------------------------------------------------
    |
    | 远程调用器发送请求之后，超过该时间服务器还没有返回，则会抛出异常
    |
    */

    'remote_timeout' => env('MICRO_SERVICE_REMOTE_TIMEOUT', 30000),

    /*
    |--------------------------------------------------------------------------
    | 服务中心驱动
    |--------------------------------------------------------------------------
    |
    | 支持的值 remote 和 local ，调试时可以使用 local 在配置文件中配置固定的服务节点列表
    |
    */
    'service_center_driver' => env('MICRO_SERVICE_SERVICE_CENTER_DRIVER', 'remote'),


    /*
    |--------------------------------------------------------------------------
    | 服务中心节点列表
    |--------------------------------------------------------------------------
    |
    | 仅当服务中心驱动设置为 remote 时，以下配置的节点列表才会生效
    |
    */
    'service_center_nodes' => [
        // 生产环境
        'production' => [
            //
        ],
        // 测试环境
        'testing' => [
            //
        ],
        // 开发环境
        'local' => [
            [
                'scheme' => 'http', // 协议
                'host' => 'www.demo.com', // 主机地址
                'port' => 80, // 端口
                'path' => 'rpc/demo-server', // 路径
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 服务端节点列表
    |--------------------------------------------------------------------------
    |
    | 仅当服务中心驱动设置为 local 时，以下配置的节点列表才会生效
    |
    */
    'server_nodes' => [
        // 生产环境
        'production' => [
            //
        ],
        // 测试环境
        'testing' => [
            //
        ],
        // 开发环境
        'local' => [
            [
                // 服务端名称
                'server_name' => 'api_gateway_server',
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
    ],

    /*
    |--------------------------------------------------------------------------
    | 服务端全局服务
    |--------------------------------------------------------------------------
    |
    | 全局服务将会在你的每一个服务端实例中注册
    | 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
    |
    */
    'server_services' => [
        // 'user' => UserService::class
    ],

    /*
    |--------------------------------------------------------------------------
    | 服务端全局中间件
    |--------------------------------------------------------------------------
    |
    | 服务端全局中间件将会在你的每一个服务端实例中注册
    | 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
    |
    */
    'server_middleware' => [
        // LogMiddleware::class
    ],

    /*
    |--------------------------------------------------------------------------
    | 服务端全局过滤器
    |--------------------------------------------------------------------------
    |
    | 服务端全局过滤器将会在你的每一个服务端实例中注册，过滤器可以调整 RPC 底层输入输出
    |
    */
    'server_filters' => [
        \Gzoran\LaravelMicroService\Servers\Filters\EncryptFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | 客户端全局中间件
    |--------------------------------------------------------------------------
    |
    | 客户端全局中间件将会在你的每一个客户端端实例中注册
    |
    */
    'client_middleware' => [
        // LogMiddleware::class
    ],

    /*
    |--------------------------------------------------------------------------
    | 客户端中间件组
    |--------------------------------------------------------------------------
    |
    | 客户端中间件组可以方便批量注册多个中间件
    |
    */
    'client_middleware_groups' => [
        // 'monitoring' => [LogMiddleware::class],
    ],

    /*
    |--------------------------------------------------------------------------
    | 客户端全局过滤器
    |--------------------------------------------------------------------------
    |
    | 客户端全局过滤器将会在你的每一个客户端实例中注册，过滤器可以调整 RPC 底层输入输出
    |
    */
    'client_filters' => [
        \Gzoran\LaravelMicroService\Clients\Filters\EncryptFilter::class,
    ],
];