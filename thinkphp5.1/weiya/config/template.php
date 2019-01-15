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
        //公共上传路径
        'public_uploads' => Request::domain() .'/uploads',
        //维雅资源路径
        'weiya_img' => Request::domain() .'/static/weiya/img',
        'weiya_js' => Request::domain() .'/static/weiya/js',
        'weiya_css' => Request::domain() .'/static/weiya/css',
        //hui-ui
        'hui_lib' => Request::domain() .'/static/h-ui.lib',
        'hui_js' => Request::domain() .'/static/h-ui/js',
        'hui_css' => Request::domain() .'/static/h-ui/css',
        'hui_img' => Request::domain() .'/static/h-ui/img',
        //hui-admin-ui
        'hui_admin_js' => Request::domain() .'/static/admin/h-ui.admin/js',
        'hui_admin_css' => Request::domain() .'/static/admin/h-ui.admin/css',
        'hui_admin_img' => Request::domain() .'/static/admin/h-ui.admin/img',
        'hui_admin_skin' => Request::domain() .'/static/admin/h-ui.admin/skin',
    ],
];
