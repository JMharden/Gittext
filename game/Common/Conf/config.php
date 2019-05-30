<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_MODULE'    => 'Home', //默认模块
	'MODULE_ALLOW_LIST'	=> array('Home', 'Admin','Api'),
	'LOAD_EXT_CONFIG' 	=> 'db',
	'URL_MODEL'			=> 0,
	'DATA_CACHE_TYPE'	=> 'Redis',
	'DATA_CACHE_TIME'	=> 7000,
	'SAFE_SALT'			=> '/@DragonDean/#', // 全局盐值,
	'ALLOWED_FILE_TYPES'=> array('jpg','gif','png','bmp','jpeg','webp'), // 允许上传的文件格式
	// 'LIST_TPL_PATH'		=> APP_PATH . 'Home/View/Index/list',
	'SMS_USER'			=> 'zhihuinongchang', // 短信账号
	'SMS_PASS'			=> 'zhihuinongchang', // 短信密码
	'SMS_SIGN'			=> '智慧农场' // 短信签名

);