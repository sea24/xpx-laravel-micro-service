<?php

namespace Gzoran\LaravelMicroService\Servers\Filters;

use Gzoran\LaravelMicroService\Exceptions\TransportDecryptException;
use Hprose\Filter;
use Illuminate\Contracts\Encryption\DecryptException;
use stdClass;

/**
 * 加密过滤器
 * Class EncryptFilter
 *
 * @package Gzoran\LaravelMicroService\Servers\Filters
 */
class EncryptFilter implements Filter
{
    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @throws TransportDecryptException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        try {
            return decrypt($data);
        } catch (DecryptException $exception) {
            throw new TransportDecryptException($exception->getMessage());
        }
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return mixed
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        return encrypt($data);
    }
}