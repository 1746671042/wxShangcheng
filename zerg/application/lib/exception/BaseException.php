<?php
namespace app\lib\exception;
use think\exception\Handle;
use think\Exception;
//自定义异常处理类
class BaseException extends Exception{
    //HTTP 状态码  404/200
    public $code=400;
    //错误具体信息
    public $msg='参数错误';
    //自定义的错误代码
    public $errorcode =10000;
    
    //编写构造函数
    public function __construct($params=[]) {
       if(!is_array($params)){
           return ;
        //throw new Exception('参数必须是数组');
       }
       //首先判断参数是否存在
       if(array_key_exists('code', $params)){
          $this->code=$params['code']; 
       }
       if(array_key_exists('msg', $params)){
          $this->msg=$params['msg']; 
       }
       if(array_key_exists('errorCode', $params)){
          $this->errorCode=$params['errorCode']; 
       }
    }
}

