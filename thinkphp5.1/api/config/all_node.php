<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'common'=>[
            'id'=>1,'name'=>'系统','type'=>1,
            'sub_menu' => [
                ['id'=>2,'name'=>'账号管理','display'=>1,'controller'=>'User','action'=>'manage',],
                ['id'=>3,'name'=>'个人信息','display'=>1,'controller'=>'User','action'=>'info',],
            ],
        ],
    ],
];