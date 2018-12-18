<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------
//注意：模块不能覆盖
use think\facade\Request;
return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    'taglib_begin' => '{',
    // 标签库标签开始标记
    // 标签库标签结束标记
    'taglib_end'   => '}',
    'tpl_replace_string' => [
        //公共资源路径
        'public_img' => Request::domain() .'/static/common/img',
        'public_js' => Request::domain() .'/static/common/js',
        'public_css' => Request::domain() .'/static/common/css',
        'public_hui_admin' => Request::domain() .'/static/admin/hadmin',
        'public_admin_common_js' => Request::domain() .'/static/admin/common/js',
        'public_admin_common_css' => Request::domain() .'/static/admin/common/css',
        'public_admin_common_img' => Request::domain() .'/static/admin/common/img',
        //美尚平台首页资源路径
        'index_img' => Request::domain() .'/static/index/img',
        'index_js' => Request::domain() .'/static/index/js',
        'index_css' => Request::domain() .'/static/index/css',
        'index_admin_img' => Request::domain() .'/static/index_admin/img',
        'index_admin_js' => Request::domain() .'/static/index_admin/js',
        'index_admin_css' => Request::domain() .'/static/index_admin/css',
        //美容店家资源路径
        'store_img' => Request::domain() .'/static/store/img',
        'store_js' => Request::domain() .'/static/store/js',
        'store_css' => Request::domain() .'/static/store/css',
        'store_admin_img' => Request::domain() .'/static/store_admin/img',
        'store_admin_js' => Request::domain() .'/static/store_admin/js',
        'store_admin_css' => Request::domain() .'/static/store_admin/css',
        //供应商资源路径
        'factory_img' => Request::domain() .'/static/factory/img',
        'factory_js' => Request::domain() .'/static/factory/js',
        'factory_css' => Request::domain() .'/static/factory/css',
        'factory_admin_img' => Request::domain() .'/static/factory_admin/img',
        'factory_admin_js' => Request::domain() .'/static/factory_admin/js',
        'factory_admin_css' => Request::domain() .'/static/factory_admin/css',
        //从业人员资源路径
        'practitioner_img' => Request::domain() .'/static/practitioner/img',
        'practitioner_js' => Request::domain() .'/static/practitioner/js',
        'practitioner_css' => Request::domain() .'/static/practitioner/css',
        'practitioner_video' => Request::domain() .'/static/practitioner/video',
        'practitioner_admin_img' => Request::domain() .'/static/practitioner_admin/img',
        'practitioner_admin_js' => Request::domain() .'/static/practitioner_admin/js',
        'practitioner_admin_css' => Request::domain() .'/static/practitioner_admin/css',
        //商城资源路径
        'mall_img' => Request::domain() .'/static/mall/img',
        'mall_js' => Request::domain() .'/static/mall/js',
        'mall_css' => Request::domain() .'/static/mall/css',
        'mall_video' => Request::domain() .'/static/mall/video',
        'mall_admin_img' => Request::domain() .'/static/mall_admin/img',
        'mall_admin_js' => Request::domain() .'/static/mall_admin/js',
        'mall_admin_css' => Request::domain() .'/static/mall_admin/css',
        //公共上传路径
        'public_uploads' => Request::domain() .'/uploads',
        //彩绘图片路径
        'caihui_img' => Request::domain() .'/static/caihui/img',
        //彩绘js路径
        'caihui_js' => Request::domain() .'/static/caihui/js',
        //彩绘js路径
        'caihui_css' => Request::domain() .'/static/caihui/css',
        //维雅资源路径
        'weiya_img' => Request::domain() .'/static/weiya/img',
        'weiya_js' => Request::domain() .'/static/weiya/js',
        'weiya_css' => Request::domain() .'/static/weiya/css',

        //后台
        'public_admin_pc' => Request::domain() .'/static/admin_pc',
        'public_admin_pc_common_css' => Request::domain() .'/static/admin_pc/common/css',
        'public_admin_pc_common_img' => Request::domain() .'/static/admin_pc/common/img',
        'public_admin_pc_common_js' => Request::domain() .'/static/admin_pc/common/js',
    ],
];
