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
// | 维雅公众号 配置文件
// +----------------------------------------------------------------------

return [
    //TODO: 修改这里配置为您自己申请的商户信息
    'appid'=>'wxb249b1cd89a875b2',
    'appsecret'=>'eb215884be650aead864ef4d46c285c2',
    'mchid' =>'',
    'key' => '',

    //TODO:公众号支付回调函数
    'call_back_url'  => "CallBack/notifyUrl",
    'call_back_url_business'  => "http://".$_SERVER['HTTP_HOST']."/index.php/Business/CallBack/notifyUrl",
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var unknown_type
     */
    'curl_proxy_host' =>'0.0.0.0',
    'curl_proxy_port' => 0,

    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    'report_levenl' => 1,

    //=======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var path
     */
    'sslcert_path' => __DIR__.'/../../../common/component/payment/weixin/cert/apiclient_cert.pem',
    'sslkey_path' => __DIR__.'/../../../common/component/payment/weixin/cert/apiclient_key.pem',
];
