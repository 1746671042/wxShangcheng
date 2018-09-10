<?php

//023etP4u1BLpKb0orI3u1TA85u1etP4i
namespace app\api\controller\v1;
use think\Controller;
use think\Request;
use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;
use app\api\validate\AppTokenGet;
use app\api\service\AppToken;
class Token extends Controller

{
   public function getToken($code=''){
       (new TokenGet())->goCheck(); 
       $ut = new UserToken($code);
       $token = $ut->get();
       return [
           'token'=>$token
       ];
   }
   
    /**
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
    public function getAppToken($ac='', $se='')
    {
        //加入此代码阔以跨域js 不可跨域
//        header('Access-Control-Allow-Origin: *');
//        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//        header('Access-Control-Allow-Methods: POST,GET');
        
        
        
        
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        return [
            'token' => $token
        ];
    }

    public function verifyToken($token='')
    {
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];
    }

}
