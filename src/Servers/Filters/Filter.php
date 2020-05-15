<?php
namespace Gzoran\LaravelMicroService\Servers\Filters;
use Hprose\Filter as BaseFilter;
use \Hprose\Tags;
use \Hprose\BytesIO;
use \Hprose\Reader;
/**
 * 将输出的结果转成json然后des-ecb加密 压缩传输
 */
class Filter implements BaseFilter{
   //加密密钥
   private $key="gum4qAO6W7Jqssz7rPaCBq36CLjmP1nh";

   //解密
   public function inputFilter($data,\stdClass $context){
       //解密
       //$data=openssl_decrypt($data,"DES-ECB",$this->key);
       $result=$data;
       //异常错误处理
       if($result[0] === Tags::TagError){
           $stream = new BytesIO($result);
           $reader = new Reader($stream);
           //下标进1
           $stream->skip(1);
           //如果服务端抛出的错误信息是数字
           //会原样返回
           //反而是字符串的情况下会返回json字符串
           $error=$reader->readString();
           $tmp=json_decode($error,1);
           if(is_array($tmp)){
               $error=$tmp['message'] ?? '服务错误';
           }
           throw new \Exception($error);
       }
       return $result;
   }

   /**
    * 加密
    */
   public function outputFilter($data,\stdClass $context){
       return $data;
       //加密
       //return \openssl_encrypt(\json_encode($data),"DES-ECB",$this->key);
   }
}