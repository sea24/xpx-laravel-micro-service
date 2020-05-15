<?php

namespace Gzoran\LaravelMicroService\Servers\Filters;

use Gzoran\LaravelMicroService\Servers\Exceptions\ServerException;
use Hprose\BytesIO;
use Hprose\Reader;
use Hprose\Tags;
use Hprose\Writer;
use Illuminate\Support\Arr;
use stdClass;

/**
 * JsonRpc 协议转换过滤器
 * Class JsonRpcFilter
 *
 * @package Gzoran\LaravelMicroService\Servers\Filters
 */
class JsonRpcFilter extends FilterAbstract
{
    /**
     * @param $data
     * @param stdClass $context
     * @return string
     * @throws ServerException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        $requests = json_decode($data, true);

        if ($data[0] === '{') {
            $requests = [$requests];
        }

        $stream = new BytesIO();
        $writer = new Writer($stream, true);

        $context->userdata->jsonrpc = [];

        foreach ($requests as $request) {
            $jsonrpc = new stdClass();
            $jsonrpc->id = Arr::get($request, 'id');
            if ($request['jsonrpc'] != '2.0') {
                throw new ServerException('Only support JSON-RPC 2.0.');
            }
            $jsonrpc->version = '2.0';
            $context->userdata->jsonrpc[] = $jsonrpc;
            if (!isset($request['method'])) {
                unset($context->userdata->jsonrpc);
                return $data;
            }
            $stream->write(Tags::TagCall);
            $writer->writeString($request['method']);
            if (isset($request['params']) && count($request['params']) > 0) {
                $writer->writeArray($request['params']);
            }
        }
        $stream->write(Tags::TagEnd);
        $data = $stream->toString();
        unset($stream, $writer);

        return $data;
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return false|string
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        $responses = [];
        $stream = new BytesIO($data);
        $reader = new Reader($stream);
        $tag = $stream->getc();
        foreach ($context->userdata->jsonrpc as $jsonrpc) {
            $response = new stdClass();
            $response->id = $jsonrpc->id;
            $response->jsonrpc = $jsonrpc->version;
            if ($tag !== Tags::TagEnd) {
                $reader->reset();
                if ($tag === Tags::TagResult) {
                    $response->result = $reader->unserialize();
                } else if ($tag === Tags::TagError) {
                    $lasterror = error_get_last();
                    $response->error = new stdClass();
                    $response->error->code = $lasterror['type'];
                    $response->error->message = $reader->unserialize();
                }
                $tag = $stream->getc();
            } else {
                $response->result = null;
            }
            if ($response->id !== null) {
                $responses[] = $response;
            }
        }
        if (count($context->userdata->jsonrpc) === 1) {
            if (count($responses) === 1) {
                return json_encode($responses[0]);
            }
            return '';
        }

        return json_encode($responses);
    }
}