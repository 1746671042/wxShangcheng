<?php
namespace app\api\service;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Product;
use think\Log;
use think\Db;
use think\Loader;
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify{

    public function NotifyProcess($data, &$msg) {
        //支付成功回调
	   
       if($data['result_code'] =='SUCCESS'){
           
           //再次查询订单
		   //dump($data);exit;
           $orderNo = $data['out_trade_no'];
		 
           //使用事务与锁防止多次删减库存   lock
           Db::startTrans();
           try{
               $order = OrderModel::where('order_no','=',$orderNo)->lock(true)->find();
			 
			   
               //订单状态为1（未支付）
               if($order->status ==1){
                   //进行库存量检测
                   $service  = new OrderService();
                   $stockStatus = $service->checkOrderStock($order->id);
                   //库存量通过
                   if($stockStatus['pass']){
                       //更新订单状态
					   
                       $this->updateOrderStatus($order->id,true);
                       //减库存
					   
                       $this->reduceStock($stockStatus);
					   
                   }else{
                       //支付通过，但是库存量未通过
                       $this->updateOrderStatus($order->id,false);
                   }
               }
               Db::commit();
               //都成功返回ture
               return true;
           } catch (Exception $ex) {
               Db::rallback();
               Log::error($ex);
               return false;
           }
       }else{
           return true;
       }
    }
    
    
    //    支付成功后修改状态
    private  function updateOrderStatus($orderID,$success){
		//file_put_contents("13.txt",$orderID);
		
        $status = $success?OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
		//file_put_contents("14.txt",$status);
        //更新操作
        $res = OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
		//file_put_contents("15.txt",$res);
    }
    
    //库存量减少   setdec  自动减少
    private function reduceStock($stockStatus){
        foreach($stockStatus['pStatusArray'] as $singlePStatus){
            Product::where('id','=',$singlePStatus['id'])->setDec('stock',$singlePStatus['count']);
        }
    }
}