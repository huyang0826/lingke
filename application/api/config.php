<?php
//配置文件
return [
	// 视图输出字符串内容替换
	'view_replace_str'       => [
	    '__ROOT__'      =>  '',
	    '__PUBLIC__'    =>  '/public/',
	    '__STATIC__'    =>  '/static/admin',
	    '__JS__'    =>  '/static/admin/js',
	    '__CSS__'    =>  '/static/admin/css',
	    '__IMG__'    =>  '/static/admin/img',
	],
	  // 控制器类后缀
    'controller_suffix'      => true,
    'ADMIN_MENU_LIST'      =>false,//缓存菜单
];