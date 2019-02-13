<?php
namespace Admin\Controller;
use Think\Controller;
class ChouController extends AdminController {
    // 通知列表
	public function index(){
		$_GET = array_merge($_GET,$_POST);
		
		if($_GET['user_id']){
			$where['user_id'] = intval($_GET['user_id']);
		}
		if(isset($_GET['status']) && $_GET['status'] != ''){
			$where['status'] = intval($_GET['status']);
		}
		// 默认之显示已支付的
		elseif(!$_GET['all'] == 1){
			$where['status'] = array('gt',0);
		}
		
		if($_GET['date']){
			$where['create_time'] = array(
				array('egt',strtotime($_GET['date'])),
				array('lt',strtotime($_GET['date'])+86400)
			);
		}
		$this -> _list('chou',$where);
	}
	
	// 设置为中将
	public function reward(){
		$id = intval($_GET['id']);
		$info = M('chou') -> find($id);
		if($info['status'] != 1){
			$this -> error('操作错误');
		}
		
		$where = array('status' => 1);
		$total = M('chou') -> where($where) -> sum('money');
		
		if(!$total){
			$this -> error('无可数据');
		}
		$reward = $total * $this -> _site['chou_reward']/100;
		
		M('chou') -> where($where) -> save(array(
			'status' => 2
		));
		
		// 推荐奖
		$expense = $total * $this -> _site['chou_expense'] / 100;
		
		M('chou') -> save(array(
			'id' => $info['id'],
			'status' => 3,
			'reward' => $reward,
			'reward_time' => NOW_TIME,
			'expense' => $expense
		));
		M('user') -> save(array(
			'id' => $info['user_id'],
			'money' => array('exp','money+'.$reward),
			'reward' => array('exp','reward+'.$reward)
		));
		flog($info['user_id'],'money',$reward,7);
		
		// 有推荐人则发推荐奖
		$user_info = M('user') -> find($info['user_id']);
		if($user_info['parent1']){
			M('user') -> save(array(
				'id' => $user_info['parent1'],
				'money' => array('exp','money+'.$expense),
			));
			flog($user_info['parent1'],'money',$expense,8);
		}
		
		$this -> success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	
}?>