<?php
namespace Home\Controller;
use Think\Controller;
class CliController extends HomeController {
	
	public function _initialize(){
		if(CLI != 'true'){
			echo "fail";
		}
		
		// 加载配置
		$config = M('config') -> select();
		if(!is_array($config)){
			die('请先在后台设置好各参数');
		}
		foreach($config as $v){
			$key = '_'.$v['name'];
			$this -> $key = unserialize($v['value']);
			$_CFG[$v['name']] = $this -> $key;
		}
		$this -> assign('_CFG', $_CFG);
		$GLOBALS['_CFG'] = $_CFG;
	}
	
	public function index(){
		$paiwei = $this -> _paiwei['points'];
		while(1){
			$users = M('user') -> where(array(
				'points' => array('egt',$paiwei)
			)) -> select();
			
			if(empty($users)){
				sleep(1);
			}
			
			foreach($users as $user){
				$points = $user['points'];
				while($user['points'] >= $paiwei){
					// 排位
					//paiwei($user);
					$user['points'] -= $paiwei;
				}
				
				// 消耗积分　＝　用户原有积分 － 现在的积分
				$points_consume = $points - $user['points'];
				M('user') -> save(array(
					'id' => $user['id'],
					'points' => array('exp','points-'.$points_consume)
				));
				flog($user['id'],'points',$points_consume,10);
			}
			
		}
	}
	
}?>