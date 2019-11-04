<?php

namespace Gzoran\LaravelMicroService;

use Gzoran\LaravelMicroService\Commands\RegisterServer;
use Illuminate\Support\ServiceProvider;

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
            __DIR__ . '/microservice.php', 'microservice'
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
            __DIR__ . '/microservice.php' => config_path('microservice.php'),
        ]);

        // 注册命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                RegisterServer::class,
            ]);
        }
    }
}
