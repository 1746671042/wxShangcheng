<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    //
    protected function prefixImgUrl($value,$data){   //获取图片路径 拼接路径 banner-2a.png
        //判断是本地还是远程
        $finalUrl = $value;
        if($data['from'] ==1){
            $finalUrl =  config('setting.img_prefix').$value; //"http://wxshop.com/images/banner-2a.png
        }
            return $finalUrl;   
    }
    
    
//    tp 遵循PSR-4 PSR-0自动加载规范
}
