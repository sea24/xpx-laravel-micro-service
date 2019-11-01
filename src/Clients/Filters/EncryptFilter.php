<?php

namespace Gzoran\LaravelMicroService\Clients\Filters;

use Gzoran\LaravelMicroService\Servers\Filters\EncryptFilter as ServerEncryptFilter;

/**
 * 加密过滤器
 * Class EncryptFilter
 *
 * @package Gzoran\LaravelMicroService\Clients\Filters
 */
class EncryptFilter extends ServerEncryptFilter
{
    // 继承了服务端的过滤器，使得两端加解密方法相同
}