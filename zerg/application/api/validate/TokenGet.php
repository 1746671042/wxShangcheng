<?php
namespace app\api\validate;

class TokenGet extends BaseValidate{
    protected  $rule=[
       'code'=>'require|isNotEmpty'
   ];
   protected  $message=[
       'code'=>'对不起，您没有code,无法获取token',
   ];
}