<?php
namespace app\api\validate;

class IDCollection extends BaseValidate{
    protected  $rule=[
        'ids'=>'require|checkIDs',
    ];
    protected  $message = [
        'ids'=>"ids参数必须是以逗号分隔的多个正整数"
    ];
    //$value  是 ids = id1，id2
    protected  function checkIDs($value){
        $value = explode(',', $value);
        //判断传输的id 是否为空
        if(empty($value)){
            return false;
        }
        //判断每个id 是否为整数
        foreach($value as $id){
            if(!$this->isPositiveInteger($id)){
                return false;
            }
        }
        return true; 
    }
}