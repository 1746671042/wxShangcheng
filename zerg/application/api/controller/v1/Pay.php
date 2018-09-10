<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;
use think\Log;
class Pay extends BaseController{
    //定义前置方法（用户可访问，管理员不可）
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'getPreOrder']
    ];
      
    public function getPreOrder($id = 1){
        //实现对id教研
        (new IDMustBePostiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay(); 
    }
    
    //接收微信支付返回的结果
    //public function redirectNotify(){
        //通知频路  15/15/30/180/1800/1800/1800/1800/3600 单位秒
        //再次检查库存量 超卖
        //更新订单status 状态
        //减库存
        //如果成功处理我们返回微信成功处理的消息  否则，我们需要返回没有成功的处理
        
        //特点 ：post :xml格式 不携带参数
       //$notify = new WxNotify();
       //$notify->Handle();
    //}
    
    
    
    //返回的信息转发给redirectNotify
    public function receiveNotify(){
        //通知频路  15/15/30/180/1800/1800/1800/1800/3600 单位秒
        //再次检查库存量 超卖
        //更新订单status 状态
        //减库存
        //如果成功处理我们返回微信成功处理的消息  否则，我们需要返回没有成功的处理
        
        //特点 ：post :xml格式 不携带参数
		//Log::write("**********************************************fdsafasdfsdaf");
        $notify = new WxNotify();
        $notify->Handle();
        //$xmlData = file_get_contents('php://input');
        //$result = curl_post_raw('http://www.wxshop.com/api/v1/pay/re_notify?XDEBUG_SESSION_START=netbeans-xdebug',$xmlData);
    }
}
