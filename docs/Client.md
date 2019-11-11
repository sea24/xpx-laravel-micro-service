## 客户端（Client）

> 客户端（client）是访问某个服务端（server）的代理，如用户客户端（user client）可以对应着用户服务端（user server）。

### 定义客户端（Client）

- 你可以在任意目录定义客户端（client），只需要继承客户端抽象类（ClientAbstract）即可
- 你需要在客户端（client）定义服务端名称属性（serverName），服务端名称会作为服务端（server）标识，在向服务中心（service center）请求节点列表的时候使用

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
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

- 定义远程调用器（remote）

> 客户端（client）实际上是远程调用器（remote）的代理，
> 调用客户端（client）中的方法，都会通过 __call 魔术方法转发到远程调用器（remote），
> 这样做为客户端中间件（client middleware）和熔断器（circuit breaker）等中间层的实现提供了便利。

- 你可以在任意目录定义远程调用器（remote），只需要继承远程调用器抽象类（RemoteAbstract）即可

```php

namespace App\MicroService\Clients\Remotes;

use Gzoran\LaravelMicroService\Clients\Remotes\RemoteAbstract;

/**
 * 示例远程调用器
 * Class DemoRemote
 *
 * @package App\MicroService\Clients\Remotes
 */
class DemoRemote extends RemoteAbstract
{
    /**
     * 列表
     *
     * @param array $params
     * @return array
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function userIndex(array $params = [])
    {
        return $this->client()->user->index($params);
    }

    /**
     * 详情
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function userShow($id)
    {
        return $this->client()->user->show($id);
    }
}

```

- 将远程调用器（remote）配置到客户端（client）
- 重载客户端（client）的 remote 方法，并返回远程调用器（remote）

```php

namespace App\MicroService\Clients;

use App\MicroService\Clients\Remotes\DemoRemote;
use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    ···

    /**
     * 远程调用器
     *
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function remote()
    {
        return DemoRemote::class;
    }
    
    ···
}

```

- 客户端远程调用
- 配置好远程调用器（remote）之后，即可使用客户端（client）进行远程调用

```php

namespace App\Http\Controllers\Api;

use App\MicroService\Clients\DemoClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DemoController extends BaseController
{
    /**
     * @param Request $request
     * @param DemoClient $client
     * @return Response
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function userIndex(Request $request, DemoClient $client)
    {
        return $this->success($client->userIndex($request->all()));
    }

    /**
     * @param $id
     * @param DemoClient $client
     * @return Response
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function userShow($id, DemoClient $client)
    {
        return $this->success($client->userShow($id));
    }
}

```

### 设置客户端方法重试次数（Method Retry）

> 远程调用由于网络问题有可能会出现调用失败的情况，在这个时候，一些幂等类型（调用 N 次对数据无影响）的调用可以设置重试次数，再次发起调用，
> 默认情况下所有客户端方法调用失败时，都不会进行重试，除非你设置了该方法的重试次数

- 设置方法的重试次数，你只需要配置客户端（client）的 methodRetry 属性即可

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    /**
     * 方法重试次数
     *
     * @var array
     */
    protected $methodRetry = [
        // 方法名 => 重试次数
        'userIndex' => 3,
    ];
}

```

#### 方法重试注意事项

- 请确保你配置的方法是幂等方法，否则可能会因为网络问题进行重试时，给数据造成不可预测的影响
- 进行方法重试时，客户端可能无法捕获服务端的异常，这是因为调用失败时，客户端会切换节点重新请求，
当重试次数都用完时，客户端可以捕获到服务端异常，而当可用节点用完时，客户端会直接抛出无可用节点异常

## 客户端中间件（Client Middleware）

> 客户端中间件（client middleware）底层使用 Laravel Pipeline 实现，提供了对客户端（client）的输入输出处理，类似 Laravel 的中间件。

- 定义客户端中间件
- 你可以在任意目录定义客户端中间件，只需要继承客户端中间件抽象类（MiddlewareAbstract），并实现 handle 方法即可

```php

namespace App\MicroService\Clients\Middleware;

use Gzoran\LaravelMicroService\Clients\Middleware\MiddlewareAbstract;
use Gzoran\LaravelMicroService\Clients\Request;
use Closure;

class DemoMiddleware extends MiddlewareAbstract
{
    /**
     * @param Request $request
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

- 注册客户端中间件
- 你可以将客户端中间件（client middleware）配置到客户端（client）的 clientMiddleware 属性中

```php

namespace App\MicroService\Clients;

use App\MicroService\Clients\Middleware\DemoMiddleware;
use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 客户端中间件
     *
     * @var array
     */
    protected $clientMiddleware = [
        DemoMiddleware::class,
    ];
    
    ···
    
}

```

## 客户端方法中间件（Client Method Middleware）

> 你可能无法满足于对整个客户端（client）应用中间件，所以这里提供了方法中间件（method middleware）以满足更细颗粒度的需求

- 注册方法中间件
- 你可以将中间件配置到客户端（client）的 methodMiddleware 属性中

```php

namespace App\MicroService\Clients;

use App\MicroService\Clients\Middleware\DemoMiddleware;
use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    /**
     * 方法中间件
     *
     * @var array
     */
    protected $methodMiddleware = [
        // 中间件 => 方法数组
        DemoMiddleware::class => [
            'userIndex',
        ],
    ];
}

```

## 客户端全局中间件（Client Global Middleware）

> 如果你定义了多个客户端（client），每个客户端（client）都应用到了同一个中间件，那么客户端全局中间件将帮助你节省每个客户端（client）的配置步骤

- 注册客户端全局中间件
- 你可以在配置文件 microservice.php 的客户端全局中间件配置项 client_middleware 中注册你的中间件

```php

···

'client_middleware' => [
    \App\MicroService\Clients\Middleware\DemoMiddleware::class,
],

···

```

## 客户端中间件组（Client Middleware Group）

> 如果你想批量配置一些中间件，客户端中间件组（client middleware group）将帮助你节省配置步骤

- 注册一个中间件组
- 你可以在配置文件 microservice.php 的客户端中间件组配置项 client_middleware_groups 中注册你的中间件组

```php

'client_middleware_groups' => [
    'monitoring' => [
        \App\MicroService\Clients\Middleware\LogMiddleware::class,
    ],
],

```

- 应用中间件组

```php

namespace App\MicroService\Clients;

use App\MicroService\Clients\Middleware\DemoMiddleware;
use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 客户端中间件
     *
     * @var array
     */
    protected $clientMiddleware = [
        'monitoring',
    ];
    
    ···
    
}

```

## 客户端熔断器（Client Circuit Breaker）

> 分布式系统中经常会出现由于某个基础服务不可用造成整个系统不可用的情况，这种现象被称为服务雪崩效应,
> 为了应对服务雪崩，一种常见的做法是服务降级，客户端熔断器（client circuit breaker）就是为了解决这个问题,
> 熔断器（circuit breaker）实际上相当于电路中的保险丝，起着保护电路的作用。

- 拓展包默认为每个客户端注册了一个基础熔断器（BaseCircuitBreaker）
- 基础熔断器（BaseCircuitBreaker）在远程调用抛出异常时触发熔断，并回调客户端（client）中的降级方法，如果没有降级方法，则抛出异常
- 默认的降级方法是调用方法拼接上降级方法后缀（Fallback）
- 下面定义一个降级方法

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\Request;
use Gzoran\LaravelMicroService\Clients\ClientAbstract;
use Illuminate\Support\Facades\Cache;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 服务降级方法
     *
     * @param \Exception $exception
     * @param Request $request
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function userIndexFallback(\Exception $exception, Request $request)
    {
        // 你可以在这里返回已缓存的数据以保证调用的连贯性
        return Cache::get('user.index');
    }
    
    ···
    
}

```

- 自定义降级方法后缀
- 只需要在客户端（client）中重载 clientCircuitBreaker 属性即可

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 降级方法后缀
     *
     * @var string
     */
    protected $failbackPostfix = 'YourFallback';
    
    ···
    
}

```

- 自定义熔断器
- 你可以在任意目录定义熔断器（circuit breaker），只需要继承熔断器抽象类（CircuitBreakerAbstract）并实现 process 方法即可

```php

namespace App\MicroService\Clients\CircuitBreakers;
use Gzoran\LaravelMicroService\Clients\CircuitBreakers\CircuitBreakerAbstract;
use Closure;

/**
 * 自定义熔断器
 * Class FooCircuitBreaker
 *
 * @package App\MicroServices\CircuitBreakers
 */
class FooCircuitBreaker extends CircuitBreakerAbstract
{
    /**
     * @param Closure $process
     * @param Closure|null $failback
     * @return mixed
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function process(Closure $process, Closure $failback = null)
    {
        try {
            return $process();
        } catch (\Exception $exception) {
            if (!$failback) {
                throw $exception;
            }
            logs()->warning('Base circuit breaker failback! Service name:' . $this->request->getServerName() . '; Invoke:' . $this->request->getClass() . '::' . $this->request->getMethod());
            return $failback($exception);
        }
    }
}

```

- 注册自定义熔断器
- 你可以在客户端（client）的熔断器属性（clientCircuitBreaker）中注册自定义的熔断器即可

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;
use App\MicroService\Clients\CircuitBreakers\FooCircuitBreaker;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 客户端熔断器
     *
     * @var string
     */
    protected $clientCircuitBreaker = FooCircuitBreaker::class;
    
    ···
    
}

```

## 客户端方法熔断器（Client Method Circuit Breaker）

> 方法熔断器（method circuit breaker）提供了针对方法颗粒度的熔断逻辑

- 注册客户端方法熔断器
- 你只需要将熔断器配置在客户端（client）方法熔断器属性（methodCircuitBreakers）中即可

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;
use App\MicroService\Clients\CircuitBreakers\FooCircuitBreaker;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 方法熔断器
     *
     * @var array
     */
    protected $methodCircuitBreakers = [
        // 方法 => 熔断器
        'index' => FooCircuitBreaker::class,
    ];
    
    ···
    
}

```

## 调度器（Scheduler）

> 调度器（scheduler）是管理节点的工具，它可以从服务中心获取节点列表，并根据相应策略选取合适的节点提供给远程调用器（remote）使用
> 客户端抽象类（client abstract）中的 scheduler 方法配置了默认的随机策略调度器（RandomScheduler）

- 自定义调度器
- 你可以在任意目录定义调度器（scheduler），只需要继承调度器抽象类（SchedulerAbstract）并实现 policy 方法即可

```php

namespace App\MicroService\Clients\Schedulers;
use Gzoran\LaravelMicroService\Clients\Exceptions\EnableNodeNotFoundException;
use Gzoran\LaravelMicroService\Clients\Node;
use Gzoran\LaravelMicroService\Clients\Schedulers\SchedulerAbstract;

/**
 * 自定义调度器
 * Class FooScheduler
 *
 * @package App\MicroServices\Schedulers
 */
class FooScheduler extends SchedulerAbstract
{
    /**
     * 节点被标记为不可用时，达到的失败次数（含）
     *
     * @var int
     */
    protected $disableFailCounter = 1;
    
    /**
     * 调度策略
     *
     * @return Node
     * @throws EnableNodeNotFoundException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function policy() : Node
    {
        // 失败次数大于阈值的节点标记为不可用
        foreach ($this->nodes as $node) {
            /**
             * @var Node $node
             */
            if ($node->getFailCounter() >= $this->disableFailCounter) {
                $node->disable();
            }
        }
        $nodes = collect($this->nodes)->filter(function (Node $node) {
            return $node->getStatus() == true;
        });
        if ($nodes->isEmpty()) {
            throw new EnableNodeNotFoundException('There are no enable node in list.');
        }
        return $nodes->random();
    }
}

```

- 注册自定义调度器
- 你可以在客户端重载 scheduler 方法以注册你的调度器

```php

namespace App\MicroService\Clients;

use Gzoran\LaravelMicroService\Clients\ClientAbstract;
use App\MicroService\Clients\Schedulers\FooScheduler;

/**
 * 示例客户端
 * Class DemoClient
 *
 * @method userIndex(array $all)
 * @method userShow($id)
 */
class DemoClient extends ClientAbstract
{
    
    ···
    
    /**
     * 设置调度器
     *
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function scheduler()
    {
        return FooScheduler::class;
    }
    
    ···
    
}

```

### 异常处理（Exception）

> 执行远程调用如果发生了异常，在客户端只可以捕获异常基类 \Exception::class，且只有 message 属性是服务端传递过来的
> 所以拓展包通过服务端异常序列化中间件 ExceptionSerializeMiddleware::class 将异常的 message 属性转换成了结构化
> 的 json 数据，方便客户端进行相应的异常处理

- 远程调用异常 RemoteInvokeException::class

客户端在执行远程调用时，服务端若出现异常，客户端会抛出 RemoteInvokeException::class 异常，该异常的 message 属性是结构化的 json 数据

```json

{
  "exception": "Exception",
  "message": "We try to throw a exception in index",
  "code": 0,
  "file": "/home/vagrant/Code/xiao-pu-xiong/xpx-bis-sass-sys/projects/demo-service/www/demo-service/app/MicroService/Services/UserService.php",
  "line": 41,
  "from": {
    "server_name": "demo_server",
    "service_name": "user_index"
  }
}

```

> 你可以根据结构化的 json 数据追踪和定位问题

- RemoteInvokeException 提供的方法

```php

// 获取消息数组
RemoteInvokeException::getMessageArray
// 获取远程异常消息
RemoteInvokeException::getRemoteMessage
// 获取远程异常码
RemoteInvokeException::getRemoteCode
// 获取远程异常详情
RemoteInvokeException::getRemoteErrors
// 获取远程异常文件
RemoteInvokeException::getRemoteFile
// 获取远程异常行号
RemoteInvokeException::getRemoteLine
// 获取远程异常来源
RemoteInvokeException::getRemoteFrom

```

#### 注意事项

- 如果方配置了重试，客户端可能无法捕获到 RemoteInvokeException::class，原因上文已提及
- 注意区分好异常的责任边界，若无需客户端处理的异常，请继续向上抛出，否则，末端的调用者将很难定位到真正的问题