<?php
namespace Common\Util;
/**
*	微信类，集成微信常用功能,目前只支持明文模式
*	@author DragonDean
*	@url	http://www.dragondean.cn
*/
class tplmsg{

	private $access_token;
	private $ddwechat;
	public $errmsg;
	
	/**
	*	构造函数，初始化参数
	*	@param array $param 数组格式的参数
	*/
	public function __construct(){
		
		$this -> ddwechat = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);
		//$this -> ddwechat -> setParam($GLOBALS['_CFG']['mp']);
		$this -> access_token = $this -> ddwechat -> getaccesstoken();
	}
	
	// 初始化，从官方获得模板id。执行之前
	public function init(){
		$tpls = array(
			'TM00303' => '发货通知',
			'OPENTM205297594' => '订单支付提醒',
			'OPENTM201139572' => '邀请关注通知'
		);
		
		$config = array();
		foreach($tpls as $key => $val){
			$id = $this -> ddwechat -> gettplid($key,$this -> access_token);
			if($id){
				$config[$key] = array(
					'id' => $id,
					'status' => 1
				);
			}
			else{
				$this -> errmsg[] = $this -> ddwechat -> errmsg;
			}
		}
		return $config;
	}
	
	
	// 发送订单支付通知
	public function pay_notice($openid,$order_info){
		if(empty($GLOBALS['_CFG']['tplmsg']['OPENTM205297594']['id']) || $GLOBALS['_CFG']['tplmsg']['OPENTM205297594']['status'] != 1){
			return false;
		}
		
		$data  = array(
			'touser' => $openid,
			'template_id' => $GLOBALS['_CFG']['tplmsg']['OPENTM205297594']['id'],
			'url' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/?a=order_detail&id='.$order_info['id'],
			'data' => array(
				'first' => array(
					'value' => '恭喜您，订单已支付成功！',
					'color' => '#01033C',
				),
				// 订单编号
				'keyword1' => array(
					'value' => $order_info['sn'],
					'color' => '#C50000',
				),
				//订单金额				
				'keyword2' => array(
					'value' => $order_info['total'],
					'color' => '#666666',
				),
				'remark' => array(
					'value' => '我们将尽快安排发货，请耐心等待！',
					'color' => '#9E9B9B',
				),
			)
		);
		$rt = $this -> ddwechat -> tplmsg($data, $this -> access_token);
		if(APP_DEBUG && !$rt){
			var_dump($this -> ddwechat -> errmsg);
		}
	}
	
	// 发货提醒
	public function express_notice($openid, $order_info){
		if(empty($GLOBALS['_CFG']['tplmsg']['TM00303']['id']) || $GLOBALS['_CFG']['tplmsg']['TM00303']['status'] != 1){
			return false;
		}
		
		$data  = array(
			'touser' => $openid,
			'template_id' => $GLOBALS['_CFG']['tplmsg']['TM00303']['id'],
			'url' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/?a=order_detail&id='.$order_info['id'],
			'data' => array(
				'first' => array(
					'value' => '您的订单'.$order_info['sn'].'已发货',
					'color' => '#01033C',
				),
				// 快递公司
				'delivername' => array(
					'value' => $order_info['express'],
					'color' => '#C50000',
				),
				// 快递单号				
				'ordername' => array(
					'value' => $order_info['express_no'],
					'color' => '#666666',
				),
				'remark' => array(
					'value' => '点击查看订单详情和物流信息！',
					'color' => '#9E9B9B',
				),
			)
		);
		$rt = $this -> ddwechat -> tplmsg($data, $this -> access_token);
		if(APP_DEBUG && !$rt){
			var_dump($this -> ddwechat -> errmsg);
		}
	}
	
	// 邀请关注提醒
	public function invite_notice($parent_info, $user_info){
		if(empty($GLOBALS['_CFG']['tplmsg']['OPENTM201139572']['id']) || $GLOBALS['_CFG']['tplmsg']['OPENTM201139572']['status'] != 1){
			return false;
		}
		
		$data  = array(
			'touser' => $parent_info['openid'],
			'template_id' => $GLOBALS['_CFG']['tplmsg']['OPENTM201139572']['id'],
			'url' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/?a=ucenter',
			'data' => array(
				'first' => array(
					'value' => $user_info['nickname'].'已通过您的二维码关注成功!',
					'color' => '#01033C',
				),
				// 微信昵称/姓名
				'keyword1' => array(
					'value' => $user_info['nickname'],
					'color' => '#C50000',
				),
				// 扫码时间				
				'keyword2' => array(
					'value' => date('Y-m-d H:i:s'),
					'color' => '#666666',
				),
				'remark' => array(
					'value' => '您离成功又近来一步，继续努力！',
					'color' => '#9E9B9B',
				),
			)
		);
		$rt = $this -> ddwechat -> tplmsg($data, $this -> access_token);
		if(APP_DEBUG && !$rt){
			var_dump($this -> ddwechat -> errmsg); // 可以通过开发者中心的运维中心查看到返回的内容
		}
	}
	
	
	
}
?>