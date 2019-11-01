<?php

namespace Gzoran\LaravelMicroService\Servers;

use stdClass;

/**
 * 服务端请求类
 * Class Request
 *
 * @package Gzoran\LaravelMicroService\Servers
 */
class Request
{
    /**
     * 具体服务名称
     *
     * @var string
     */
    protected $name;

    /**
     * 参数
     *
     * @var array
     */
    public $args;

    /**
     * 上下文
     *
     * @var stdClass
     */
    protected $context;

    /**
     * Request constructor.
     * @param string $name
     * @param array $args
     * @param stdClass $context
     */
    public function __construct(string $name, array &$args, stdClass $context)
    {
        $this->name = $name;
        $this->args = $args;
        $this->context = $context;
    }

    /**
     * @return string
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return stdClass
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function getContext()
    {
        return $this->context;
    }
}