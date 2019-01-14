<?php
namespace Wap\Controller;

class WxLoginController extends \Think\Controller
{
	public function index()
	{
		$urls = I('urls');
		$sysconfig = M('sys_config')->find();
		$ubeiopenid = $this->getUrl($urls, 3);
		if ($sysconfig['cbeicode'] == 1 && $ubeiopenid == '') {
			if(!check_is_weixin()){
				$url = 'http://' . $_SERVER['HTTP_HOST'].'/Wxlogin_img.html';
				$this->redirect('Wxlogin/img');
				//header('Location:'.$url);
				die;
			}
			$url = 'http://' . $_SERVER['HTTP_HOST'] . U('Wap/Wxlogin/getBackupOpenId?urls=' . $urls);
			$url = urlencode($url);
			$wxurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $sysconfig['cbeiappid'] . '&redirect_uri=' . $url . '&response_type=code&scope=snsapi_base#wechat_redirect';
			header('Location:' . $wxurl);
		} else {
			if ($sysconfig['cdenglucode'] == 1) {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . U('Wap/Wxlogin/getOpenId?urls=' . $urls);				
				$url = urlencode($url);
				$wxurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $sysconfig['cwxappid'] . '&redirect_uri=' . $url . '&response_type=code&scope=snsapi_base#wechat_redirect';
			} else {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . U('Wap/Wxlogin/getUserInfo?urls=' . $urls);						
				$url = urlencode($url);
				$wxurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $sysconfig['cwxappid'] . '&redirect_uri=' . $url . '&response_type=code&scope=snsapi_userinfo#wechat_redirect';
			}
			
			header('Location:' . $wxurl);
		}
	}
	public function img(){
		$this->assign('server_url',$_SERVER['HTTP_HOST']);
		$this->display();
	}
	public function getOpenId()
	{
		$wxcode = I('code');
		$urls = I('urls');
		$config = M('sys_config')->find();
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $config['cwxappid'] . '&secret=' . $config['cwxappsecret'] . '&code=' . $wxcode . '&grant_type=authorization_code';
		$info = json_decode(http_curl_get($url));
		$uopenid = $info->openid;
		if ($uopenid == '') {
			echo '公众号授权登录失败->错误码->' . $info->errcode . '，解决方法：重置Appsecret';
			die;
		}
		$ubeiopenid = $this->getUrl($urls, 3);
		$utid = $this->getUrl($urls, 2);
		$this->checkuser($uopenid, $ubeiopenid, $utid);
	}
	public function getUserInfo()
	{
		$wxcode = I('code');
		$urls = I('urls');
		$config = M('sys_config')->find();
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $config['cwxappid'] . '&secret=' . $config['cwxappsecret'] . '&code=' . $wxcode . '&grant_type=authorization_code';
		$info = json_decode(http_curl_get($url));
		$uopenid = $info->openid;
		$wxtoken = $info->access_token;
		if ($uopenid == '') {
			echo '公众号授权登录失败->错误码->' . $info->errcode . '，解决方法：重置Appsecret';
			die;
		}
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $wxtoken . '&openid=' . $uopenid . '&lang=zh_CN';
		$userinfo = json_decode(http_curl_get($url));
		$ubeiopenid = $this->getUrl($urls, 3);
		$utid = $this->getUrl($urls, 2);
		$uickname = filter_Emoji($userinfo->nickname);
		$usex = intval($userinfo->sex);
		$uheadimgurl = $userinfo->headimgurl;
		$udizhi = $userinfo->province . $userinfo->city;
		$this->checkuser($uopenid, $ubeiopenid, $utid, $uickname, $usex, $uheadimgurl, $udizhi);
	}
	private function checkuser($uopenid = '', $ubeiopenid = '', $utid = 0, $uickname = '', $usex = 0, $uheadimgurl = '', $udizhi = '')
	{			
		$user = M('user_list')->where("uopenid='%s'",$uopenid)->find();
		$ubeiser = M('user_list')->where("ubeiopenid='%s' and ubeiopenid<>''",$ubeiopenid)->find();
		$config = M('sys_config')->find();
		
		if (!$user) {
			//如果备份的存在
			if ($ubeiser) {	
				M('user_list')->save(array('id' => $ubeiser['id'], 'uopenid' => $uopenid));
				$userid = $ubeiser['id'];
			}
			else {	
				$parentuser = M('user_list')->where('id=%d',$utid)->find();
				if (0 < $parentuser['uvip']) {
					$data['utid'] = $utid;
				}
				if ($config['cdailicode'] == 2) {
					$yongjinset = M('yongjin_set')->order('ydengji asc')->find();
					$data['uvip'] = intval($yongjinset['ydengji']) == 0 ? 1 : intval($yongjinset['ydengji']);
				}
				$data['uopenid'] = $uopenid;
				$data['ubeiopenid'] = $ubeiopenid;
				$data['uickname'] = $uickname;
				$data['usex'] = $usex;
				$data['uheadimgurl'] = $uheadimgurl;
				$data['udizhi'] = $udizhi;
				$data['uregtime'] = time();
				$data['loginip'] = get_ip();
				$data['ulogintime'] = time();
				
				//如果首次注册送钱
				if(intval($config["firstregmoney"])>0){
					M('user_zhanghu')->add(array('userid' => $userid,'remainmoney'=>intval($config["firstregmoney"])));
				}
				
				if ($utid != 0)
                {
                    $parent_user = M('user_list')->where("id=%d",$utid)->find();
                    if ($parent_user)
                    {
                        $data['userid1'] = $parent_user['id'];
                        $data['userid2'] = $parent_user['userid1'];
                        $data['userid3'] = $parent_user['userid2'];                      
                    }
                }
				
				
				$userid = M('user_list')->add($data);
				unset($data);
				M('user_zhanghu')->add(array('userid' => $userid));
			}
			
		} else {
			if ($ubeiopenid != '' && $user['ubeiopenid'] != $ubeiopenid) {
				$data['ubeiopenid'] = $ubeiopenid;
			}
			if ($uickname != '') {
				$data['uickname'] = $uickname;
				$data['usex'] = $usex;
				$data['uheadimgurl'] = $uheadimgurl;
				$data['udizhi'] = $udizhi;
			}
			
			if($user['userid1']==0 && $user['userid2']==0 && $user['userid3']==0){
				if ($utid != 0)
                {
                    $parent_user = M('user_list')->where("id=%d",$utid)->find();
                    if ($parent_user)
                    {
                        $data['userid1'] = $parent_user['id'];
                        $data['userid2'] = $parent_user['userid1'];
                        $data['userid3'] = $parent_user['userid2'];                      
                    }
                }
			}
			
			$data['loginip'] = get_ip();
			$data['ulogintime'] = time();
			$data['id'] = $userid = $user['id'];
			M('user_list')->save($data);
			unset($data);
		}
		session('userid', $userid);
		session('uopenid',$uopenid);
		$this->redirect('Index/index');
		//$this->jumpurl($userid,$uopenid);
	}
	public function getBackupOpenId()
	{
		$wxcode = I('code');
		$urls = I('urls');
		$sysconfig = M('sys_config')->find();
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $sysconfig['cbeiappid'] . '&secret=' . $sysconfig['cbeiappsecret'] . '&code=' . $wxcode . '&grant_type=authorization_code';
		$info = json_decode(http_curl_get($url));
		$ubeiopenid = $info->openid;
		if ($ubeiopenid == '') {
			echo '备份公众号授权登录失败->错误码->' . $info->errcode . '，解决方法：重置Appsecret';
			die;
		}
		
		$jumpurlObj = M('url')->field('url')->where('status=2')->limit(0, 1)->select();  //1:停用，2：启用
		if(!empty($jumpurlObj) ) {
			$headurl = 'http://'.$jumpurlObj[0]['url'] . U('Wap/Index/index?utid=' . $this->getUrl($urls, 2) . '&ubeiopenid=' . $ubeiopenid);
			header('Location:' . $headurl);
		}else{
			echo '至少要有一个启用的微信域名';
			die;
		}
	}
	public function getUrl($urlsquery = '', $val = 1)
	{
		$urlarr = explode('|', $urlsquery);
		foreach ($urlarr as $v) {
			if (stripos($v, 'm-') !== false) {
				$m = substr($v, 2);
				continue;
			}
			if (stripos($v, 'c-') !== false) {
				$c = substr($v, 2);
				continue;
			}
			if (stripos($v, 'a-') !== false) {
				$a = substr($v, 2);
				continue;
			}
			if (stripos($v, 'utid-') !== false) {
				$utid = substr($v, 5);
				continue;
			}
			if (stripos($v, 'ubeiopenid-') !== false) {
				$ubeiopenid = substr($v, 11);
				continue;
			}
		}
		$urls = 'index.php?m=' . $m . '&c=' . $c . '&a=' . $a . '&utid=' . $utid;
		if ($val == 1) {
			return $urls;
		} else {
			if ($val == 2) {
				return intval($utid);
			} else {
				if ($val == 3) {
					return $ubeiopenid;
				}
			}
		}
	}
	
	//域名跳转
	public function jumpurl($userid = '', $uopenid = ''){
		//查询数据库可用域名
		$jumpurlObj = M('url')->field('url')->where('status=2')->limit(0, 1)->select();  //1:停用，2：启用
		if(empty($jumpurlObj) ) {
			die("没有添加域名!");
			$jumpurl = 'www.qq.com';
		} else {
			$jumpurl = $jumpurlObj[0]['url'];
		}		
		//跳转到可用链接
		header('Location:http://mp.wapwei.com/api.php?id=15');
	}
	
	public function mainfo()
	{
		$sysconfObj = M('sys_config')->field('wangzhankaiguan,weihutixing')->where('id=1')->find();
		$on_off = $sysconfObj['wangzhankaiguan'];
		if($on_off == 1){				
			$jumpurlObj = M('url')->field('url')->where('status=2')->limit(0, 1)->select();  //1:停用，2：启用
			if(!empty($jumpurlObj) ) {					
				$utid = I('utid', 0, 'intval');
				if ($utid == 0) {
					$utid = I('path.3', 0, 'intval');
				}
				$config = M('sys_config')->find();
				if ($config['cbeicode'] == 1) {
					$url = $config['cbeiurl'] . U('Wap/Index/index?utid=' . $utid);
				} else {
					$url = 'http://'.$jumpurlObj[0]['url'] .'/' . U('Wap/Index/index?utid=' . $utid);					
				}				
			}
			if(check_is_weixin()){

                header('Location:' . $url);
            }else{
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
			echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">';
			echo "<h3>请用微信扫描或保存二维码到手机在微信中识别</h3>";
			echo "<div style='padding:30px;text-align:center;'>";
            echo '<img src="'.U("Wxlogin/create_qrcode").'&url='.urlencode($url).'" class="img"/>';
                echo "</div>";
            }

		}else{
			$notice = $sysconfObj['weihutixing'];
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
			echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">';
			echo "<div style='padding:30px;text-align:center;'>".$notice."</div>";
			exit;
		}
		
	}
	public function create_qrcode(){
	        $url=urldecode(I('url'));
			vendor('phpqrcode.qrlib');
			\QRcode::png($url);

    }
}