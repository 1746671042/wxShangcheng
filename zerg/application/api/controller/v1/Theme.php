<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\lib\exception\ThemeException;
use app\api\validate\IDMustBePostiveInt;

class Theme extends Controller
{
    /**
     * @url theme?ids=id1,id2,id3……
     * @return 一组theme 模型
     */
    public function getSimpleList($ids = ''){
        //调用验证规则
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        //调用关联模型
        $result = ThemeModel::with(['topicImg','headImg'])->select($ids);
        //添加isempty  防止查询出来是个空的对象，数组可以用！判断，对象不可以
        if($result->isEmpty()){
        //判断是否为空，为空抛出异常
            throw  new ThemeException();
        }
        return $result;
    }
    
    
    /**调取每个主题内部的信息
     * @url  theme/:id
     * @return 一组theme 模型
     */
    public function getComplexOne($id){
        //验证id 是否符合
        (new IDMustBePostiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if(!$theme){
        //判断是否为空，为空抛出异常
            throw  new ThemeException();
        }
        return $theme;
    }
}
