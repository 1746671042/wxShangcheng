<?php

namespace app\api\Controller\v1;
use app\api\validate\IDMustBePostiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use app\lib\exception\BaseException;
use think\Exception;
class Banner{
    
    //数据库缓存   php think optimize:schema   runtime下
    //路由缓存   php think optimize:route
    
    public static function getBanner($id){
        //根据banner id号获取banner信息 验证规范
        (new IDMustBePostiveInt())->goCheck();
        //模型查询，with内部是调用模型关联方法
        //$banner = BannerModel::with(['items','items.img'])->find($id);
        $banner = BannerModel::getBannerById($id);
        //去除指定元素
        //$banner = hidden(['update_time',"delete_time"]);
        //只显示
        //$banner->vasible(['id','update_time']);
        if(!$banner){
            //抛出错误不存在
            throw new BannerMissException('内部错误');
        }
        //读取图片前置路径
        $c=config('setting.img_prefix');                
        return $banner;
    }
}
 