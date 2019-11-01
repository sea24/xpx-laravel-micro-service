<?php

namespace Gzoran\LaravelMicroService\Clients\Contracts;

use Gzoran\LaravelMicroService\Clients\Request;

/**
 * 远程调用器接口
 * Interface RemoteContract
 *
 * @package Gzoran\LaravelMicroService\Contracts
 */
interface RemoteContract
{
    /**
     * 调用
     *
     * @param Request $request
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function invoke(Request $request);

    /**
     * 设置调度器
     *
     * @param SchedulerContract $scheduler
     * @return RemoteContract
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function setScheduler(SchedulerContract $scheduler);
}