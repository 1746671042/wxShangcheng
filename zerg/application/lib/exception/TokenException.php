<?php
namespace app\lib\exception;

//自定义异常处理类
class TokenException extends BaseException{
    //token异常
        public $code = 401;
        public $msg = 'Token 已过期或无效';
        public $errorCode = 10001;
  
}
