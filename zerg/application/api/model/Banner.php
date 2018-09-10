<?php
namespace app\api\model;
use think\Db;
use think\Exception;
use app\api\model\BaseModel;
class Banner extends BaseModel{
    //设置需要隐藏字段，自动隐藏
    protected $hidden= ['update_time','delete_time'];
    
    //建立模型关联
    public function items(){
        return $this->hasMany("BannerItem","banner_id","id");
    }
    
    public static function getBannerById($id){
        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
        //根据banner id号获取banner信息
        //$result = Db::query('select * from banner_item where banner_id=?',[$id]);
        //$result = Db::table("banner_item")->where('banner_id','=',$id)->select();
        //return $result;
    }
}   
