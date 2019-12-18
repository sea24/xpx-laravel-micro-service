<?php

namespace Gzoran\LaravelMicroService\Servers\Filters;

use Illuminate\Support\Arr;
use stdClass;

/**
 * 跟踪过滤器
 * Class TracerFilter
 *
 * @package Gzoran\LaravelMicroService\Servers\Filters
 */
class TracerFilter extends FilterAbstract
{
    /**
     * 启用状态
     *
     * @var bool
     */
    protected $enable;

    /**
     * 请求父 id
     *
     * @var string
     */
    public static $requestParentId;

    /**
     * 追踪数组
     *
     * @var array
     */
    public static $trace;

    /**
     * TracerFilter constructor.
     */
    public function __construct()
    {
        $this->enable = config('microservice.tracer.enable');
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return false|string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        if (!$this->enable || !$data = \json_decode($data, true)) {
            return $data;
        }

        static::$requestParentId = $data['id'];
        static::$trace = Arr::get($data, 'trace');

        return \json_encode($data);
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        if (!$this->enable || !$data = \json_decode($data, true)) {
            return $data;
        }

        if (static::$trace) {
            $data['trace'] = static::$trace;
            // HTTP 请求记录节点
            $scheme = request()->getScheme();
            if (in_array($scheme, ['http', 'https'])) {
                $data['trace']['node'] = [
                    'scheme' => $scheme,
                    'host' => request()->server('HTTP_HOST'),
                    'port' => request()->getPort(),
                    'path' => trim(request()->getPathInfo(), '/'),
                ];
            }
        }

        return \json_encode($data);
    }
}