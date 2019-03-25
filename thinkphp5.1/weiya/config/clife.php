<?php
return [
    'eyeshape' => [
        ['type' => ['eyelid'=>1,'narrow'=>1,'updown'=>1],'explain' => '铜铃眼',],
        ['type' => ['eyelid'=>1,'narrow'=>1,'updown'=>2],'explain' => '睡龙眼',],
        ['type' => ['eyelid'=>2,'narrow'=>2,'updown'=>1],'explain' => '丹凤眼',],
        ['type' => ['eyelid'=>2,'narrow'=>2,'updown'=>2],'explain' => '睡凤眼',],
        ['type' => ['eyelid'=>1,'narrow'=>2,'updown'=>1],'explain' => '瑞凤眼',],
        ['type' => ['eyelid'=>1,'narrow'=>2,'updown'=>2],'explain' => '月牙眼',],
        ['type' => ['eyelid'=>2,'narrow'=>3,'updown'=>1],'explain' => '桃花眼',],
        ['type' => ['eyelid'=>2,'narrow'=>3,'updown'=>2],'explain' => '柳叶眼',],
        ['type' => ['eyelid'=>1,'narrow'=>3,'updown'=>1],'explain' => '狐媚眼',],
        ['type' => ['eyelid'=>1,'narrow'=>3,'updown'=>2],'explain' => '孔雀眼',],
    ],
    'blackHeadLevel'=>[
        1 => '无',
        2 => '极少',
        3 => '轻度',
        4 => '中度',
        5 => '重度',
    ],
    'authentication' => [
        'store_operation' => [
            'id'=>10,
            'name'=>'店铺运营',
            'nodes'=>[
                ['id'=>11,'name'=>'商品管理','url'=>'',],
                ['id'=>12,'name'=>'场景管理','url'=>'',],
                ['id'=>13,'name'=>'特价管理','url'=>'',],
                ['id'=>14,'name'=>'上架管理','url'=>'',],
                ['id'=>15,'name'=>'下架管理','url'=>'',],
            ],
        ],'pre_sale' => [
            'id'=>30,
            'name'=>'售前客服',
            'nodes'=>[
                ['id'=>31,'name'=>'售前咨询','url'=>'',],
            ],
        ],'in_sale' => [
            'id'=>50,
            'name'=>'售中客服',
            'nodes'=>[
                ['id'=>51,'name'=>'接单打印','url'=>'',],
                ['id'=>52,'name'=>'备货打包','url'=>'',],
                ['id'=>53,'name'=>'发货完成','url'=>'',],
                ['id'=>54,'name'=>'物流填单','url'=>'',],
            ],
        ],'after_sale' => [
            'id'=>70,
            'name'=>'售后客服',
            'nodes'=>[
                ['id'=>71,'name'=>'售后咨询','url'=>'',],
                ['id'=>72,'name'=>'退换货','url'=>'',],
            ],
        ],'financial_settlement' => [
            'id'=>90,
            'name'=>'财务结算',
            'nodes'=>[
                ['id'=>91,'name'=>'退换货','url'=>'',],
                ['id'=>92,'name'=>'开发票','url'=>'',],
            ],
        ],
    ],
];