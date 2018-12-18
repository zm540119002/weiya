<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'goods_category'=>[
            'id'=>100,'name'=>'商品分类','type'=>2,
            'sub_menu' => [
                ['id'=>101,'name'=>'商品分类管理','display'=>1,'controller'=>'GoodsCategory','action'=>'manage',],
            ],
        ],
        'factory'=>[
            'id'=>200,'name'=>'入驻审核','type'=>2,
            'sub_menu' => [
                ['id'=>201,'name'=>'厂商入驻审核','display'=>1,'controller'=>'Factory','action'=>'auditManage',],
            ],
        ],
        'brand'=>[
            'id'=>300,'name'=>'品牌审核','type'=>2,
            'sub_menu' => [
                ['id'=>301,'name'=>'厂商品牌审核','display'=>1,'controller'=>'Brand','action'=>'auditManage',],
            ],
        ],
        'store'=>[
            'id'=>400,'name'=>'店铺审核','type'=>2,
            'sub_menu' => [
                ['id'=>401,'name'=>'厂商店铺审核','display'=>1,'controller'=>'Store','action'=>'auditManage',],
            ],
        ],
        'goods'=>[
            'id'=>400,'name'=>'商品审核','type'=>2,
            'sub_menu' => [
                ['id'=>401,'name'=>'厂商店铺商品审核','display'=>1,'controller'=>'Goods','action'=>'auditManage',],
            ],
        ],

    ],
];