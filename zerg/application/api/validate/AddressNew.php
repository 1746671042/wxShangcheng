<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class AddressNew extends BaseValidate{
   public $rule = [
       'name'=>'require|isNotEmpty',
       'mobile'=>'require|isMobile',
       'province'=>'require|isNotEmpty',
       'city'=>'require|isNotEmpty',
       'country'=>'require|isNotEmpty',
       'detail'=>'require|isNotEmpty',
   ];
}