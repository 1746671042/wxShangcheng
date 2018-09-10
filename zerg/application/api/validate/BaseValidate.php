<?php
namespace app\api\validate;
use think\Exception;
use think\Validate;
use think\Request;
use app\lib\exception\ParameterException;
class BaseValidate extends Validate{

    public function goCheck(){ 
        //获取http 传入的参数
        //对这些参数校验
        $request = Request::instance();
        $params = $request->param();
        //添加->batch() 为了批量验证
        $result = $this->batch()->check($params);
        if(!$result){ 
            $e = new ParameterException([
                'msg'=>$this->error,
            ]);
            throw $e;
        }else{
            return true;
        }
    }
    //系统会自动传入几个参数 第一个是 要验证的值，第二个是规则，自己可以规定规则内容或者不写，第三个是最初传入的data。其实不只这三个参数，想了解详细的可以看看文档
    protected  function isPositiveInteger($value,$rule='',$data='',$field=''){
        if(is_numeric($value) &&is_int($value+0) && ($value+0)>0){
            return true;
        }else{
            return false;
//            return $field.'必须为整数';
        }
    }
    
    //地址验证规则 手机号
    public function isMobile($value){
        $rule = '^1(3|4|5|6|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    
    
    //判断微信客户信息
    protected  function isNotEmpty($value,$rule='',$data='',$field=''){
        if(empty($value)){
            return false;
         }else{
            return true;
//            return $field.'必须为整数';
        }
    }
    
    
    //接收参数教研地址参数只要六个
    public function getDataByRule($arrays){
        if(array_key_exists('user_id', $arrays)| array_key_exists('uid', $arrays)){
            //不允许包含userID或者id 防止恶意覆盖userId 外键
            throw new ParameterException([
                'msg'=>'参数中包含有非法参数userid或者id'
            ]); 
        }
        $newArray = [];
        foreach($this->rule as $key=>$value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}