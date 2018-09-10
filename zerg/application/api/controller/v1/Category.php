<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;
use app\api\validate\IDMustBePostiveInt;
class Category extends Controller
{
    //查询所有分类
    public function getAllCategories()
    {
        //查询数据集所有方法 查询全部则用空数组
        $categories= CategoryModel::all([],'img');
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }
    
    
}
