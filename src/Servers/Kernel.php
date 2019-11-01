<?php

namespace Gzoran\LaravelMicroService\Servers;

class Kernel
{
    /**
     * 注册全局服务
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function services()
    {
        // 数组的 key 为服务名前缀，用以区分不同服务类的相同方法，单词用下划线分割
        return [
            //
        ];
    }

    /**
     * 全局中间件
     *
     * @return array
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function middleware()
    {
        return [
            //
        ];
    }
}