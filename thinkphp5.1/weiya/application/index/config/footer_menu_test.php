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
        [//0
            'name'   => '',
            'class'=>[
                'bottom_item',
                'amount',
            ],
        ],[//1
            'name'   => '加入购物车',
            'class'=>[
                'bottom_item',
                'add_cart',
            ],
            'action'=>url('Cart/addCart'),
        ],[//2
            'name'   => '购物车',
            'class'=>[
                'bottom_item',
                'add_cart_icon',
            ],
        ],[//3
            'name'   => '立即购买',
            'class'=>[
                'bottom_item',
                'buy_now',
            ],
        ],[//4
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
        ],[//5
            'name'   => '支付',
            'class'=>[
                'bottom_item',
                'pay',
            ],
        ],[//6
            'name'   => '增加地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],[//7
            'name'   => '修改地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],[//8
            'name'   => '新建地址',
            'class'=>[
                'bottom_item',
                'address_create',
            ],
        ],[//9
            'name'   => '去结算',
            'class'=>[
                'bottom_item',
                'settlement',
            ],
        ],[//10
            'name'   => '全选',
            'class'=>[
                'bottom_item',
                'checked_all',
            ],
        ],[//11
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
        ],[//12
            'name'   => '确认收货',
            'class'=>[
                'bottom_item',
                'confirm_receive',
            ],
        ], [//13
            'name'   => '去评价',
            'class'=>[
                'bottom_item',
                'to_evaluate',
            ],
        ],[//14
            'name'   => '再次购买',
            'class'=>[
                'bottom_item',
                'purchase_again',
            ],
        ],[//15
            'name'   => '增加商标',
            'class'=>[
                'bottom_item',
                'add_brand',
            ],
        ],[//16
            'name'   => '立即提交',
            'class'=>[
                'bottom_item',
                'submit',
            ],
        ],[//17
            'name'   => '删除',
            'class'=>[
                'bottom_item',
                'delete',
            ],
        ]
    ],
];
