<?php
namespace app\lib\exception;
use think\Exception;
//自定义异常处理类
class ProductException extends Exception{
    //HTTP 状态码  404/200
    public $code=404;
    //错误具体信息
    public $msg='指定商品不存在，请检查参数';
    //自定义的错误代码
    public $errorcode =20000;
    
   
}

