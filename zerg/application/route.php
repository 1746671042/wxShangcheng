<?php

use think\Route;
//动态更改版本号  :version 自动检测v1 v2  这是版本兼容
//轮播图
Route::get("api/:version/banner/:id","api/:version.Banner/getBanner");
//专题
Route::get("api/:version/theme","api/:version.Theme/getSimpleList");
//专题内部信息
Route::get("api/:version/theme/:id","api/:version.Theme/getComplexOne");


//Route::group('api/:version/product', function(){
//    //最新商品
//    Route::get('/recent',"api/:version.Product/getRecent");
//    //获取分类下各个商品
//    Route::get('/by_category',"api/:version.Product/getAllCategory");
//    //获取首页各个商品的详情
//    Route::get('/:id',"api/:version.Product/getOne",[],['id'=>'\d+']);
//});
//最新商品
Route::get("api/:version/product/recent","api/:version.Product/getRecent");
////获取分类下各个商品
Route::get("api/:version/product/by_category","api/:version.Product/getAllCategory");
//获取首页各个商品的详情
Route::get("api/:version/product/:id","api/:version.Product/getOne",[],['id'=>'\d+']);


//分类列表
Route::get("api/:version/category/all","api/:version.Category/getAllCategories");
//用户信息
Route::post("api/:version/token/user","api/:version.Token/getToken");



//Route::post("api/:version/address","api/:version.Address/CreateOrUpdateAddress");

Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//支付
Route::post('api/:version/order','api/:version.Order/placeOrder');
//用户购买的商品
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);

Route::get('api/:version/order/paginate','api/:version.Order/getSummary');
Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');



//调用微信支付接口
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
//Route::post('api/:version/pay/re_notify','api/:version.Pay/redirectNotify');
Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');

//Message
Route::post('api/:version/message/delivery', 'api/:version.Message/sendDeliveryMsg');

//Address//用户地址
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');



/**
 * 路由注册
 *
 * 以下代码为了尽量简单，没有使用路由分组
 * 实际上，使用路由分组可以简化定义
 * 并在一定程度上提高路由匹配的效率
 */

// 写完代码后对着路由表看，能否不看注释就知道这个接口的意义

//Sample
//Route::get('api/:version/sample/:key', 'api/:version.Sample/getSample');
//Route::post('api/:version/sample/test3', 'api/:version.Sample/test3');
//
////Miss 404
////Miss 路由开启后，默认的普通模式也将无法访问
////Route::miss('api/v1.Miss/miss');
//
////Banner
//Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');
//
////Theme
//// 如果要使用分组路由，建议使用闭包的方式，数组的方式不允许有同名的key
////Route::group('api/:version/theme',[
////    '' => ['api/:version.Theme/getThemes'],
////    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct'],
////    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct']
////]);
//
//Route::group('api/:version/theme',function(){
//    Route::get('', 'api/:version.Theme/getSimpleList');
//    Route::get('/:id', 'api/:version.Theme/getComplexOne');
//    Route::post(':t_id/product/:p_id', 'api/:version.Theme/addThemeProduct');
//    Route::delete(':t_id/product/:p_id', 'api/:version.Theme/deleteThemeProduct');
//});
//
////Route::get('api/:version/theme', 'api/:version.Theme/getThemes');
////Route::post('api/:version/theme/:t_id/product/:p_id', 'api/:version.Theme/addThemeProduct');
////Route::delete('api/:version/theme/:t_id/product/:p_id', 'api/:version.Theme/deleteThemeProduct');
//
////Product
//Route::post('api/:version/product', 'api/:version.Product/createOne');
//Route::delete('api/:version/product/:id', 'api/:version.Product/deleteOne');
//Route::get('api/:version/product/by_category/paginate', 'api/:version.Product/getByCategory');
//Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
//Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);
//Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
//
////Category
//Route::get('api/:version/category', 'api/:version.Category/getCategories'); 
//// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
////Route::get('api/:version/category/:id', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
////Route::get('api/:version/category/:id/products', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
//Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');
//
////Token
//Route::post('api/:version/token/user', 'api/:version.Token/getToken');
//
//Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
//Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');
//
////Address
//Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
//Route::get('api/:version/address', 'api/:version.Address/getUserAddress');
//
////Order
//Route::post('api/:version/order', 'api/:version.Order/placeOrder');
//Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);
//Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');
//
////不想把所有查询都写在一起，所以增加by_user，很好的REST与RESTFul的区别
//Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
//Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');
//
////Pay
//Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
//Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
//Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');
//Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');
//
////Message
//Route::post('api/:version/message/delivery', 'api/:version.Message/sendDeliveryMsg');


