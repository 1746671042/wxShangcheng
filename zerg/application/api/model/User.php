<?php

namespace app\api\model;

use think\Model;

class User extends BaseModel
{
     //定义关联信息
    public function address(){
        return $this->hasOne("UserAddress","user_id","id");
    }
    
    //判断openid 是否存在
    public static function getByOpenId($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }
    
   
}
