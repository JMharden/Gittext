<?php

	// $mysql_server_name="120.27.222.61"; //数据库服务器名称
	// $mysql_username="ceshi_com"; // 连接数据库用户名
	// $mysql_password="8hhFNiPbJd"; // 连接数据库密码
	// $mysql_database="ceshi_com"; // 数据库的名字 
   
 //    // 连接到数据库
 //    $conn=mysql_connect($mysql_server_name, $mysql_username,
 //                        $mysql_password);
                        
 //     // 从表中提取信息的sql语句
 //    $strsql="SELECT * FROM `dd_user` where id = 1";
 //    // 执行sql查询
 //    $result=mysql_db_query($mysql_database, $strsql, $conn);
 //    // 获取查询结果
 //    $row=mysql_fetch_row($result);
 //    // 释放资源
 //    mysql_free_result($result);
 //    // 关闭连接
 //    mysql_close($conn); 
 //    print_r($row);
 //    echo 22;
 //    die;
// echo '<!DOCTYPE html><html lang="en"><head>
// <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>微信安全检测中...</title></head></html>';
// die;
/**

*	微信三级分销商城

*

*	http://www.besttool.cn

*

*	Q Q : 2045003697

*

*	微信: dragondean

*

================================================================================



	使用协议：

	遇到问题请联系售后，不要随意改动程序源代码，一经改动不再享受售后保障服务。

	您购买的是程序使用权，版权归开发者所有。未经同意不得转售或者修改后再次销售

	使用本程序则视为接受本协议

	

================================================================================

*/

define('BASE_PATH',dirname(__FILE__));
require(BASE_PATH.'/database.php');
if(get_magic_quotes_gpc()){

	function stripslashes_deep($value){

		$value = is_array($value) ?

		array_map('stripslashes_deep', $value) :

		stripslashes($value);

		return $value;

	}

	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);

	$_POST = array_map('stripslashes_deep', $_POST);

	$_GET = array_map('stripslashes_deep', $_GET);

	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);

}



unset($_GET['m']);

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('PHP 版本必须大于等于5.3.0 !');



define('', 'power');


ini_set('display_errors', false);

if(!APP_DEBUG){

	ini_set('display_errors', false);

}



header("Content-type:text/html;charset=utf-8");


define('APP_PATH','./Application/');

require './#DFrame/DFrame.php';