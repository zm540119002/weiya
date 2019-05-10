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
    'title' => '维雅品牌定制',
    'module_type' => 1,//模块类型
    'default_page_size'=>10,
    // 支付链接 system_id 系统平台id 请去msy index config文件查询 payment_type 支付方式 1：订单支付 2：充值支付
    'pay_gateway' => 'https://msy.meishangyun.com/index/Payment/pay?system_id=1&sn=',
    'pay_recharge'=> 'https://msy.meishangyun.com/index/Payment/pay?system_id=1&sn=',
    // 充值金额
    'recharge_amount'=>[0.01,10000,20000,30000,50000,80000],

    // 支付方式 1 微信 2：支付宝 3：网银 4:钱包
    'pay_code' => [
        'WeChatPay' => [
            'code' => 1,
            'name' => '微信支付',
        ],
        'Alipay' => [
            'code' => 2,
            'name' => '支付宝',
        ],
        'UnionPay' => [
            'code' => 3,
            'name' => '银联支付',
        ],
        'walletPay' => [
            'code' => 4,
            'name' => '钱包支付',
        ],
    ],
    // 充值方式 1 微信 2：支付宝 3：网银 4:线下支付
    'recharge_code' => [
        'WeChatPay' => [
            'code' => 1,
            'name' => '微信支付',
        ],
        'Alipay' => [
            'code' => 2,
            'name' => '支付宝',
        ],
        'UnionPay' => [
            'code' => 3,
            'name' => '银联支付',
        ],
        'OfflinePay' => [
            'code' => 4,
            'name' => '线下支付',
        ],
    ],


    // 支付单的类型 1 订单 2：充值 3：加盟
    'pay_type' => [
        'orderPay' => [
            'code' => 1,
            'name' => '订单支付',
        ],
        'rechargePay' => [
            'code' => 2,
            'name' => '充值支付',
        ],
        'franchisePay' => [
            'code' => 3,
            'name' => '加盟支付',
        ],
    ],
];


