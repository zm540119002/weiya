<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c] 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 ]
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    //底部购物车配置
    'menu' => [
        [
            'name'   => '总金额',
            'class'=>[
                'bottom_item',
                'amount',
            ],
        ],//0
        [
            'name'   => '加入购物车',
            'class'=>[
                'bottom_item',
                'add_cart',
            ],
            'action'=>url('Cart/addCart'),
        ],//1
        [
            'name'   => '购物车',
            'class'=>[
                'bottom_item',
                'add_cart_icon',
            ],
        ],//2
        [
            'name'   => '立即购买',
            'class'=>[
                'bottom_item',
                'buy_now',
            ],
        ],//3
        [
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
        ],//4
        [
            'name'   => '支付',
            'class'=>[
                'bottom_item',
                'pay',
            ],
        ],//5
        [
            'name'   => '增加地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],//6
        [
            'name'   => '修改地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],//7
        [
            'name'   => '新建地址',
            'class'=>[
                'bottom_item',
                'address_create',
            ],
        ],//8
        [
            'name'   => '去结算',
            'class'=>[
                'bottom_item',
                'settlement',
            ],
        ],//9
        [
            'name'   => '全选',
            'class'=>[
                'bottom_item',
                'checked_all',
            ],
        ],//10
        [
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
        ],//11
        [
            'name'   => '确认收货',
            'class'=>[
                'bottom_item',
                'confirm_receive',
            ],
        ],//12
        [
            'name'   => '去评价',
            'class'=>[
                'bottom_item',
                'to_evaluate',
            ],
        ],//13
        [
            'name'   => '再次购买',
            'class'=>[
                'bottom_item',
                'purchase_again',
            ],
        ],//14
        [
            'name'   => '增加商标',
            'class'=>[
                'bottom_item',
                'add_brand',
            ],
        ],//15
        [
            'name'   => '立即提交',
            'class'=>[
                'bottom_item',
                'submit',
            ],
        ],//16
        [
            'name'   => '删除',
            'class'=>[
                'bottom_item',
                'delete',
            ],
        ]//17
    ],
];
