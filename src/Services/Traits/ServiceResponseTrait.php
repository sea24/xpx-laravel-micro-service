<?php

namespace Gzoran\LaravelMicroService\Services\Traits;

/**
 * 服务返回
 * Trait ServiceResponseTrait
 *
 * @package Gzoran\LaravelMicroService\Services
 */
trait ServiceResponseTrait
{
    /**
     * 成功返回
     *
     * @param null $data
     * @param array $extra
     * @return array|null
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function success($data = null, array $extra = [])
    {
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }
        }

        if (!is_array($data)) {
            $data = (array)$data;
        }

        if (isset($data['code']) && isset($data['data'])) {
            return $data;
        }

        $response = [
            'code' => 0,
            'message' => 'ok',
            'data' => $data,
        ];

        $response = array_merge($response, $extra);

        return $response;
    }

    /**
     * 失败返回
     *
     * @param string $message
     * @param int $code
     * @param null $data
     * @param array $extra
     * @return array|null
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function failure(string $message = 'error', int $code = 1, $data = null, array $extra = [])
    {
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }
        }

        if (!is_array($data)) {
            $data = (array)$data;
        }

        if (isset($data['code']) && isset($data['data'])) {
            return $data;
        }

        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        $response = array_merge($response, $extra);

        return $response;
    }
}