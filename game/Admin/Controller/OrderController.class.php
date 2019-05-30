<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends AdminController {
    // 列表
	public function index(){
		$_GET = array_merge($_GET,$_POST);
		$where = array();
		if(isset($_GET['status']) && $_GET['status'] != ''){
			$where['status'] = intval($_GET['status']);
		}
		if(!empty($_GET['name'])){
			$where['name'] = I('get.name');
		}
		if(!empty($_GET['mobile'])){
			$where['mobile'] = I('get.mobile');
		}
		if(!empty($_GET['user_id'])){
			$where['user_id'] = I('get.user_id');
		}
		$count = M('order') -> where($where) -> count();
		$page = new \Think\Page($count,20);
		$list = M('order') -> where($where) 
				-> join('dd_user on dd_user.id = dd_order.user_id') 
				-> field('dd_user.name user_name,dd_user.mobile,dd_order.*')
				-> order('id desc')
				-> limit($page -> limit()) -> select();
		
		$this -> assign('list',$list);
		$this -> assign('page',$page);
		$this -> display();
	}
	
	// 编辑、添加
	public function edit(){
		$id = intval($_GET['id']);
		$info = M('order') -> find($id);
		if(!$info){
			$this -> error('访问错误!');
		}
		$this -> assign('info',$info);
		
		// 查询会员信息
		$user_info = M('user') -> find($info['user_id']);
		$this -> assign('user_info',$user_info);
		
		$this -> display();
	}
	
	// 删除商品
	public function del(){
		$id = intval($_GET['id']);
		$info = M('order') -> find($id);
		if($info['status'] == 1){
			$this -> error('已支付的订单不能删除');
		}
		
		$this -> _del('order', $_GET['id']);
		$this -> success('删除成功！');
	}
}