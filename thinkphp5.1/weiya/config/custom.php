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
    'title' => '美尚云',
    'welcome_speech' => '您好，请问有什么可以帮到您的？',
    'error_login' => '您还未登录平台，请先登录！',
    'default_page_size' => 5,//默认每页显示记录数
    'not_ajax' => '请用AJAX方式访问！',//不是AJAX
    'not_post' => '请用POST方式访问！',//不是POST
    'not_get' => '请用GET方式访问！',//不是GET
    'multi_store' => '登录成功，请选择店铺！',
    'no_empower' => '未授权！',//不是GET
    'sms_expire' => 60 * 10,//短信验证码过期时间
    'sms_sign_name' => '美尚云',//短信签名名称（阿里云）
    'sms_template_code' => 'SMS_127169884',//短信模板CODE（阿里云）
    'factory_cache_time' => 60 * 60 * 24,//供应商缓存时间
    'store_cache_time' => 60 * 60 * 24,//采购商缓存时间
    'current_store_cache_time' => 10 * 24,//当前店铺缓存时间
    'system_type' => [
        '1000' => 'factory',
        '1001' => 'store',
    ],'store_type' => [
        '1' => '企业旗舰店',
        '2' => '品牌旗舰店',
    ],'run_type' => [
        '1' => '美尚采购店铺',
        '2' => '美尚分成店铺',
        '3' => '美尚会店铺',
        '4' => '在线商城',
    ],'operational_model'=>[
        '1'=>'美尚联营',
        '2'=>'商户自营',
    ],
    'unit'=>[
        '1'=>'个',
        '2'=>'支',
        '3'=>'瓶',
        '4'=>'盒',
        '5'=>'箱',
    ],
    'shelf_status'=>[
        '1'=>'下架',
        '2'=>'待审核',
        '3'=>'上架',
    ],
    'auth_status'=>[
        '-1'=>'审核不通过',
        '1'=>'待审核',
        '2'=>'审核通过',
    ],
    /**
     * '支付方式：0：保留 1 微信 2：支付宝 3：网银 4:钱包',
     */
    'payment_code'=>[
        '1'=>'微信支付',
        '2'=>'支付宝',
        '3'=>'银联',
        '4'=>'账户钱包',
    ],
    /**
     * '支付方式：0：保留 1 微信 2：支付宝 3：网银 4:钱包',
     */
    'brand_type'=>[
        '1'=>'食品',
        '2'=>'保健',
        '3'=>'护肤',
    ],
];