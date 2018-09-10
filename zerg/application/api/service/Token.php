<?php
//Usertoken 基类
namespace app\api\service;
use think\Request;
use think\Cache;
use app\lib\exception\TokenException;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;
class Token{
    //生成token
    public static function generateToken(){
        //32位随机字符串
        $randChars = getRandChar(32);
        //用三组字符串进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }
    
    
    //指明想要获取缓存中那个变量
    public static function getCurrentTokenVar($key){
        //必须要从http头部拿到信息
        $token = Request::instance()->header('token');
//        获取缓存
        $vars = Cache::get($token);
        if(!$vars){
            throw  new TokenException();
        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量值不存在');
            }
        }
    }
    //用户地址
    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar('uid');
//        $scope = self::getCurrentTokenVar('scope');
        return $uid;
    }
    
    
    //定义前置规则    //用户和管理员都可访问权限
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        //先判断是否存在
        if($scope){
            //判断权限 等级
            if($scope >= ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }
    //只有用户才能访问
    public static function needExclusiveScope(){
        $scope = self::getCurrentTokenVar('scope');
        //先判断是否存在
        if($scope){
            //判断权限 等级
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }
    
    //超级管理员
     public static function needSuperScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
    
    
    
    public static function isValidOperate($checkUID){
        //比对当前id和订单id
        if(!$checkUID){
            throw new Exception('检查UID事必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkUID){
            return true;
        }
        return false;
    }
    
    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if($exist){
            return true;
        }
        else{
            return false;
        }
    }
    
}
