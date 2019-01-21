<?php
namespace app\index\validate;

class NeedsMessage extends \think\Validate
{
    //验证规则
    protected $rule = [
        'name'  => [
            'require',
            'max' => 30,
        ],
        'mobile'=>[
            'mobile',
        ],
        'message'=>[
            'require',
        ],
    ];
    //验证消息
    protected $message  =   [
        'name.require' => '名称必须！',
        'name.max' => '名称最多不能超过64个字符！',
        'message.require' => '留言不能为空！',
    ];

}