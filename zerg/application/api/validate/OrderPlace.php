<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
use app\lib\exception\ParameterException;
 class  OrderPlace extends BaseValidate{
    protected  $products= [
        [
            'product_id'=>1,
            'count'=>3
        ],
        [
            'product_id'=>2,
            'count'=>3
        ],
        [
            'product_id'=>3,
            'count'=>3
        ]
    ];
     protected  $oProducts= [
        [
            'product_id'=>1,
            'count'=>3
        ],
        [
            'product_id'=>2,
            'count'=>3
        ],
        [
            'product_id'=>3,
            'count'=>3
        ]
    ];
    protected $rule = [
       'products'=>'checkProducts',
    ];
    protected  $singleRule= [
        'product_id'=>'require|isPositiveInteger',
        'count'=>'require|isPositiveInteger',
    ];


    protected  function checkProducts($values){
       if(empty($values)){
           throw new ParameterException([
              'msg'=>"商品列表不能为空" 
           ]);
       }
       if(!is_array($values)){
           throw new ParameterException([
              'msg'=>"商品参数格式不正确" 
           ]);
       }
       
       
       foreach($values as $value){
           $this->checkProduct($value);
       }
       return true;
   }
   
   //遍历数据格式是否正确
   protected  function checkProduct($value){
       
       $validate = new BaseValidate($this->singleRule);
       $result = $validate->check($value);
       if(!$result){
           throw new ParameterException([
               'msg'=>'商品参数错误',
           ]);
       }
   }
}
