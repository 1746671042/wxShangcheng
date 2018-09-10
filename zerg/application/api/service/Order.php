<?php

namespace app\api\service;
use app\api\model\Product;
use app\lib\exception\OrderException;
use app\api\model\UserAddress;
use app\lib\exception\UserException;
use app\api\model\OrderProduct;
use app\api\service\DeliveryMessage;
use think\Db;
//use app\api\model\Order as OrderModel;
class Order{
    //订单的商品列表，也就是客户端传递过来的products参数
    protected  $oProducts;
    //真是的商品信息（包括库存量）
    protected $products;
    protected $uid;
    //下单接口
    public function place($uid,$oProducts){
        //oProducts 和 products 作对比
        //products 从数据库中查询出来的数据
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid= $uid;
        $status = $this->getOrderStatus();
        if(!$status['pass']){
            $status['order_id']= -1;
            return $status;
        }
        
        //开始创建订单
        $orderSnap =$this->snapOrder($status);
        
        $order = $this->createOrder($orderSnap);
        $order['pass']=true;
        return $order;
    }
    
    //正式生成订单
    public function createOrder($snap){
        //tp 事务处理防止数组插入一半，导致错误
        //事务开始 仅仅用一个单词
        Db::startTrans(); 
        try{
                //调用生成订单号方法
                $orderNo = $this->makeOrderNo();
                $order = new \app\api\model\Order();
                $order->user_id = $this->uid;
                $order->order_no = $orderNo;
                $order->total_price = $snap['orderPrice'];
                $order->total_count = $snap['totalCount'];
                $order->snap_img = $snap['snapImg'];
                $order->snap_name = $snap['snapName'];
                $order->snap_address = $snap['snapAddress']; 
                $order->snap_items = json_encode($snap['pStatus']);
                $order->save();
                //向关联表添加数据
                $orderID = $order->id;
                $create_time = $order->create_time;
                foreach($this->oProducts as &$p){
                    $p['order_id']= $orderID;
                };

                $orderProduct = new OrderProduct();
                $orderProduct->saveAll($this->oProducts);
                //事务结束
                Db::commit();
                return [
                    'order_no'=>$orderNo,
                    'order_id'=>$orderID,
                    'create_time'=>$create_time
                ];
        
        } catch (Exception $ex) {
            //假如事务失败，调出失败数据事务回滚机制
            Db::rollback();
               throw $ex;
        }
       
    }
    
    
    //生成订单号
    public static function makeOrderNo(){
         //生成订单号
        $yCode = array("A","B","C","D","F","G","H","I","J");
        $orderSn = $yCode[intval(date('Y'))-2018].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }
    
    
    
    //生成订单快照
    private function snapOrder($status){
        $snap=[
            'orderPrice'=>0,
            'totalCount'=>0,
            //订单中所有商品给状态
            'pStatus'=>[],
            //订单地址
            'snapAddress'=>null,
            //所购买商品的图片与名称
            'snapName'=>'',
            'snapImg'=>''
        ];
        $snap['orderPrice']=$status['orderPrice'];
        $snap['totalCount']=$status['totalCount'];
        $snap['pStatus']=$status['pStatusArray'];
        $snap['snapAddress']= json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        //多件商品后面加等
        if(count($this->products) > 1){
            $snap['snapName'] .= '等';
        }
        return $snap;
    }
    
    
    
    //获取用户地址
    private  function getUserAddress(){
        $userAddress = UserAddress::where('user_id' , '=' , $this->uid)->find();
        //当用户地址不存在时
        if(!$userAddress){
            throw new UserException([
                'msg'=>'用户收货地址不存在,下单失败',
                'errorCode'=>60001,
            ]);
        }
        return $userAddress->toArray();
    }

    
    
    //建立方法，方便支付(pay)时候再次查询数据（下单却未支付情况）  对外提供，不是本类使用
    public function checkOrderStock($orderID){
        $oProducts = OrderProduct::where('order_id','=',$orderID)->select();
        $this->oProducts= $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
        
    }

    private function getOrderStatus(){
        //此数组记录所有订单的相关信息
        $status=[
            'pass'=>true,
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatusArray'=>[]
        ];
        foreach($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus($oProduct['product_id'],$oProduct['count'],$this->products);
            //
            if(!$pStatus['haveStock']){
                $status['pass']= false;
            }
            //所有商品总价
            $status['orderPrice']+=$pStatus['totalPrice'];
            $status['totalCount']+=$pStatus['counts'];
            //把所有的商品信息存入pstatusArray 数组
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }
    
    
    private function getProductStatus($oPID,$oCount,$products){
        $pIndex = -1;
        $pStatus = [
            'id'=>null,
            'havaStock'=>false,
            'count'=>0,
            'counts'=>0,
            'price'=>0,
            'name'=>'',
            'totalPrice'=>0,
            'main_img_url'=>null
        ];
        for($i=0;$i<count($products);$i++){
            if($oPID ==  $products[$i]['id']){
                $pIndex = $i;
            }
        }
        //购买商品不合法跑出异常（购买商品已下架等）
        if($pIndex ==-1){
            //客户端传递的ProductId有可能根本不存在
            throw new OrderException([
                'msg'=>'id为'.$oPID.'商品不存在，创建订单失败',
            ]);
        }else{
            //都符合规范，则执行正常购买程序
            //获取商品id
            $product = $products[$pIndex];
            $pStatus['id']= $product['id'];
            $pStatus['name']= $product['name'];
            $pStatus['counts']= $oCount;
            $pStatus['price']=$product['price'];
            $pStatus['main_img_url']=$product['main_img_url'];
            //一个商品的总价个
            $pStatus['totalPrice']= $product['price']*$oCount;
            //库存判断
            if($product['stock']-$oCount>=0){
                $pStatus['haveStock']=true;
            }
        }
        return $pStatus;
    }
    
    
    //根据订单信息查找真实商品信息
    private function getProductsByOrder($oProducts){
//        foreach($oProducts as $oProduct){
//            //循环查询数据库（不可取）  
//        }
        
        $oPIDs = [];
        foreach($oProducts as $item){
            array_push($oPIDs, $item['product_id']);
        }
        //查询并规定显示那个属性
        $products = Product::all($oPIDs)->visible(['id','price','stock','name','main_img_url'])->toArray();
        return $products;
    }
    
    
     public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }

}

