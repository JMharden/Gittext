<?php
/*
 *	公共函数
 *
 */

function createQRcode($save_path,$qr_data='PHP QR Code :)',$qr_level='L',$qr_size=4,$save_prefix='qrcode'){
    if(!isset($save_path)) return '';
    //设置生成png图片的路径
    $PNG_TEMP_DIR = & $save_path;
    //导入二维码核心程序
    vendor('phpqrcode.class#phpqrcode');  //注意这里的大小写哦，不然会出现找不到类，PHPQRcode是文件夹名字，class#phpqrcode就代表class.phpqrcode.php文件名
    //检测并创建生成文件夹
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR);
    }
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
        $errorCorrectionLevel = & $qr_level;
    }
    $matrixPointSize = 4;
    if (isset($qr_size)){
        $matrixPointSize = & min(max((int)$qr_size, 1), 10);
    }
    if (isset($qr_data)) {
        if (trim($qr_data) == ''){
            die('data cannot be empty!');
        }
        //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
        $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        //开始生成
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    } else {
        //默认生成
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    if(file_exists($PNG_TEMP_DIR.basename($filename)))
        return basename($filename);
    else
        return FALSE;
 }
 function getsn($merchant){
	return generate_randsn();
 }
 
 function sign_rsa($keyStr16){ 
    $signMsg;
    $pubilc_keys = openssl_get_publickey(file_get_contents('dpay/cer/public_key_2048.pem'));
	openssl_public_encrypt($keyStr16, $signMsg, $pubilc_keys,OPENSSL_PKCS1_PADDING); //×¢²áÉú³É¼ÓÃÜÐÅÏ¢
    return base64_encode($signMsg); //base64×ªÂë¼ÓÃÜÐÅÏ¢
 }
 
  function decode_rsa($keySignStr){ 
    $signMsg;
    $private_keys = openssl_pkey_get_private(file_get_contents('dpay/cer/rsaPKCS8PrivateKey.pem'));
	openssl_private_decrypt(base64_decode($keySignStr), $signMsg, $private_keys); //×¢²áÉú³É¼ÓÃÜÐÅÏ¢
    return $signMsg; //base64×ªÂë¼ÓÃÜÐÅÏ¢
 }

 function sign_aes($aes_content, $keyStr16){ 
	$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');      
	$iv_size = mcrypt_enc_get_iv_size($cipher);    
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	if (mcrypt_generic_init($cipher, $keyStr16, $iv) != -1)    
	{ 
		$cipherText = mcrypt_generic($cipher,pad2Length($aes_content,16));    
		mcrypt_generic_deinit($cipher);    
		mcrypt_module_close($cipher);    
		   
		// Display the result in hex.    
		//printf("encrypted result:%s\n",base64_encode($cipherText));    
        return base64_encode($cipherText);		
	}  
 } 
 
  function decode_aes($keyStr16,$aes_content){ 
	$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');      
	$iv_size = mcrypt_enc_get_iv_size($cipher);    
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	if (mcrypt_generic_init($cipher, $keyStr16, $iv) != -1)    
	{ 
		$cipherText = mdecrypt_generic($cipher,base64_decode($aes_content));    
		mcrypt_generic_deinit($cipher);    
		mcrypt_module_close($cipher);    
		   
		// Display the result in hex.    
		//printf("encrypted result:%s\n",base64_encode($cipherText));    
        return $cipherText;		
	}  
 } 
 
 function pad2Length($text, $padlen){    
    $len = strlen($text)%$padlen;    
    $res = $text;    
    $span = $padlen-$len;    
    for($i=0; $i<$span; $i++){    
        $res .= chr($span);    
    }    
    return $res;    
} 
 
 function sign_sha1($sign_md5){
	$signMsg;
	$private_keys = openssl_pkey_get_private(file_get_contents('dpay/cer/rsaPKCS8PrivateKey.pem'));
	openssl_sign($sign_md5, $signMsg, $private_keys,OPENSSL_ALGO_SHA1); //×¢²áÉú³É¼ÓÃÜÐÅÏ¢
    return base64_encode($signMsg); //base64×ªÂë¼ÓÃÜÐÅÏ¢
 }

 function generate_randsn( $length = 28 ) { 
	// ÃÜÂë×Ö·û¼¯£¬¿ÉÈÎÒâÌí¼ÓÄãÐèÒªµÄ×Ö·û 
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$sn = ''; 
	for ( $i = 0; $i < $length; $i++ ) 
	{ 
		// ÕâÀïÌá¹©Á½ÖÖ×Ö·û»ñÈ¡·½Ê½ 
		// µÚÒ»ÖÖÊÇÊ¹ÓÃ substr ½ØÈ¡$charsÖÐµÄÈÎÒâÒ»Î»×Ö·û£» 
		// µÚ¶þÖÖÊÇÈ¡×Ö·ûÊý×é $chars µÄÈÎÒâÔªËØ 
		// $password .= substr($chars, mt_rand(0, strlen($chars) ¨C 1), 1); 
		$sn .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
	} 
	return $sn; 
} 
function get_url_with_out_domain() {
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $relate_url;
}

// function x_http_get($url) {
//     $curl = curl_init(); // 启动一个CURL会话
//     curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
//     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
//     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
//     curl_setopt($curl, CURLOPT_USERAGENT, "'Mozilla/5.0 (Linux; U; Android 2.3.7; zh-cn; c8650 Build/GWK74) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1s'"); // 模拟用户使用的浏览器
//     curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
//     curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
//     //curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
//     //curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); // Post提交的数据包
//     curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
//     curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
//     $res = curl_exec($curl);
//     curl_close($curl);
//     return $res;
// }

function ms_ec_time() {
    list($tmp1, $tmp2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
}

function xmlToArray($xml)
{
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}



// 对字符串进行加盐散列加密


function xmd5($str){


	return md5(md5($str).C('SAFE_SALT'));


}

// 返回最新比特币成交价格
function get_newbtc() {
	$info = M('btclist')->order('id desc')->find();
	return $info['last'];
}

// 获得当前的url
function get_current_url($flag=0) 
{
    $domain 		= M('domain')->where(array('is_home'=>0,'is_qr_code'=>1,'is_lock'=>0))->order('id DESC')->find();
    $domain_jump 	= M('domain')->where(array('is_home'=>1,'is_qr_code'=>0,'is_lock'=>0))->order('id DESC')->find();

    if ($domain['domain']==$_SERVER['HTTP_HOST']) {
        $url="http://".$domain_jump['domain'];
    } else {
        $url = "http://" . $_SERVER['HTTP_HOST'];
    }
    if ($flag==0) {
       $url .= $_SERVER['REQUEST_URI'];
    }
	return $url;
}
function get_qqnum(){
	$data = send_get('http://www.77tj.org/api/tencent/onlineim');
   // $list = json_decode($data,true);
    return $data;
}
function get_btc(){
     $url = 'https://www.okcoin.cn/api/v1/ticker.do?symbol=btc_cny';
     $info  = send_get($url);
     $list = json_decode($info,true);
     return $list;
}
function get_btc_list(){
	 $url = 'https://www.okcoin.cn/api/v1/trades.do?symbol=btc_cny&since=600';
     $info  = send_get($url);
     $list = json_decode($info,true);
     return $list;
}
function get_btc_new(){
	 $url = 'https://www.okcoin.cn/api/v1/kline.do?symbol=btc_cny&type=1min';
     $info  = send_get($url);
     $list = json_decode($info,true);
     return $list;
}
function send_get($url){
	$ch = curl_init($url) ;  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 
    $output = curl_exec($ch) ; 
    return $output;
}
function send_post($url,$data=null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $info = curl_exec($ch);
    curl_close($ch);
    return $info;
}

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
//返回当前的毫秒时间戳
function msectime() {
       list($tmp1, $tmp2) = explode(' ', microtime());
       return (float)sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
}
 

/** 格式化时间戳，精确到毫秒，x代表毫秒 */

function microtime_format($format = 'x', $utimestamp = null)
{
    if (is_null($utimestamp))
           $utimestamp = microtime(true);
 
       $timestamp = floor($utimestamp);
       $milliseconds = round(($utimestamp - $timestamp) * 1000000);
 
       return date(preg_replace('`(?<!\\\\)x`', $milliseconds, $format), $timestamp);
}
//发送微信消息


function sendwen($openid,$msg){


    $token = wx_token();


    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";


            $msg = str_replace('"', '\\"', $msg);


            $data = '{"touser":"' . $openid . '","msgtype":"text","text":{"content":"' . $msg . '"}}';


    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $url);


    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');


    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);


    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);


    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $info = curl_exec($ch);


    curl_close($ch);


    return $info;


}


function wxuser($openid){


  $token = wx_token();  


  $info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN');


  return json_decode($info,true);


}


//获取token


function wx_token(){


    $appid = $GLOBALS['_CFG']['mp']['appid']; 


    $appsecret = $GLOBALS['_CFG']['mp']['appsecret']; 

    // 尝试从缓存读取
	$cache = S($appid.'_accesstoken');
	if($cache && $cache['expire'] > time() && !empty($cache['accesstoken'])){
	 $chong = session('chongxin');
	 if($chong!=1){
	  return $cache['accesstoken'];
     }
	}
    $token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;


    $token_content = file_get_contents($token_url);


    $token = @json_decode($token_content, true);

    $chong = session('chongxin');
	 if($chong==1){
	  session('chongxin',0);
     }
    S($appid.'_accesstoken', array('accesstoken' => $token['access_token'], 'expire' => time() +$token['expires_in']));
    return $token['access_token'];


}


// 补全url


function complete_url($url){


	$host = $GLOBALS['_CFG']['web_site']['url'];


	if(substr($url,0,1) == '.'){


		return $host.__ROOT__.substr($url,1);


	}


	elseif(substr($url,0,7) != 'http://' && substr($url,0,8) != 'https://'){


		return $host.$url;


	}


	else{


		return $url;


	}


}








// 根据GET参数生成一个支付URL签名


function make_url_sign($get){


	unset($get['sign']);


	ksort($get, SORT_STRING);


	$string =  urldecode(http_build_query($get));


	$string = xmd5($string);


	return substr($string,16,16);


}





// 根据GET参数检查URL签名


function check_url_sign($get){


	$sign = $get['sign'];


	if(empty($sign))return false;


	unset($get['sign']);


	$new_sign = make_url_sign($get);


	return $new_sign == $sign ? true : false;


}








// 根据自定义菜单类型返回名称


function get_selfmenu_type($type){


	$type_name = '';


	switch($type){


		case 'click':


			$type_name = '点击推事件';


			break;


		case 'view':


			$type_name = '跳转URL';


			break;


		case 'scancode_push':


			$type_name = '扫码推事件';


			break;


		case 'scancode_waitmsg':


			$type_name = '扫码推事件且弹出“消息接收中”提示框';


			break;


		case 'pic_sysphoto':


			$type_name = '弹出系统拍照发图';


			break;


		case 'pic_photo_or_album':


			$type_name = '弹出拍照或者相册发图';


			break;


		case 'pic_weixin':


			$type_name = '弹出微信相册发图器';


			break;


		case 'location_select':


			$type_name = '弹出地理位置选择器';


			break;


		default : $type_name = '不支持的类型';


	}


	return $type_name;


}





// 获取列表模板列表


function get_list_tpl(){


	$tpl_path = C('list_tpl_path');


	$dir_list = scandir($tpl_path);


	foreach($dir_list as $k => $v){


		if(in_array($v,array('.','..'))  || !is_dir($tpl_path.'/'.$v)){


			unset($dir_list[$k]);


		}


	}


	return $dir_list;


}





// 返回订单的状态


function get_order_status($status){


	$arr = array(


		-1 => '已取消',


		0 => '待审核',


		1 => '待服务',


		2 => '已完成'


	);


	if(!empty($arr[$status]))return $arr[$status];


	else return '未知';


}





// 发送短信


function send_sms($mobile,$msg,$sign = null, $orig = false){


	if(empty($sign))$sign = C('SMS_SIGN');


	$msg = "【".$sign."】".$msg;


	$url = "http://api.smsbao.com/sms?u=".C('SMS_USER')."&p=".md5(C('SMS_PASS'))."&m={$mobile}&c=".urlencode($msg);


	$rt =  file_get_contents($url);


	M('sms_log') -> add(array(


		'mobile' => $mobile,


		'msg' => $msg,


		'result' => $rt,


		'create_time' => NOW_TIME


	));


	if($orig)return $rt;


	return $rt == '0' ? true : false;


}





// 返回短信状态


function get_sms_result($rt){


	$code = array(


		0  => '发送成功',


		30 => '密码错误',


		40 => '账号不存在',


		41 => '余额不足',


		42 => '帐号过期',


		43 => 'IP地址限制',


		50 => '内容含有敏感词',


		51 => '手机号码不正确'


	);


	$return = !empty($code[$rt]) ? $code[$rt] : '其他错误';


	return  $return;


}





// 记录错误日志


function elog($msg){


	$log_file = "./log/error/".date('Ym/d').".log";


	if(!is_dir(dirname($log_file))){


		mkdir(dirname($log_file),0777,1);


	}


	$log_arr = array(


		date('H:i:s'),


		$msg,


		get_current_url(),


		$_SERVER['HTTP_USER_AGENT']


	);


	file_put_contents($log_file,implode("\t",$log_arr)."\n",FILE_APPEND);


}








// 发送客服提示消息


function send_msg($openid,$str){


	$dd = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);


	$accesstoken = $dd -> getaccesstoken();


	$msg = array(


		'touser' => $openid,


		'msgtype' => 'text',


		'text' => array(


			'content' => $str


		)


	);


	$dd -> custommsg($msg);


}





// 获得用户信息,缓存（迟钝）


function get_user_info($user_id){


	$user_info = M('user') -> find($user_id);


	return $user_info;


	/*


	$key = S('user_info_'.$user_id);


	if(S($key)){


		return S($key);


	} else{


		$user_info = M('user') -> find($user_id);


		S($key,$user_info);


		return $user_info;


	}


	*/


}





// 微信企业转帐


function mch_wxpay($sn,$openid,$money,$remark = null){


	$param = array(


		'mch_appid' => $GLOBALS['_CFG']['mp']['appid'],


		'mchid' => $GLOBALS['_CFG']['mp']['mch_id'],


		'partner_trade_no' => $sn,


		'openid' => $openid,


		'check_name' => 'NO_CHECK', // 不验证名字


		'amount' => intval($money*100), // 金额，分


		'desc' => empty($remak) ? '系统转帐' : $remak,


	);


	


	$dd = new \Common\Util\ddwechat;


	$dd -> setParam($GLOBALS['_CFG']['mp']);
    if(substr($GLOBALS['_CFG']['mp']['cert'], 0,1)=='.'){
    	$GLOBALS['_CFG']['mp']['cert'] = substr($GLOBALS['_CFG']['mp']['cert'], 1);
    }

	$ssl = array(


		'sslcert' => getcwd() . $GLOBALS['_CFG']['mp']['cert'].'apiclient_cert.pem',


		'sslkey'  => getcwd() . $GLOBALS['_CFG']['mp']['cert'].'apiclient_key.pem',


	);


	$rt = $dd -> mch_pay($param, $ssl);


	if($rt['return_code'] == 'SUCCESS' && $rt['result_code'] == 'SUCCESS'){


		$status = 1;


	}


	else{


		$status = 0;


	}


	


	return array(


		'status' => $status,


		'return_code' => $rt['return_code'],


		'result_code' => $rt['result_code'],


		'return_msg'  => $rt['return_msg'],


		'err_code_des'  => $rt['err_code_des'],


		'err_code'  => $rt['err_code'],


		'payment_no'  => $rt['payment_no'], // 微信订单号


	);


}





// 记录财务日志


function flog($user_id,$type,$money,$action,$remark = null){


	if(CLI === true){


		$time = time();


	}else{


		$time = NOW_TIME;


	}


	M('finance_log') -> add(array(


		'user_id' => $user_id,


		'type' => $type,


		'money' => $money,


		'action' => $action,


		'create_time' => $time,


		'remark' => $remak


	));


}





// 取得财务动作名称


function get_flog_name($action){


	$arr = array(


		1 => '扩建土地',


		2 => '购买植物',


		3 => '购买肥料',


		4 => '采摘收入',


		5 => '提现',


		6 => '参与抽抽乐',


		7 => '抽瞅乐奖金',


		8 => '抽瞅乐推荐奖',


		9 => '偷菜',


		10 => '公排占点',


		11 => '施肥奖励',


		100 => '懒人奖',


		101 => '扩建分成',


		102 => '植物分成',


		103 => '肥料分成',


	);


	return $arr[$action];


}


// 取得微信支付参数


function get_wxpay_parameters($sn,$total,$openid,$remark = null){
 return true;
	if(S('wxpay_'.$sn))return S('wxpay_'.$sn);


	// 微信支付


	$jsapi = new \Common\Util\wxjspay;


	$param = $GLOBALS['_CFG']['mp'];


	$param['key'] = $GLOBALS['_CFG']['mp']['key'];


	


	$param['openid'] = $openid;


	$param['body'] = empty($remark) ? '在线支付' : $remark;


	$param['out_trade_no'] = $sn;


	$param['total_fee'] = $total * 100;


	$param['notify_url'] = $GLOBALS['_CFG']['web_site']['url'].'/wx_notify.php';


	$jsapi -> set_param($param);


	$uo = $jsapi -> unifiedOrder('JSAPI');


	


	// 发生错误则提示


	if(!$uo){


		elog('[wxpay]'.$jsapi -> errmsg);


		return false ;


	}


	


	$jsapi_params = $jsapi -> get_jsApi_parameters();


	if($jsapi_params){


		S('wxpay_'.$sn,$jsapi_params);


	}


	return $jsapi_params;


}


//根据id获取用户信息


function get_user($id){


   $user_info = M('user')->where(array('id'=>$id))->find();


   return $user_info;


}

function GetMonth($sign="1")
{
    //得到系统的年月
    $tmp_date=date("Ym");
    //切割出年份
    $tmp_year=substr($tmp_date,0,4);
    //切割出月份
    $tmp_mon =substr($tmp_date,4,2);
    $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
    $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
    if($sign==0){
        //得到当前月的下一个月
        return $fm_next_month=date("Ym",$tmp_nextmonth);
    }else{
        //得到当前月的上一个月
        return $fm_forward_month=date("Ym",$tmp_forwardmonth);
    }
}
// 分成 type => 1表示扩建，2表示购买植物，3表示购买肥料



// 分成 type => 1表示公排直推，2表示静态100，3表示静态500 4代表静态1500

function expense($user_info,$money,$type){
    $config = explode(';',$GLOBALS['_CFG']['web_site']['expense']);
    //1
    if($user_info['parent1']){
    $parent_info = M('user') ->where(array('id'=>$user_info['parent1']))->find();
    $expense = $config[0]*$money/100;
    if($parent_info){

        $expense= array(

            'user_id' => $parent_info['id'],

            'buyer_id' => $user_info['id'],

            'money' => $expense,

            'level' => 1,

            'create_time' => time(),

            'type' => $type

        );
        $save = array('money'=>$parent_info['money']+$expense['money'],'expense'=>$parent_info['expense']+$expense['money']);
        M('user')->where(array('id'=>$parent_info['id']))->save($save);
        $info = M('expense')->add($expense);
        if($info){
            $msg = "你的下级 ".$user_info['nickname']." 成功下单 恭喜您获到佣金奖".$expense['money']."元！\n购买时间:".date('Y-m-d H:i:s');
            sendwen($parent_info['openid'],$msg);
        }

     } 
    }
   //2
    if($user_info['parent2']){
    $parent_info = M('user') ->where(array('id'=>$user_info['parent2']))->find();
    $expense = $config[1]*$money/100;
    if($parent_info){

        $expense= array(

            'user_id' => $parent_info['id'],

            'buyer_id' => $user_info['id'],

            'money' => $expense,

            'level' => 2,

            'create_time' => time(),

            'type' => $type

        );
        M('user')->where(array('id'=>$parent_info['id']))->save(array('money'=>$parent_info['money']+$expense['money'],'expense'=>$parent_info['expense']+$expense['money']));
        $info = M('expense')->add($expense);
        if($info){
            $msg = "你的下级 ".$user_info['nickname']." 成功下单 恭喜您获到佣金奖".$expense['money']."元！\n购买时间:".date('Y-m-d H:i:s');
            sendwen($parent_info['openid'],$msg);
        }

     } 
    }
    //3
    if($user_info['parent3']){
    $parent_info = M('user') ->where(array('id'=>$user_info['parent3']))->find();
    $expense = $config[2]*$money/100;
    if($parent_info){

        $expense= array(

            'user_id' => $parent_info['id'],

            'buyer_id' => $user_info['id'],

            'money' => $expense,

            'level' => 3,

            'create_time' => time(),

            'type' => $type

        );
        M('user')->where(array('id'=>$parent_info['id']))->save(array('money'=>$parent_info['money']+$expense['money'],'expense'=>$parent_info['expense']+$expense['money']));
        $info = M('expense')->add($expense);
        if($info){
            $msg = "你的下级 ".$user_info['nickname']." 成功下单 恭喜您获到佣金奖".$expense['money']."元！\n购买时间:".date('Y-m-d H:i:s');
            sendwen($parent_info['openid'],$msg);
        }

     } 
    }
    //4
    if($user_info['parent3']){
    $parent_info = M('user') ->where(array('id'=>$user_info['parent4']))->find();
    $expense = $config[3]*$money/100;
    if($parent_info){

        $expense= array(

            'user_id' => $parent_info['id'],

            'buyer_id' => $user_info['id'],

            'money' => $expense,

            'level' => 4,

            'create_time' => time(),

            'type' => $type

        );
        M('user')->where(array('id'=>$parent_info['id']))->save(array('money'=>$parent_info['money']+$expense['money'],'expense'=>$parent_info['expense']+$expense['money']));
        $info = M('expense')->add($expense);
        if($info){
            $msg = "你的下级 ".$user_info['nickname']." 成功下单 恭喜您获到佣金奖".$expense['money']."元！\n购买时间:".date('Y-m-d H:i:s');
            sendwen($parent_info['openid'],$msg);
        }

     } 
    }
    //5
    if($user_info['parent5']){
    $parent_info = M('user') ->where(array('id'=>$user_info['parent5']))->find();
    $expense = $config[4]*$money/100;
    if($parent_info){

        $expense= array(

            'user_id' => $parent_info['id'],

            'buyer_id' => $user_info['id'],

            'money' => $expense,

            'level' => 5,

            'create_time' => time(),

            'type' => $type

        );
        M('user')->where(array('id'=>$parent_info['id']))->save(array('money'=>$parent_info['money']+$expense['money'],'expense'=>$parent_info['expense']+$expense['money']));
        $info = M('expense')->add($expense);
        if($info){
            $msg = "你的下级 ".$user_info['nickname']." 成功下单 恭喜您获到佣金奖".$expense['money']."元！\n购买时间:".date('Y-m-d H:i:s');
            sendwen($parent_info['openid'],$msg);
        }

     } 
    }
    
}





// 根据用户信息取得推广二维码路径信息


function get_qrcode_path($user){


	if(!is_array($user)){


		$user = M('user') -> find($user);


	}


	


	$path = './Public/qrcode/'.date('ym/d/',$user['sub_time']);


	return array(


			'path'		=> $path,


			'new'		=> $path.$user['id'].'_dragondean.jpg',


			'head' 		=> $path.$user['id'].'_head.jpg',


			'qrcode'	=> $path.$user['id'].'_qrcode.jpg',


			'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path,1)


		);


}


function cget($url){


  $ch=curl_init();


  curl_setopt($ch,CURLOPT_URL,$url);


  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);


  curl_setopt($ch,CURLOPT_HEADER,0);


  $output = curl_exec($ch);


  curl_close($ch);


  return $output;


}


function cpost($url,$data=''){


    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $url);


    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');


    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);


    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);


    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $info = curl_exec($ch);


    curl_close($ch);


    return $info;


}


//获取参数二维码


function cancode($uid){


    $token = wx_token();    


    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;


    $data = array('action_name' => 'QR_LIMIT_STR_SCENE', 'action_info' => array('scene' => array('scene_str' => 'user_'.$uid)));


    $ret1 = cpost($url, json_encode($data));


    $content = @json_decode($ret1, true);


    $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($content['ticket']);


    $imgDir = './Public/code/';  


    $filename=$uid.".jpg";///要生成的图片名字          


    $jpg = $url;


    $file = copy($url,($imgDir.$filename));//打开文件准备写入     


    return $imgDir.$filename;


}

function tuidan($openid,$name,$price){
   $msg = '恭喜你购买'.$name."成功!\n金额:".$price."元\n时间:".date('Y-m-d H:i');
   sendwen($openid,$msg);
}
//自动生成二维码


function newqrcode($uid,$return_false = false){


	// 忽略用户取消，限制执行时间为90s

   
	ignore_user_abort();


	set_time_limit(90);

    $path = './Public/code/';
	$path_info =  array(


			'path'		=> $path,


			'new'		=> $path.$uid.'_dragondean.jpg',


			'head' 		=> $path.$uid.'_head.jpg',


			'qrcode'	=> $path.$uid.'.jpg',


			'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path,1)


		);

	


	// 已生成则直接返回

	unlink($path_info['qrcode']);
	if(is_file($path_info['new'])){


		return $path_info['new'];


	}


	


	// 目录不存在则创建
   if(!is_dir($path_info['path'])){


		mkdir($path_info['path'], 0777,1);


	}


	$dd = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);


	


	if(!is_file($path_info['qrcode'])){


		$accesstoken = $dd -> getaccesstoken();


		$path_info['qrcode'] = cancode($uid);


	}


	$format_arr = array(


		1 => 'gif',


		2 => 'jpeg',


		3 => 'png'


	);


	


	// 保存


	imagejpeg($im_dst, $path_info['new']);


	// 销毁


	imagedestroy($im_src);


	imagedestroy($im_dst);


	return $path_info['qrcode'];


}


function create_qrcode_url($user_info,$domain,$return_false = false){
    // 忽略用户取消，限制执行时间为90s
    if(is_array($user_info)){
        $uid = $user_info['id'];
    }else{
        $uid = $user_info;
    }
    ignore_user_abort();
    set_time_limit(90);   
    $path_info = get_qrcode_path($user_info);
    unlink($path_info['qrcode']);
    // 已生成则直接返回
    if (is_file($path_info['new'])) {
        return $path_info['new'];
    }

    // 目录不存在则创建

    if(!is_dir($path_info['path'])){
        mkdir($path_info['path'], 0777,1);

    }

    //$dd = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);


    if(!is_file($path_info['qrcode'])){

        include COMMON_PATH.'Util/phpqrcode/phpqrcode.php';
        $url = 'http://'.$domain['domain'].'/?uid='.$uid;

        \QRcode::png($url,$path_info['qrcode'],'M',14);
        //$accesstoken = $dd -> getaccesstoken();
//        $path_info['qrcode'] = cancode($uid);

        //$path_info['qrcode'] = $path_info['qrcode'];


    }


    // 合成


    $im_dst = imagecreatefromjpeg("./Public/images/bg.jpg");
//
//
//    $im_src = imagecreatefrompng($path_info['qrcode']);
//
//    // 合成二维码（二维码大小282*282)
//
//    imagecopyresized( $im_dst, $im_src,310, 350, 0, 0, 200, 200, 520, 520);

    $im_src = imagecreatefrompng($path_info['qrcode']);
    list($width, $height) = getimagesize($path_info['qrcode']);
    // 合成二维码（二维码大小282*282)
//    imagecopyresized( $im_dst, $im_src,265,710, 0, 0, 320, 320, $width, $height);
    imagecopyresized( $im_dst, $im_src,210,545, 0, 0, 320, 320, $width, $height);

    // 保存
    	// 合成昵称

   if($user_info['id']){
	$str = 'ID:'.$user_info['id'];


	$color = ImageColorAllocate($im_dst, 255,255,255);


	$font_info = imagettfbbox( 18 , 0 , './Public/font/simhei.ttf' , $str );


	$width = $font_info[2] -  $font_info[0];


	$left = 7;


	$rs = imagettftext($im_dst, '22', 0, $left,80, $color, './Public/font/simhei.ttf',  $str);

   }
    imagejpeg($im_dst, $path_info['new']);
   

    // 销毁


    imagedestroy($im_src);


    imagedestroy($im_dst);

    return $path_info['new'];


}
// 生成二维码图片,return_false默认直接提示错误，当为true的时候返回false


function create_qrcode($user_info,$return_false = false){


	// 忽略用户取消，限制执行时间为90s
    if(is_array($user_info)){
    	$uid = $user_info['id'];
    }else{
    	$uid = $user_info;
    }
   
	ignore_user_abort();


	set_time_limit(90);


	


	$path_info = get_qrcode_path($user_info);


	


	// 已生成则直接返回


	if(is_file($path_info['new'])){


		return $path_info['new'];


	}


	


	// 目录不存在则创建


	if(!is_dir($path_info['path'])){


		mkdir($path_info['path'], 0777,1);


	}


	


	$dd = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);


	


	if(!is_file($path_info['qrcode'])){


		$accesstoken = $dd -> getaccesstoken();


		$path_info['qrcode'] = cancode($uid);


		//$rs = $dd -> createqrcode('user_'.$user_info['id'],null,$accesstoken);


		


		// if(!$rs){


		// 	elog("[qrcode]".$dd -> errmsg);


		// 	return $rs;


		// }


		


		// $qrcode_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$rs['ticket'];


		// $qrcode_img = $dd -> exechttp($qrcode_url, 'get', null , true);


		// if(!$qrcode_img){


		// 	elog("[qrcode]\t下载二维码失败".var_export($qrcode_img,1));


		// 	return 3;


		// }


		


		// // 保存图片	


		// $save = file_put_contents($path_info['qrcode'],$qrcode_img);





		// if(!$save){


		// 	elog("[qrcode]二维码保存失败");


		// 	return 4;


		// }


	}


	// 合成


	$im_dst = imagecreatefromjpeg("./Public/images/newcode2.jpg");


	$im_src = imagecreatefromjpeg($path_info['qrcode']);


	// 合成二维码（二维码大小282*282)


	imagecopyresized( $im_dst, $im_src,240, 250, 0, 0, 160, 160, 430, 430);




	// 合成昵称

 //   if($user_info['nickname']){
	// $str = $user_info['nickname'];


	// $color = ImageColorAllocate($im_dst, 0,0,0);


	// $font_info = imagettfbbox( 20 , 0 , './Public/font/simhei.ttf' , $str );


	// $width = $font_info[2] -  $font_info[0];


	// $left = (640 - $width)/2;


	// $rs = imagettftext($im_dst, '20', 0, $left, 180, $color, './Public/font/simhei.ttf',  $str);

 //   }
	


	// 远程头像且本地没有保存,则获取远程头像到本地


	if(!(strpos($user_info['headimg'],'http://') === false) && !is_file($path_info['head'])){


		$head_img = $dd -> exechttp($user_info['headimg'], 'get', null , true);


		$head = file_put_contents($path_info['head'], $head_img);


	}


	else{


		$path_info['head'] = $user_info['headimg'];


	}


	


	$head_info = getimagesize($path_info['head']);


	$format_arr = array(


		1 => 'gif',


		2 => 'jpeg',


		3 => 'png'


	);


	// if(!empty($format_arr[$head_info[2]])){


	// 	$func = 'imagecreatefrom'.$format_arr[$head_info[2]];

	// 	// 合成头像

	// 	$im_src = $func($path_info['head']);

	// 	imagecopyresized ( $im_dst, $im_src, 276, 60, 0, 0, 80, 80, $head_info[0], $head_info[1]);


	// }



	// 保存


	imagejpeg($im_dst, $path_info['new']);


	// 销毁


	imagedestroy($im_src);


	imagedestroy($im_dst);


	return $path_info['new'];


}


// 


function is_gongpai($userid){


	$is_info = M('tree')->where(array('user_id'=>$userid))->select();


	return $is_info;


}


  


	


// 执行排位


function paiwei($user_info,$is_fanli=0){

   die;
	// 找系统上的挂靠点


	$parent_node = M('tree') -> where(array(


		'childs' => array('lt',2)


	)) -> order('id asc') -> find();


	//分配土地


	 $land = M('land')->where(array('user_id'=>$user_info['id']))->find();


	 if(empty($land)){


		for($i=0;$i<18;$i++){


			M('land')->add(array('user_id'=>$user_info['id'],'status'=>1,'create_time'=>time()));


		}


	}


	//返利  


	if($is_fanli!=0){


		$fanli = $GLOBALS['_CFG']['paiwei']['fanli'];


		$fanli = $fanli?$fanli:0;


		$daili = $user_info['fanli']+$fanli;


		M('user')->where(array('id'=>$user_info['id']))->save(array('fanli'=>$daili));


		$msg = "恭喜您成功购买公排,并返利金额到你的代理返利账户里!\n"."获得返利金额:".$fanli."元\n您目前的总代返利金额:".$daili."元\n购买时间:".date('Y-m-d H:i');


		sendwen($user_info['openid'],$msg);


	}


	// 数据库是空的，添加到最上的节点


	if(!$parent_node){


		M('tree') -> add(array(


			'user_id' => $user_info['id'],


			'pos' => 0,


			'x' => 0,


			'y' => 0,


			'childs' => 0,


			'parent' => 0,


			'create_time' => get_now(),


			'times' => 0,


			'team' => 0,


			'points' => $GLOBALS['_CFG']['paiwei']['points'],


		));


		return false;


	}


	


	$my_pos = find_child_pos($parent_node['x'],$parent_node['y']);


	$my_pos['y'] += $parent_node['childs'];


	


	// 查询排位次数


	$times = M('tree') -> where('user_id='.$user_info['id']) -> count();


	


	$tree_data = array(


		'user_id' => $user_info['id'],


		'pos' => get_index_by_pos($my_pos['x'],$my_pos['y']),


		'x' => $my_pos['x'],


		'y' => $my_pos['y'],


		'childs' => 0,


		'parent' => $parent_node['id'],


		'create_time' => get_now(),


		'times' => $times,


		'team' => 0


	);


	M('tree') -> add($tree_data);


	


	M('tree') -> where('id='.$parent_node['id']) -> setInc('childs');


	


	// 发放普通懒人奖


	reward_lazy($tree_data);


	


	if(!empty($user_info['openid'])){


		$msg = "恭喜您成功占点:\n".$my_pos['x'].'排'.$my_pos['y'].'列'."\n".date('Y-m-d H:i:s');


		sendwen($user_info['openid'],$msg);


	}


}





// 获得指定层数的上级pos集合


function get_parent_nodes($x,$y,$level){


	// 循环获得上级的pos


	$i = 0;


	$parent_node = get_parent_pos($x,$y);


	$parent_pos_arr = array(); // 上级位置数组


	while($i < $level && !$parent_node === false){


		$parent_pos_arr[] = get_index_by_pos($parent_node['x'],$parent_node['y']);


		$parent_node = get_parent_pos($parent_node['x'],$parent_node['y']);


		$i++;


	}


	return $parent_pos_arr;


}





// 发放直推荐奖


function reward_streight($tree_node,$level){


	if(!$tree_node){


		return false;


	}


	// 父节点


	if($GLOBALS['_CFG']['level'][$level]['streight_person'] == 1 && $tree_node['parent']){


		$parent_node = M('tree') -> where('id='.$tree_node['parent']) -> find();//getField('user_id');


		$parent_user_id = $parent_node['id'];


		$times = $parent_node['times'] +1;


		$remark = $GLOBALS['_CFG']['level'][$parent_node['level']]['name'].$times;


	}


	// 推进人


	elseif($GLOBALS['_CFG']['level'][$level]['streight_person'] == 0 && $tree_node['user_id']){


		$parent_user_id = M('user') -> where('id='.$tree_node['user_id']) -> getField('parent1');


		$remark = '直推';


	}


	// 直推奖金额


	$reward = $GLOBALS['_CFG']['level'][$level]['streight_money'];


	


	if($parent_user_id && $GLOBALS['_CFG']['level'][$level]['streight_money']){


		$tiliu = $reward * $GLOBALS['_CFG']['site']['points_tiliu']/100;


		$money = $reward - $tiliu;


		M('user') -> save(array(


			'id' => $parent_user_id,


			'money' => array('exp','money+'.$money),


			'points' => array('exp','points+'.$tiliu),


			'reward_streight' => array('exp','reward_streight+'.$reward),


			'zhitui' => array('exp','zhitui+1')


		));


		if($money)flog($parent_user_id,'money',$money,31,$remark);


		if($tiliu)flog($parent_user_id,'points',$tiliu,31,$remark);


	}


}





//发放普通懒人奖，level表示懒人奖的层数


function reward_lazy($node_info){


	// 懒人奖金额


	$reward = $GLOBALS['_CFG']['paiwei']['lazy_money'];


	// 奖励层数


	$levels = $GLOBALS['_CFG']['paiwei']['lazy_level'];


	


	// 获得上面levels父级的节点pos


	$parent_pos_arr = get_parent_nodes($node_info['x'],$node_info['y'],$levels);


	if(!empty($parent_pos_arr)){


		// 对level层以内的上级发放懒人奖


		$where = array(


			'pos' => array('in',$parent_pos_arr)


		);


		


		$parent_nodes = M('tree') -> where($where) -> field() -> select();


		


		$user_id_arr = array();


		foreach($parent_nodes as $parent_node){


			$user_id_arr[] = $parent_node['user_id'];


		}


		


		// 发放懒人奖到用户


		// M('user') -> where(array('id' => array('in',$user_id_arr))) -> save(array(


		// 	'money' => array('exp','money+'.$reward),


		// 	'reward_lazy' => array('exp','reward_lazy+'.$reward),


		// ));


		


		// 累加对应的奖励次数


		M('tree') -> where(array(


			'pos' => array('in',$parent_pos_arr)


		)) -> setInc('lazy_times');


		


		// 循环增加懒人奖财务记录


		//$i = 0;


		foreach($parent_nodes as $parent_node){


			$times = $parent_node['times'] +1;


			$remark = $GLOBALS['_CFG']['level'][$parent_node['level']]['name'].$times;


			if($reward)flog($parent_node['user_id'],'money',$reward,10,$remark);


			


			$parent_info = M('user') -> find($parent_node['user_id']);


			M('user') -> where(array('id' => $parent_node['user_id'])) -> save(array(


			'money' => array('exp','money+'.$reward),


			'reward_lazy' => array('exp','reward_lazy+'.$reward),


		    ));


			$msg = "恭喜您获得了一个懒人奖：\n金额：{$reward}\n时间：".date('Y-m-d H:i:s');


			sendwen($parent_info['openid'],$msg);


		}


	}


	return ;


}





// 根据我的位置找我的下级的位置


function find_child_pos($x,$y){


	$x ++;


	if($y==0){


		$y = 0;


	}


	else{


		$y = 2*$y;


	}


	return array('x' => $x,'y' => $y);


}





// 根据位置 （xy）获得序号 


function get_index_by_pos($x,$y){


	return pow(2,$x)-1+$y;


}





// 根据位置获得父节点的位置


function get_parent_pos($x,$y){


	if($x<=0){


		return false;


	}


	


	$x = $x-1;


	$y = floor(($y)/2);


	return array('x' => $x,'y' => $y);


}





// 获得当前时间戳


function get_now(){


	if(CLI === true)return time();


	else return NOW_TIME;


}





// 转为微信头像地址大小,默认原始图片(/0)，其他支持132*132(/132)、96*96(/96)、64*64(/64)、46*46(/46)


// 头像原图可能很大，下载和访问都很慢，根据需要制定大小可以加快访问速度


function headsize($url,$size = 0){


	$arr = explode('/',$url);


	if(!is_numeric($arr[count($arr)-1]))return $url;


	$arr[count($arr)-1] = $size;


	return implode('/',$arr);


}





// 取得支付方式


function get_payway($payway){


	$arr = array(


		'money' => '余额',


		'points' => '积分',


		'wxpay' => '微信',


		'alipay' => '支付宝'


	);


	return $arr[$payway];


}
function isMobile(){
    $user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
    //echo $user_agent;
    $mobile_agents = Array("ipad","wap","android","iphone","sec","sam","ericsson","240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte","ben","hai","phili");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            if( 'ipad' == $device )
            {
                return $is_mobile;
            }
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}
function http_curl_get($url, $type = 1)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_TIMEOUT, 5000);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($type == 1) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    }
    $res = curl_exec($curl);
    if ($res) {
        curl_close($curl);
        return $res;
    } else {
        $error = curl_errno($curl);
        curl_close($curl);
        return $error;
    }
}

/* 
 * 检测域名是否正常
 */ 
function http_code($url) 
{  
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_exec($ch);  
	$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($curl_code == 200 || $curl_code == 302) {
	    return true;
	} else {
	    return false;
	}

}

function getonlineip(){//获取用户ip

  if($_SERVER['HTTP_CLIENT_IP'])

  {

   $onlineip=$_SERVER['HTTP_CLIENT_IP']; //用户IP

  }

  else if($_SERVER['HTTP_X_FORWARDED_FOR'])

  {

   $onlineip=$_SERVER['HTTP_X_FORWARDED_FOR']; //代理IP

  }

  else

  {

   $onlineip=$_SERVER['REMOTE_ADDR']; //服务器IP

  }

  return $onlineip;

}

  function getPostData() {
        $postdata = file_get_contents("php://input");
        $data = urldecode($postdata);
         $data = substr_replace($data, '', 0, 5);
        $data = str_replace(PHP_EOL, '', $data);
        //var_dump($data);
         //$data = json_decode($data, true);
        //var_dump($data);
         return $data;
  }

     /**二分查找*/
function search($score, $filter)
{   

    $half = floor(count($filter) / 2); // 取出中間数

    // 判断积分在哪个区间

    if ($score <= $filter[$half-1]['max']) {

        $filter = array_slice($filter, 0 , $half);

    } else {

        $filter = array_slice($filter, $half , count($filter));

    }
    // 继续递归直到只剩一个元素

    if (count($filter) != 1) {

        $filter = search($score, $filter);

    }
    return $filter;

}
