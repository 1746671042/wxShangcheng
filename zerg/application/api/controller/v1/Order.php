<?php
namespace app\api\controller\v1;

use think\Controller;
use app\lib\exception\TokenException;
use app\api\service\Token as TokenSerivce;
use app\api\model\Order as OrderModel;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;
use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\validate\PagingParameter;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\SuccessMessage;
//订单
class Order extends BaseController
{
    //用户在选择商品后，向api 提交包含它所选择商品的相关信息
    //api在接收到消息后，需要检查订单相关商品的库存量
    //有库存，把订单数据存入数据库中，下单成功了，返回客户端信息，告诉客户端可以支付
    //调用我们的支付接口，进行支付
    //还需要再次进行库存量检测
    //服务器这边就可以调用微信的支付接口进行支付
    //小程序根据服务器返回的结果拉起微信支付
    //微信会返回给我们一个支付的结果
    //成功：也需要进行库存量及检测
    //成功：进行库存量的扣除
    //修改地址等需要权限判断
    
    //6212bc4198e73571237f2e509b88cab2
    //定义前置方法（用户可访问，管理员不可）
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'],
        'checkSuperScope' => ['only' => 'delivery,getSummary']
    ];
   
        
        
//    获取用户的订单   页数和条数  默认1页15条
    public function getSummaryByUser($page=1,$size=15){
        (new PagingParameter())->goCheck();
        //获取用户id  在token 中
        $uid =TokenSerivce::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size);
        //判空  对象判空用empty
        if($pagingOrders->isEmpty()){
            return [
                'data'=>[],
                'current_page'=>$pagingOrders->getCurrentPage()
            ];
        }
//        $data =$pagingOrders->toArray();
        $data = $pagingOrders->hidden(['snap_items', 'snap_address','prepay_id'])
            ->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders->currentPage()
        ];
    }
    
    
    
    //订单详情
    public function getDetail($id){
        (new IDMustBePostiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }
    
    
    public function placeOrder(){
        (new OrderPlace())->goCheck();   
        $products = input('post.products/a');
        $uid = TokenSerivce::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }
    
    
     /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 20){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        (new PagingParameter())->goCheck();
//        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    public function delivery($id){
        return "awfads";
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if($success){
            return new SuccessMessage();
        }
    }
}
