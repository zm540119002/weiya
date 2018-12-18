<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'deploy'=>[
            'id'=>40,'name'=>'供应商入驻','type'=>2,
            'sub_menu'=>[
                ['id'=>41,'name'=>'供应商入驻申请','display'=>1,'controller'=>'Deploy','action'=>'register',],
            ],
        ],
        'record'=>[
            'id'=>50,'name'=>'企业档案','type'=>2,
            'sub_menu'=>[
                ['id'=>51,'name'=>'企业档案案编辑','display'=>1,'controller'=>'Record','action'=>'edit',],
                ['id'=>52,'name'=>'企业档案案预览','display'=>1,'controller'=>'Record','action'=>'preview',],
            ],
        ],
        'brand'=>[
            'id'=>60,'name'=>'商标管理','type'=>2,
            'sub_menu' => [
                ['id'=>61,'name'=>'商标管理','display'=>1,'controller'=>'Brand','action'=>'manage',],
                ['id'=>62,'name'=>'商标备案','display'=>1,'controller'=>'Brand','action'=>'record',],
            ],
        ],
        'store'=>[
            'id'=>70,'name'=>'开店申请','type'=>2,
            'sub_menu' => [
                ['id'=>71,'name'=>'开店部署','display'=>1,'controller'=>'Store','action'=>'index',],
                ['id'=>72,'name'=>'店铺列表','display'=>1,'controller'=>'Store','action'=>'manage',],
                ['id'=>73,'name'=>'申请开新店','display'=>1,'controller'=>'Store','action'=>'edit',],
            ],
        ],
        'organize'=>[
            'id'=>80,'name'=>'组别角色','type'=>2,
            'sub_menu' => [
                ['id'=>81,'name'=>'组别角色','display'=>1,'controller'=>'Organize','action'=>'index',],
                ['id'=>82,'name'=>'组别角色删除','display'=>1,'controller'=>'Organize','action'=>'del',],
            ],
        ],
        'operation'=>[
            'id'=>90,'name'=>'店铺运营','type'=>2,
            'sub_menu' => [
                ['id'=>91,'name'=>'店铺提醒','display'=>1,'controller'=>'Operation','action'=>'index',],
            ],
        ],
        'goods'=>[
            'id'=>100,'name'=>'商品','type'=>2,
            'sub_menu' => [
                ['id'=>101,'name'=>'商品','display'=>1,'controller'=>'Goods','action'=>'manage',],
                ['id'=>102,'name'=>'增加商品','display'=>1,'controller'=>'Goods','action'=>'edit',],
                ['id'=>103,'name'=>'商品预览','display'=>1,'controller'=>'Goods','action'=>'preview',],
                ['id'=>104,'name'=>'商品排序','display'=>1,'controller'=>'Goods','action'=>'setSort',],
                ['id'=>105,'name'=>'上下架','display'=>1,'controller'=>'Goods','action'=>'setShelf',],
                ['id'=>106,'name'=>'库存管理','display'=>1,'controller'=>'Goods','action'=>'setInventory',],
            ],
        ],
        'promotion'=>[
            'id'=>110,'name'=>'促销','type'=>2,
            'sub_menu' => [
                ['id'=>111,'name'=>'场景','display'=>1,'controller'=>'Promotion','action'=>'manage',],
                ['id'=>112,'name'=>'增加促销','display'=>1,'controller'=>'Promotion','action'=>'edit',],
            ],
        ],
        'order'=>[
            'id'=>120,'name'=>'订单管理','type'=>2,
            'sub_menu' => [
                ['id'=>121,'name'=>'打单','display'=>1,'controller'=>'Order','action'=>'index',],
            ]
        ]
    ],
];