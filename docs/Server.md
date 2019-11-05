## 服务端（Server）

> 定义服务端（server）和发布的服务 (service) 可以为客户端（client）调用提供支持。

### 定义服务（Service）

> 服务（service）是提供某类业务调用的集合，如用户服务等；一个服务端（server）可注册多个服务（service）,
> 一个服务（service）里可提供多个方法（method），方法（method）包含了业务的具体实现。

- 你可以在任意目录下定义服务类，只需要继承服务抽象类（ServiceAbstract）即可
- 确保你要发布的方法为公共方法，一个服务类里，只有公共方法才会被发布出去

```php

namespace App\MicroService\Services;

use Gzoran\LaravelMicroService\Services\ServiceAbstract;

/**
 * 用户服务
 * Class ServiceCenterService
 *
 * @package App\MicroService\Services
 */
class UserService extends ServiceAbstract
{
    ···
}

```

### 定义服务端（Server）

> 定义服务端（Server）为客户端提供 RPC 服务，服务端支持 HTTP 和 Socket 两种方式，但是由于 Hprose PHP Socket
> 存在不稳定的情况，这里推荐使用 HTTP 方式。

- 你可以在任意目录下定义 HTTP 服务端，只需要继承拓展包提供的 HTTP Server 基类即可
- 你需要定义服务端名称属性（serverName），该名称作为唯一标识在服务中心中注册

```php

namespace App\MicroService\Servers;

use Gzoran\LaravelMicroService\Servers\Http\Server;

/**
 * 服务中心服务端
 * Class ServiceCenterServer
 *
 * @package App\MicroService\Servers
 */
class DemoServer extends Server
{
    /**
     * 服务端名称，在服务中心中唯一注册
     *
     * @var string
     */
    protected $serverName = 'demo_server';
    
    ···
}

```

- 配置框架路由及控制器
- 你需要将 HTTP Server 的 start 方法在控制器中调用，使 RPC 服务能通过 HTTP 进行访问
- 访问地址称为该服务端的节点（Node）

```php

namespace App\Http\Controllers\Rpc;

use App\MicroService\Servers\DemoServer;
use Illuminate\Http\Request;

/**
 * Demo 服务端 RPC 控制器
 * Class ServiceController
 *
 * @package App\Http\Controllers\Rpc
 */
class DemoController extends BaseController
{
    /**
     * @param Request $request
     * @param DemoServer $server
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function index(Request $request, DemoServer $server)
    {
        // 通过容器将 Server 注入，并调用 start
        $server->start();
    }
}

```

### 发布服务（Publish Service）

> 服务（service）通关服务端（server）发布后，即可被客户端（client）调用

- 将服务（service）注册到服务端（server）的 services 属性中
- 服务（service）的所有公共方法将会被发布，确保你要发布的方法被正确声明

```php

namespace App\MicroService\Servers;

use App\MicroService\Services\UserService;
use Gzoran\LaravelMicroService\Servers\Http\Server;

/**
 * 服务中心服务端
 * Class ServiceCenterServer
 *
 * @package App\MicroService\Servers
 */
class DemoServer extends Server
{
    ···

    /**
     * 注册服务
     *
     * @var array
     */
    protected $services = [
        // 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
        'user' => UserService::class,
    ];
    
    ···
}

```

- 发布全局服务
- 如果你定义了多个服务端并使用了相同的服务，你可以将服务全局发布
- 你可以在配置文件 microservice.php 的全局服务配置项 server_services 中发布你的服务

```php

···

'server_services' => [
    // 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
    'user' => UserService::class
],

···

```

### 服务端中间件（Server Middleware）

> 服务端中间件（server middleware）底层使用 Laravel Pipeline 实现，提供了对服务端（server）的输入输出处理，类似 Laravel 的中间件。

- 定义一个服务端中间件
- 你可以在任意目录定义中间件，只需要继承服务端中间件抽象类（MiddlewareAbstract），并实现 handle 方法即可

```php

namespace App\MicroService\Servers\Middleware;
use Gzoran\LaravelMicroService\Servers\Middleware\MiddlewareAbstract;
use Gzoran\LaravelMicroService\Servers\Request;
use Closure;

/**
 * 日志中间件
 * Class LogMiddleware
 *
 * @package App\MicroServices\Supports\Servers\Middleware
 */
class LogMiddleware extends MiddlewareAbstract
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}

```

- 注册服务端中间件
- 你可以在服务端（server）中的 middleware 属性中注册中间件

```php

namespace App\MicroService\Servers;

use App\MicroService\Servers\Middleware\LogMiddleware;

/**
 * 服务中心服务端
 * Class ServiceCenterServer
 *
 * @package App\MicroService\Servers
 */
class DemoServer extends Server
{
    ···
    
    /**
     * 中间件
     *
     * @var array
     */
    protected $middleware = [
        LogMiddleware::class,
    ];
    
    ···
}

```

### 服务端全局中间件（Server Global Middleware）

- 定义服务端全局中间件
- 如果你定义了多个服务端并使用了相同的中间件，你可以将中间件全局注册
- 你可以在配置文件 microservice.php 的服务端全局中间件配置项 server_middleware 中注册你的中间件

```php

···

'server_middleware' => [
    \App\MicroService\Servers\Middleware\LogMiddleware\LogMiddleware::class,
],

···

```

#### 拓展包默认注册的服务端全局中间件

- 异常序列化 ExceptionSerializeMiddleware::class

> 序列化异常信息，让客户端更好的追踪定位异常，后面会在客户端章节中介绍

### 服务端全局过滤器

> 过滤器提供了对服务端的底层 RPC 数据输入输出的处理

- 定义服务端全局过滤器
- 你可以在任意目录定义过滤器，只需要继承服务端过滤器抽象类（FilterAbstract），并实现 inputFilter 和 outputFilter 即可

```php

namespace App\MicroService\Servers\Filters;

use Gzoran\LaravelMicroService\Servers\Filters\FilterAbstract;
use stdClass;

/**
 * 日志过滤器
 * Class LogFilter
 *
 * @package App\MicroService\Servers\Filters
 */
class LogFilter extends FilterAbstract
{
    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        return $data;
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        return $data;
    }
}

```

- 注册服务端全局过滤器
- 你可以在配置文件 microservice.php 的服务端全局过滤器配置项 server_filters 中注册你的过滤器

```php

···

'server_filters' => [
    \App\MicroService\Servers\Filters\LogFilter::class,
],

···

```

#### 拓展包默认注册的服务端全局过滤器

- 加密过滤器 EncryptFilter::class

> 服务端通讯加密使用了 Laravel 的对称加密方法 encrypt，该方法加密机制使用的是 OpenSSL 所提供的 AES-256 和 AES-128 加密
> 加密之后的结果都会使用消息认证码 (MAC) 签名，使其底层值不能在加密后再次修改，请保证每个应用 config/app.php 中的 key 被正确设置,
> 客户端相应的加密过滤器（EncryptFilter）会继承它的实现，所以你的修改能同时被客户端所应用。