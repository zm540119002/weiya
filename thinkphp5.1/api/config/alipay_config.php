<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
    //$alipay_config['partner']		=> '2088021715417505';
    'partner' => '2088821868456923',

    //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
    'seller_id'	=> '2088821868456923',

    //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
    'seller_email'	=> '1039188986@qq.com',

    // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
    //$alipay_config['key']			=> '4obwgvuc6cr8l1n248ax2plx0s2abd9p';
    'key'		=> 'cidoucoly59f1gwnbg51qqavdetduu2n',
    // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=>123这类自定义参数，必须外网可以正常访问
    'notify_url' => "http://商户网关网址/alipay.wap.create.direct.pay.by.user-PHPUTF-8/notify_url.php",

    // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=>123这类自定义参数，必须外网可以正常访问
    'return_url' => "http://商户网址/alipay.wap.create.direct.pay.by.user-PHP-UTF-8/return_url.php",

    //签名方式
    'sign_type'   => strtoupper('MD5'),

    //字符编码格式 目前支持utf-8
    'input_charset'=> strtolower('utf-8'),

    //ca证书路径地址，用于curl中ssl校验
    //请保证cacert.pem文件在当前文件夹目录中
//    'cacert'    => __DIR__  .'\\..\\acert.pem',
    'cacert'    => __DIR__."/../../common/component/payment/alipayMobile/apiclient_cert.pem",


    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'transport'    => 'http',

    // 支付类型 ，无需修改
    'payment_type' => "1",

    // 产品类型，无需修改
'service' => "alipay.wap.create.direct.pay.by.user",
    
];
