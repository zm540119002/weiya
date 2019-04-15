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
    // 标签库标签结束标记
    'taglib_end'   => '}',
    'tpl_replace_string' => [
        //common    资源路径
        'public_js' => Request::domain() .'/static/common/js',
        'public_css' => Request::domain() .'/static/common/css',
        'public_img' => Request::domain() .'/static/common/img',
        //api    资源路径
        'api_common_js' => 'https://api.worldview.com.cn/static/common/js',
        'api_common_css' => 'https://api.worldview.com.cn/static/common/css',
        'api_common_img' => 'https://api.worldview.com.cn/static/common/img',
        //index     资源路径
        'index_js' => Request::domain() .'/static/index/js',
        'index_css' => Request::domain() .'/static/index/css',
        'index_img' => Request::domain() .'/static/index/img',
        //index_admin   资源路径
        'index_admin_js' => Request::domain() .'/static/index_admin/js',
        'index_admin_css' => Request::domain() .'/static/index_admin/css',
        'index_admin_img' => Request::domain() .'/static/index_admin/img',
        //h-ui  资源路径
        'hui_js' => Request::domain() .'/static/h-ui/js',
        'hui_css' => Request::domain() .'/static/h-ui/css',
        'hui_img' => Request::domain() .'/static/h-ui/images',
        'hui_lib' => Request::domain() .'/static/h-ui.lib',
        //h-ui.admin    资源路径
        'hui_admin_js' => Request::domain() .'/static/h-ui.admin/js',
        'hui_admin_css' => Request::domain() .'/static/h-ui.admin/css',
        'hui_admin_img' => Request::domain() .'/static/h-ui.admin/images',
        'hui_admin_skin' => Request::domain() .'/static/h-ui.admin/skin',
        //上传路径
        'public_uploads' => Request::domain() .'/uploads',
    ],
];
