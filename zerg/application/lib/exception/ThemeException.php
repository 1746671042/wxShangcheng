<?php
namespace app\lib\exception;

//自定义异常处理类
class ThemeException extends BaseException{
        public $code = 404;
        public $msg = '指定主题不存在，请检查主题ID';
        public $errorCode = 30000;
  
}
