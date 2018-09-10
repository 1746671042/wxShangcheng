<?php
namespace app\api\service;
use think\Exception;
use app\lib\exception\WeChatException;
use app\api\model\User as UserModel;
use app\api\service\Token;
use app\lib\exception\TokenException;
use app\lib\enum\ScopeEnum;
class UserToken extends Token{
    //用户信息
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;
    public function __construct($code) {
        $this->code = $code;
        $this->wxAppID=config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }
    public function get(){
        //直接调用common 文件内部函数，common 内部函数自治性
        $result = curl_get($this->wxLoginUrl);
        //变成对象
        $wxResult = json_decode($result,true);
        //return $wxResult;
        if(empty($wxResult)){
            throw new Exception("获取seesion_key 及opendID时异常，微信内部错误");
        }else{
            $loginFail = array_key_exists('errcode', $wxResult);
            //return $loginFail;
            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }
    }
    //颁发令牌
    private function grantToken($wxResult){
        //拿到openid->数据库查看，这个openId 是否存在—>如果存在则不处理，不存在增加
        //生成令牌准备缓存数据写入缓存，把令牌返回客户端
        //key:令牌
        //value:wxRestult uid scope(权限级别)
        $openid = $wxResult['openid'];
        //return $openid;
        //调用方法查询id 是否存在
        $user = UserModel::getByOpenId($openid);
//        return $user;
        if($user){
            $uid = $user->id;
        }else{
            //创建用户
            $uid=$this->newUser($openid);
            return $uid;
        }
//        return $uid;
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }
    
    
    //存入缓存
    private function saveToCache($cachedValue){
        //self调用基类方法
        $key = self::generateToken();
        //数组-》字符串
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        //存入缓存
        $request = cache($key,$value,$expire_in);
        if(!$request){
            //覆盖异常信息
            throw new TokenException(['msg'=>'服务器缓存异常','errorCode'=>10005]);
        }
//        返回令牌
        return $key;
    }




    //调整数据结构
    public function prepareCachedValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid']=$uid;
        //scope=16   app 用户的权限
        $cachedValue['scope']= ScopeEnum::User;
        //scope=32   cms 用户的权限
//        $cachedValue['scope']=32;
        return $cachedValue;
    }
    
    
   
    private function newUser($openid){
        $user = UserModel::create([
            'openid'=>$openid
        ]);
        return $user->id;
    }

    private  function processLoginError($wxResult){
        throw new WeChatException([
            'msg'=>$wxResult['errmsg'],
            'errorCode'=>$wxResult['errcode'],
        ]);
    }
}