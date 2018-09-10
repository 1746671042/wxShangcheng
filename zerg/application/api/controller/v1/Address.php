<?php
namespace app\api\controller\v1;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use app\lib\exception\SuccessMessage;
use app\api\controller\BaseController;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\api\model\UserAddress;
//用户地址管理
class Address extends BaseController
{
    //修改地址等需要权限判断
    protected $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'CreateOrUpdateAddress']
    ];
    
    /**
     * 获取用户地址信息
     * @return UserAddress
     * @throws UserException
     */
    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)
            ->find();
        if(!$userAddress){
            throw new UserException([
               'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }
    
    
    protected function checkPrimaryScope(){
        $scope = TokenService::getCurrentTokenVar('scope');
        //先判断是否存在
        if($scope){
            //判断权限 等级
            if($scope>= ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
        
    }







    public function CreateOrUpdateAddress(){
      
      //实例化并调用validate，赋值给validate
      $validate = new AddressNew();
      $validate->goCheck();
      
      //1根据Token 获取Uid
      //2根据uid 获取用户数据，判断用户是否存在，不存在跑出异常
      //3获取用户从客户端提交过来的地址
      //4根据用户地址信息判断时候存在，从而判断是更新还是添加地址
      //1
     
      $uid = TokenService::getCurrentUid();
      
      //2
      $user = UserModel::get($uid);
      if(!$user){
          throw new UserException(); 
      }
      //3
      
      $dataArray = $validate->getDataByRule(input('post.'));
      $userAddress = $user->address;
      
      if(!$userAddress){
           //没有的话添加地址
          $user->address()->save($dataArray);
      }else{
          //修改没有括号
          $user->address->save($dataArray);
      }
      //return $user
      return json(new SuccessMessage(),201);
  }
}
