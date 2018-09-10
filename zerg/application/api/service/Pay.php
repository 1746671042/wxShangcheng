<?php
namespace app\api\service;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\api\service\Token;
use app\lib\exception\TokenException;
use app\lib\enum\OrderStatusEnum;
use think\Log;
//引入微信sdk   extends\WxPay\WxPay.Api.php 文件夹下
use think\Loader;
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
//微信同意下单接口位于wxpay.date中
class Pay{
    private $orderID;
    private $orderNO;
    function __construct($orderID) {
        if(!$orderID){
            throw new Exception("订单号不允许为NULL");
        }
        $this->orderID = $orderID;
        
    }
    
    public function pay(){
        //订单号可能根本不存在
        //订单号确实存在，但是订单号和当前用户不匹配
        //订单有可能已被支付过,查询订单状态
     
        //调用检测方法
        $this->checkOrderValid();
        //进行库存量检测
        $orderService = new OrderService();  //service 
        $status = $orderService->checkOrderStock($this->orderID);
        //库存量检测未通过
        if(!$status['pass']){
            return $status;
        }
        //成功执行微信预订单生成
       
        return $this->makeWxPreOrder($status['orderPrice']);
    }
    
    //前面通过检测执行微信检测
    private function makeWxPreOrder($totalPrice){
    	
        //首先获取用户openid 
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        //调用微信sdk接口中的统一下单类
        $wxOrderData = new \WxPayUnifiedOrder();
        //赋给订单号
        $wxOrderData -> SetOut_trade_no($this->orderNo);
        $wxOrderData -> SetTrade_type('JSAPI');
        //价格   分单位*100
        $wxOrderData -> SetTotal_fee($totalPrice*100);
        $wxOrderData -> SetBody('添添商铺');
        $wxOrderData -> SetOpenid($openid);
        //传递url 接收微信url回调通知
        $wxOrderData ->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }
    
    //微信接口调用函数
    private function getPaySignature($wxOrderData){
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
		
        if($wxOrder['return_code'] !='SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取支付订单失败','error');
        }
        //prepay_id  返回支付订单后状态
        ///更新订单微信支付的预订单id（用于发送模板消息）
		
        $S = $this->recordPreorder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }
    
    //生成签名
    private function sign($wxOrder){
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData -> SetAppid(config('wx.app_id'));
        $jsApiPayData -> SetTimeStamp((string)time());
        
        $rand =  md5(time().mt_rand(0, 1000));
        $jsApiPayData -> SetNonceStr($rand);
        $jsApiPayData -> SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData -> SetSignType('md5');
        //生成sign
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData -> GetValues();
        $rawValues['paySign']= $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }
    
    
    
    private function recordPreorder($wxOrder){
		
        //更新订单微信支付的预订单id（用于发送模板消息）
        return OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
		
    }
    
    
    public function checkOrderValid(){
        //1
        $order = OrderModel::where("id","=",$this->orderID)->find();//model
        if(!$order){
            throw new OrderException();
        }
        //2
        if(!Token::isValidOperate($order->user_id)){
            throw new Exception([
                'msg'=>'订单与用户信息不匹配',
                "errorCode"=>10003
            ]);
        }
        
        //3  直接读取lib\enum\scopeEnum下面文件的属性即可
        if($order->status != OrderStatusEnum::UNPAID){
            throw  new OrderException([
                'msg'=>'订单状态异常',
                'errorCode'=>80003,
                'code'=>400
            ]);
        }
        //返回订单编号
        $this->orderNo = $order->order_no;
        return true;
        
    }
}