<?php
namespace common\validate;

class Promotion extends \think\Validate
{
    /**
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|max:50',
        'first_img' =>  'require',
        'second_img' =>  'require',
        'goods' =>  'require',
        'start_time' =>  'require',
        'end_time' =>  'require',
    ];
    protected $message  =   [
        'name.require' => '促销活动名称必须填写',
        'name.max'     => '促销活动名称最多不能超过50字',
        'first_img.require'   => '请上传图片',
        'second_img.require'   => '请上传图片2',
        'goods.require'   => '请链接商品',
        'start_time.require'   => '请填写促销开始时间',
        'end_time.require'   => '请填写促销结束时间',
    ];
}