<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
use think\Exception;
use think\Validate;
use think\Request;
class PagingParameter extends BaseValidate{

    protected  $rule=[
        'page'=>'isPositiveInteger',
        'size'=>'isPositiveInteger',
    ];
    protected  $message=[
        'page'=>'分页参数必须为正整数',
        'size'=>'分页参数必须为正整数',
    ];
}