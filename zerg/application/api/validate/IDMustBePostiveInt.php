<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
use think\Validate;
//自动继承验证规则
class IDMustBePostiveInt extends BaseValidate{
    
    protected $rule=[
        'id'=>"require|isPositiveInteger",
    ];
    protected  $message = [
        'id'=>"id必须为正整数"
    ];

}
