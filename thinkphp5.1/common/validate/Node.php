<?php
namespace common\validate;

class Node extends \think\Validate
{
    //验证规则
    protected $rule = [
        'name'  => [
            'require',
            'max' => 64,
        ],
        'path'  => [
            'require',
            'regex' => '/^[A-Za-z0-9]\/]+$/',
            'max'=> 256,
        ],
        'remark'  => [
            'max' => 512,
        ],
    ];
    //验证消息
    protected $message  =   [
        'name.require' => '名称必须！',
        'name.max' => '名称最多不能超过64个字符！',
        'path.require' => '路径必须！',
        'path.regex' => '路径只能是字母或数字！',
        'path.max' => '路径最多不能超过256个字符！',
        'remark.max' => '备注最多不能超过512个字符！',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'edit'  =>  [
            'name',
            'path',
            'remark',
        ],
    ];
}