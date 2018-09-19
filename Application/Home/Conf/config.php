<?php
return array(
	//跳转模块	
	'DATA_CACHE_TIME' => 2,	//数据库缓存查询--300秒
	'TMPL_ACTION_SUCCESS'=> 'Index/tipMessage',
	'TMPL_ACTION_ERROR'=> 'Index/tipMessage',
		
	//多语言配置
	'LANG_SWITCH_ON'     =>     true,    //开启语言包功能
	'LANG_AUTO_DETECT'   =>     true, // 自动侦测语言
	'DEFAULT_LANG'       =>    'en-us', // 默认语言
	'LANG_LIST'          =>    'en-us,zh-cn,zh-hk', //必须写可允许的语言列表
	'VAR_LANGUAGE'       =>    'l', // 默认语言切换变量
	
	'SHOW_PAGE_TRACE' =>true,
	'FIRE_SHOW_PAGE_TRACE' => true,
); 