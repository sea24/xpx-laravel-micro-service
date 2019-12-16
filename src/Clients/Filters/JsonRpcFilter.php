<?php

namespace Gzoran\LaravelMicroService\Clients\Filters;

use Gzoran\LaravelMicroService\Client\Filters\FilterAbstract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Hprose\BytesIO;
use Hprose\Reader;
use Hprose\Tags;
use Hprose\Writer;
use Illuminate\Support\Str;
use stdClass;

/**
 * JsonRpc 协议转换过滤器
 * Class JsonRpcFilter
 *
 * @package Gzoran\LaravelMicroService\Clients\Filters
 */
class JsonRpcFilter extends FilterAbstract
{
    /**
     * @param $data
     * @param stdClass $context
     * @return string
     * @throws \Exception
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function inputFilter($data, stdClass $context)
    {
        $response = json_decode($data);

        if (!isset($response->result)) {
            $response->result = null;
        }
        if (!isset($response->error)) {
            $response->error = null;
        }

        $stream = new BytesIO();
        $writer = new Writer($stream, true);

        if ($response->error) {
            $stream->write(Tags::TagError);
            $writer->writeString($response->error->message);
        } else {
            $stream->write(Tags::TagResult);
            $writer->serialize($response->result);
        }

        $stream->write(Tags::TagEnd);

        return $stream->toString();
    }

    /**
     * @param $data
     * @param stdClass $context
     * @return string
     * @throws ClientException
     * @author Mike <zhengzhe94@gmail.com>
     */
    public function outputFilter($data, stdClass $context)
    {
        $request = new stdClass();
        $request->jsonrpc = '2.0';

        $stream = new BytesIO($data);
        $reader = new Reader($stream);

        $tag = $stream->getc();

        if ($tag !== Tags::TagCall) {
            throw new ClientException('Error processing request.');
        }

        $request->method = $reader->readString();
        $tag = $stream->getc();
        if ($tag == Tags::TagList) {
            $reader->reset();
            $request->params = $reader->readListWithoutTag();
        }

        $request->id = str_replace('-', '', Str::uuid());

        $data = json_encode($request);

        return $data;
    }
}