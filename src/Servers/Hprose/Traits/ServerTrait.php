<?php

namespace Gzoran\LaravelMicroService\Servers\Hprose\Traits;

/**
 * Server 公共方法
 * Trait ServerTrait
 *
 * @package Gzoran\LaravelMicroService\Servers\Hprose\Traits
 */
trait ServerTrait
{
    /**
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function registerErrorHandler() {
        $this->errorTypes = error_reporting();
        register_shutdown_function(array($this, 'fatalErrorHandler'));
        // 注释这个方法，以防止服务端中间件不能捕获 ErrorException
        // self::$lastErrorHandler = set_error_handler(array($this, 'errorHandler'), $this->errorTypes);
    }
}