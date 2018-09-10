<?php
namespace app\lib\exception;

//自定义异常处理类
class SuccessMessage{
    //当地址更新成功或者添加成功后返回消息
    //HTTP 状态码  404/200
    public $code=201;
    //错误具体信息
    public $msg='ok';
    //自定义的错误代码
    public $errorcode =0;
    
}

