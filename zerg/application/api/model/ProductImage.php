<?php
namespace app\api\model;
use app\api\model\BaseModel;
class ProductImage extends BaseModel
{
     protected $hidden= ['img_id','delete_time','product_id'];
     //定义关联Image 表
     public function imgUrl(){
         return $this->belongsTo('Image','img_id','id');
     }
}
