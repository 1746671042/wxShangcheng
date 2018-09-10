<?php

namespace app\api\model;

use app\api\model\BaseModel;

class BannerItem extends BaseModel
{
    //设置需要隐藏的字段
    protected $hidden= ['id','img_id','banner_id','update_time','delete_time'];
    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }
}
