<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\lib\exception\ProductException;
use app\api\validate\IDMustBePostiveInt;
class Product extends Controller
{ 
    //获取最近商品  默认15
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        
        if($products->isEmpty()){
            throw new ProductException();
        }
        //去除summary  database 配置文件修改'resultset_type' 为 'collection',
        $products = $products->hidden(['summary']);
        return $products;
    }
    
    
    //分类商品各个商品
    public function getAllCategory($id){
        //验证规则
        (new IDMustBePostiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        //去除summary  database 配置文件修改'resultset_type' 为 'collection',
        $products = $products->hidden(['summary']);
        return $products;
    }
    
    
    
    //首页点击每个商品跳转详情页
    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        
        $product = ProductModel::getProductDetail($id);
        
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}
