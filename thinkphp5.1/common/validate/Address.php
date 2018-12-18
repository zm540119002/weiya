<?php
namespace common\validate;

class Address extends \think\Validate
{
    /**
     * @var array
     * 
     */
    protected $rule = [
        'name'  =>  'require|max:18|unique:\common\model\Brand',
        'brand_img' =>  'require',
        'category_id_1' =>  'require',
        'certificate' =>  'require',
    ];
    protected $message  =   [
        'name.require' => '商标全称必须填写',
        'name.max'     => '商标全称最多不能超过18字',
        'name.unique'     => '此品牌名字已存在',
        'brand_img.require'   => '请上传商标图片',
        'category_id_1.require'   => '请选择商标所属分类',
        'certificate.require'   => '请上传证书',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'add'  =>  [
            'foreign_id',
            'store_type',
            'run_type',
        ],
        'edit'  =>  [
            'auth_status',
        ],
    ];
}