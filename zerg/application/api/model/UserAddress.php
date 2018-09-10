<?php

namespace app\api\model;

use think\Model;

class UserAddress extends BaseModel
{
   //定义隐藏信息
   protected  $hidden=['id','delete_time','user_id'];
}
