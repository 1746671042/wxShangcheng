<?php
//创建一个中间器，继承controller
namespace app\api\controller;
use think\Controller;
use app\api\service\Token as TokenService;
class BaseController extends Controller{
    //用户和管理员同事访问
    protected function checkPrimaryScope(){
        TokenService::needPrimaryScope();
    }
    //只有用户才能访问
     protected function checkExclusiveScope(){
        TokenService::needExclusiveScope();
    }
    //只有管理员访问
    protected function checkSuperScope()
    {
        TokenService::needSuperScope();
    }
}