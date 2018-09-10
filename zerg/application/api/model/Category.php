<?php

namespace app\api\model;
use think\Model;
class Category extends BaseModel
{
    //设置需要隐藏的字段
    protected $hidden= ['create_time','update_time','delete_time'];
    //关联
    public function img(){
        return $this->belongsTo("Image","topic_img_id","id");
    }
}
