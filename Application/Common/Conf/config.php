<?php
require_once(dirname(__File__).'/constant.php');
return array (
		// '配置项'=>'配置值'
		// 数据库配置信息
		'DB_TYPE' => 'mysqli', // 数据库类型
		
		//服务器数据库本地开发配置
 		'DB_HOST' => 'localhost', // 服务器地址
 		'DB_NAME' => 'db_cloud', // 数据库名
 		'DB_USER' => 'root', // 用户名
 		'DB_PWD' => '', // 密码
		
		'DB_PORT' => 3306, // 端口
		'DB_PREFIX' => 'tb_',  // 数据库表前缀
		'DB_FIELDS_CACHE'=>true,
		'DB_SQL_BUILD_CACHE' => true,
		'DB_SQL_BUILD_CACHE_TIME' => 30,//前端接口缓存时间
		'DB_SQL_BUILD_QUEUE' => 30,
		'DB_SQL_BUILD_LENGTH' => 20,
		'DATA_CACHE_TYPE' => 'file',
		'DATA_CACHE_TIME' => 60,
		
 		'SHOW_PAGE_TRACE' =>true,
		'LOG_RECORD' => true, // 开启日志记录
		'LOG_LEVEL'  =>'SQL,EMERG,ALERT,CRIT,ERR',
		'LOG_EXCEPTION_RECORD' => true,
		'URL_CASE_INSENSITIVE' =>true,
		'SESSION_TYPE'		=> 'DB',
		'SESSION_OPTIONS' => array(
		'expire'	=>	2592000,
		),
		
		'COOKIE_EXPIRE'=>'2592000',
		'COOKIE_PATH'=>'/',
		

		'MAIL_ADDRESS'=>"yf099@kenxinda.org", // 邮箱地址
		'MAIL_SMTP'=>'192.168.0.250', // 邮箱SMTP服务器
		'MAIL_LOGINNAME'=>'yf099', // 邮箱登录帐号
		'MAIL_PASSWORD'=>'yf099test', // 邮箱密码
		'MAIL_NICKNAME'=>'垦鑫达appstore官方',	//昵称
		
		'DEFAULT_MODULE'        =>  'Home',  // 默认模块
		'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
		'DEFAULT_ACTION'        =>  'index', // 默认操作名称

		'ERROR_PAGE'      =>  __ROOT__.'/404.html',	// 错误定向页面
		'AWS_BUCKET'    => 'userserver.kenxinda.com',
		'AWS_BASE_PATH' => '/Public/Upload/',
		'IS_AWS_URL'     => false,
		'IS_REDIS_CACHE' => false,
		'REDIS_CACHE_PREFIX' => 'userserver',
		'REDIS_CACHE_HOST' => '127.0.0.1',
		'REDIS_CACHE_PORT' => 6379,
		'REDIS_CACHE_EXPIRE' => 86400,		
		'REDIS_PASSWORD' => '2015kxdp&.1026',
		
		'USER_CENTER_PREFIX'=>'http://localhost/userserver/',//用户中心前缀
		
		'BAIDU_PUSH_APIKEY' =>'tLoeiQ3c0l3kGkZ3bSntGbPy',   //baidu push 其他测试
        'BAIDU_PUSH_SECRETKEY' =>'2VGCk7wfh8kOQYVp9bAtTWygU05Gl0ht',
		
		'RESOURCE_PREFIX_CONFIG'   => array('default'=>'http://img.kenxinda.com/userserver/',
											'user'=>'http://img.kenxinda.com/userserver/'
											),//原始资源站点前缀
        'RESOURCE_PREFIX_LANG_CONFIG'   => array('zh-cn'=>array('http://download.kenxinda.com/userserver/'),
        										 'zh-hk'=>array('http://download.kenxinda.com/userserver/'),
												 'en-us'=>array('http://img.kenxinda.com/userserver/')
                                                ),//资源站点前缀
);

