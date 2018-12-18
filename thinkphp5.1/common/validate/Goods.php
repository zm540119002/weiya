<?php
namespace common\validate;

class Goods extends \think\Validate
{
    /**
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|max:18',
        'trait' =>  'require|max:1000',
        'sale_price' =>  'require|float',
        'retail_price' =>  'require|float',
        'thumb_img' =>  'require',
        'main_img' =>  'require',
        'category_id_1' =>  'require',
        'parameters' =>  'require|max:1000',
        'details_img' =>  'require',

    ];
    protected $message  =   [
        'name.require' => '产商全称必须填写',
        'name.max'     => '产商全称最多不能超过18字',
        'trait.require'   => '请填写商品特点',
        'trait.max'   => '商品特点不能超过1000字',
        'sale_price.require'   => '请填写销售价格',
        'sale_price.float'   => '价格格式有误',
        'retail_price.require'   => '请填写零售价格',
        'retail_price.float'   => '价格格式有误',
        'thumb_img.require'   => '请上传缩略图',
        'category_id_1.require'   => '请设置分类标签',
        'main_img.require'   => '请上传首焦图',
        'parameters.require'   => '请填写商品参数',
        'parameters.max'   => '商品参数不能超过1000字',
        'details_img.require'   => '请上传商品详情图',
    ];
}