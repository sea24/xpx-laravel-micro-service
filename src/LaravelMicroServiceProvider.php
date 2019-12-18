<?php

namespace Gzoran\LaravelMicroService;

use Gzoran\LaravelMicroService\Clients\Request;
use Gzoran\LaravelMicroService\Commands\LogoutServer;
use Gzoran\LaravelMicroService\Commands\RegisterServer;
use Gzoran\LaravelMicroService\Commands\ReportServer;
use Gzoran\LaravelMicroService\Commands\ServersList;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class LaravelMicroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 注册配置
        $this->mergeConfigFrom(
            __DIR__ . '/../config/microservice.php', 'microservice'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置
        $this->publishes([
            __DIR__ . '/../config/microservice.php' => config_path('microservice.php'),
        ]);

        // 注册命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                RegisterServer::class,
                LogoutServer::class,
                ReportServer::class,
                ServersList::class,
            ]);
        }

        // 初始化远程调用请求组 id
        Request::$groupId = str_replace('-', '', Str::uuid());

        // 队列内的请求需要重新生成组 id
        Queue::before(function (JobProcessing $event) {
            Request::$groupId = str_replace('-', '', Str::uuid());
        });
    }
}
