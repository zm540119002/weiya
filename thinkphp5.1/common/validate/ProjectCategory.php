<?php
namespace common\validate;

class ProjectCategory extends \think\Validate
{
    //验证规则
    protected $rule = [
        'name'  => [
            'require',
            'max' => 90,
        ],
        'remark'  => [
            'max' => 255,
        ],
    ];
    //验证消息
    protected $message  =   [
        'name.require' => '名称必须！',
        'name.max' => '名称最多不能超过90个字符！',
        'remark.max' => '备注最多不能超过255个字符！',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'edit'  =>  [
            'name',
            'remark',
        ],
    ];
}