<?php
namespace app\lib\exception;
use think\Exception;
use think\exception\Handle;
use think\Request;
use app\lib\exception\BaseException;
use think\log;
use think\config;
//自定义异常处理类
class ExceptionHandler extends Handle{
        private $code;
        private $msg;
        private $errorCode;
        //需要返回客户端当前请求的URL路径
        public function render(\Exception $e) {
           //当debug 开启时，显示tp5 自带错误  供开发工程师查看，，，为false 执行下面返回json
//			if(config('app_debug')==true){
//				return parent::render($ex);
//			}
            if($e instanceof BaseException){
               //如果是自定义的异常
                $this->code = $e->code;
                $this->msg =$e->msg;
                $this->errorCode = $e->errorCode;
            }else{
//                    config::get("switch"); 
                    if(config('switch')){
                        //调用tp 默认异常 
                        return parent::render($e);
                    }else{
                        $this->code = 500;
                        $this->msg ='sorry，we make a mistake. (^o^)Y';
                        $this->errorCode = 999;
                        //调用写入日志函数
                        $this->recordErrorLog($e);
                    }
                $this->code = 500;
                $this->msg ='服务器内部错误';
                $this->errorCode = 999;
                //调用写入日志函数
                $this->recordErrorLog($e);
            };
            
            $request = Request::instance();
            $result = [
                'msg' =>$this->msg,
                'error_code'=>$this->errorCode,
                //获取当前路径
                'request_url'=>$request->url(true),
            ];
            return json($result,$this->code);  //第二个参数为状态码
        }
        
        //自定义日志
        public function recordErrorLog(\Exception $e){
            //自定义写入错误信息
            Log::init([
                'type'=>'File',
                'path'=>LOG_PATH,
                'level'=>['error'],
            ]);
            Log::record($e->getMessage(),'error');
        }
  
}
