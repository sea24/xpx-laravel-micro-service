<?php

namespace Gzoran\LaravelMicroService\Clients\Schedulers;

use Gzoran\LaravelMicroService\Clients\Exceptions\EnableNodeNotFoundException;
use Gzoran\LaravelMicroService\Clients\Node;
use Gzoran\LaravelMicroService\Clients\Schedulers\SchedulerAbstract;

/**
 * 基础调度器
 * Class BaseScheduler
 *
 * @package Gzoran\LaravelMicroService\Schedulers
 */
class RandomScheduler extends SchedulerAbstract
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
            throw new EnableNodeNotFoundException("The [{$this->serverName}] has no enable node in list.");
        }

        return $nodes->random();
    }
}