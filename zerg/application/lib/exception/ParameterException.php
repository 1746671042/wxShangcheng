<?php
namespace app\lib\exception;

class ParameterException extends BaseException {
    //设置默认值
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;
}

