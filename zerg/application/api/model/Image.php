<?php
namespace app\api\model;
use think\Model;
use app\api\model\BaseModel;
class Image extends BaseModel
{
   //设置需要隐藏的字段
    protected $hidden= ['id','from','update_time','delete_time'];
    //自动获取url 字段，并处理
    public function getUrlAttr($value,$data){
        //调用子类
        return $this->prefixImgUrl($value,$data);
    }
}
