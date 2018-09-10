<?php
namespace app\api\model;
use app\api\model\Product as ProductModel;
use app\api\model\BaseModel;
use think\Model;
class Product extends BaseModel
{
    ////设置需要隐藏的字段
    protected $hidden= ['main_img_id','pivot','update_time','delete_time','from','category_id','create_time'];
    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
    
    //建立商品详情关联   1对多
    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }
    //建立商品参数   1对多
    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }
    
    public static function getMostRecent($count){
        $products = self::limit($count)
                ->order('create_time desc')
                ->select();
        return $products;
    }
    
    
    //调取分类下部各个商品
    public static function getProductsByCategoryID($categoryID){
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }
    
    //调取首页商品的详细信息
    public static function getProductDetail($id){
        $product = self::with([
            'imgs'=>function ($query){
                    $query->with(['imgUrl'])
                    ->order("order",'asc');
            }
        ])
        ->with('properties')->find($id);
        return $product;
    }
}
