<?php
namespace Admin\Controller;
use Think\Controller;
class SmsController extends AdminController {
    // 发送记录
	public function log(){
		$this -> _list('sms_log',$where,'id desc');
	}
	
	// 发送短信
	public function send(){
		if(IS_POST){
			if(!empty($_POST['mobile']) && !empty($_POST['msg'])){
				$rt = send_sms($_POST['mobile'],$_POST['msg'],$_POST['sign'],true);
				if($rt == 0){
					$msg = '短信发送成功！';
					$this -> success($msg,U('log'));
				}
				else{
					$msg = '短信发送失败：'.get_sms_result($rt);
					$this -> error($msg,U('log'));
				}
				
				exit;
			}
		}
		$this -> display();
	}
	
	// 短信概况
	public function dashbord(){
		// 查询短信余额
		$url = "http://www.smsbao.com/query?u=".C('SMS_USER')."&p=".md5(C('SMS_PASS'));
		$rt = file_get_contents($url);
		if(substr($rt,0,1) != 0){
			$left = "余额查询错误";
		}
		$rt = explode(',',$rt);
		$left = $rt[1];
		$this -> assign('left',$left);
		
		// 查询已发送条数
		$send = M('sms_log') -> count();
		$this -> assign('send',$send);
		
		// 查询成功条数
		$success = M('sms_log') -> where('result=0') -> count();
		$this -> assign('success',$success);
		
		$this ->display();
	}
}