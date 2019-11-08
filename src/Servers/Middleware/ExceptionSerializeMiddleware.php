<?php

namespace Gzoran\LaravelMicroService\Servers\Middleware;

use Closure;
use Gzoran\LaravelMicroService\Servers\Request;

/**
 * 异常序列化中间件
 * Class ExceptionSerializeMiddleware
 *
 * @package Gzoran\LaravelMicroService\Servers\Middleware
 */
class ExceptionSerializeMiddleware extends MiddlewareAbstract
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (\Exception $exception) {

            // 将异常记录到日志
            report($exception);

            // 先检查一下 message 是否可以 json_decode，避免多个服务相互调用时出现多层嵌套
            if ($messageToArray = \json_decode($exception->getMessage())) {
                throw new \Exception($exception->getMessage());
            }

            $message =[
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'errors' => [],
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'from' => [
                    'server_name' => $request->getServerName(),
                    'service_name' => $request->getServiceName(),
                ],
            ];

            if (method_exists($exception, 'errors')) {
                $errors = $exception->errors();
                if (is_string($errors) || is_array($errors)) {
                    $message['errors'] = $exception->errors();
                }
            }

            throw new \Exception(\json_encode($message));
        }

        return $response;
    }
}