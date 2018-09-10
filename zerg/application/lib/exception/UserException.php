<?php
namespace app\lib\exception;
use think\exception\Handle;
use app\lib\exception\BaseException;
//自定义异常处理类
class UserException extends BaseException{
    //HTTP 状态码  404/200
    public $code=404;
    //错误具体信息
    public $msg='用户不存在';
    //自定义的错误代码
    public $errorCode =60000;
}

