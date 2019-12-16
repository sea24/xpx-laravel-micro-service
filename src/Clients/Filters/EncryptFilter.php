<?php

namespace Gzoran\LaravelMicroService\Clients\Filters;

use Gzoran\LaravelMicroService\Client\Filters\FilterAbstract;
use Gzoran\LaravelMicroService\Exceptions\TransportDecryptException;
use Illuminate\Contracts\Encryption\DecryptException;
use stdClass;

/**
 * 加密过滤器
 * Class EncryptFilter
 *
 * @package Gzoran\LaravelMicroService\Clients\Filters
 */
class EncryptFilter extends FilterAbstract
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