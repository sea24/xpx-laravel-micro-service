<?php

namespace Gzoran\LaravelMicroService\Clients\Filters;

use Gzoran\LaravelMicroService\Client\Filters\FilterAbstract;
use Gzoran\LaravelMicroService\Clients\Contracts\ServiceCenterDriverContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\Request;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\LocalServiceCenterDriver;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\RemoteServiceCenterDriver;
use Gzoran\LaravelMicroService\Servers\Filters\TracerFilter as ServerTracerFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use stdClass;

/**
 * 跟踪过滤器
 * Class TracerFilter
 *
 * @package Gzoran\LaravelMicroService\Clients\Filters
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
     * @var ServiceCenterDriverContract
     */
    protected $serviceCenterDriver;

    /**
     * 排除的服务跟踪
     *
     * @var array
     */
    protected $except = [
        'service_center_server:trace*',
    ];

    /**
     * TracerFilter constructor.
     * @throws ClientException
     */
    public function __construct()
    {
        $this->enable = config('microservice.tracer.enable');
        $this->except = config('microservice.tracer.except');

        // 服务中心驱动
        switch ($driver = config('microservice.service_center_driver.default', 'local')) {
            case 'remote':
                $driverClass = config('microservice.service_center_driver.remote', RemoteServiceCenterDriver::class);
                break;
            case 'local':
                $driverClass = config('microservice.service_center_driver.local', LocalServiceCenterDriver::class);
                break;
            default:
                throw new ClientException('Cat not match service center driver like:' . $driver);
        }

        $this->serviceCenterDriver = new $driverClass;
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        if (!$this->enable || !$data = \json_decode($data, true)) {
            return $data;
        }

        if (isset($data['trace'])) {
            // 计算响应时间
            $data['trace']['responded_at'] = date('Y-m-d H:i:s');
            $data['trace']['response_millisecond'] = $this->millisecond();
            $data['trace']['response_time'] = $data['trace']['response_millisecond'] - $data['trace']['request_millisecond'];
        }

        $data = \json_encode($data);

        $this->traceResponse($data);

        return $data;
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return false|string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        if (!$this->enable || !$data = \json_decode($data, true)) {
            return $data;
        }

        // 追踪标识
        $data['trace'] = [
            'parent_id' => ServerTracerFilter::$requestParentId,
            'server_name' => Request::$tempServerName,
            'method' => $data['method'],
            'requested_from_app' => config('app.name'),
            'requested_at' => date('Y-m-d H:i:s'),
            'request_millisecond' => $this->millisecond(),
        ];

        $data = \json_encode($data);

        $this->traceRequest($data);

        return $data;
    }

    /**
     * @return int
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function millisecond()
    {
        list($mSec, $sec) = explode(' ', microtime());

        return (int)sprintf('%.0f', (floatval($mSec) + floatval($sec)) * 1000);
    }

    /**
     * @param string $jsonRpc
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function traceRequest(string $jsonRpc)
    {
        if ($this->shouldPassThrough($jsonRpc)) {
            return;
        }

        $this->serviceCenterDriver->traceRequest($jsonRpc);
    }

    /**
     * @param string $jsonRpc
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function traceResponse(string $jsonRpc)
    {
        if ($this->shouldPassThrough($jsonRpc)) {
            return;
        }

        $this->serviceCenterDriver->traceResponse($jsonRpc);
    }

    /**
     * @param string $jsonRpc
     * @return bool
     * @author Mike <zhengzhe94@gmail.com>
     */
    protected function shouldPassThrough(string $jsonRpc)
    {
        try {
            $data = \json_decode($jsonRpc, true);

            $serverName = Arr::get($data, 'trace.server_name');
            $method = Arr::get($data, 'trace.method');

            return collect($this->except)->contains(function ($name) use ($serverName, $method) {
                return Str::is($name, "{$serverName}:{$method}");
            });
        } catch (\Exception $exception) {
            // 此处异常不用抛出，记录日志即可，跟踪服务不应该阻断正常业务
            report($exception);
        }
    }
}