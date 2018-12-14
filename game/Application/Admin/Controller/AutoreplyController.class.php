<?php
namespace Admin\Controller;
use Think\Controller;
class AutoreplyController extends AdminController {
    // 通知列表
	public function index(){
		$this -> _list('autoreply');
	}
	
	// 编辑、添加通知
	public function edit(){
		
		$model = M('autoreply');
		
		$id = intval($_GET['id']);
		if($id>0){
			$info = $model -> find($id);
			if(!$info){
				die('信息不存在');
			}
			
			if($info['type'] == 2){
				$article_ids = explode(',',$info['content']);
				$articles  = M('article') -> where(array('id' => array('in',$article_ids))) -> select();
				$this -> assign('articles',$articles);
			}
			
			$this -> assign('info', $info);
		}
		
		if(IS_POST){
			if(empty($_POST['keyword'])){
				$this -> error('关键词不能为空');
			}
			
			if($_POST['type'] == 2){
				$_POST['content'] = implode(',',$_POST['article']);
			}
			
			if(!$url)
				$url = U('index');
			if($id>0){
				$_POST['id'] = $id;
				$model -> save($_POST);
				$this -> success('操作成功！', $url);
				exit;
			}else{
				$model -> add($_POST);
				$this -> success('添加成功！', $url);
				exit;
			}
		}
		
		$this -> display();
	}
	
	
	// 改变状态
	public function set_status(){
		$id = intval($_GET['id']);
		$status = intval($_GET['status']) > 0 ? 1 : 0;
		M('autoreply') -> where("id={$id}") -> setField('status', $status);
		$this -> success('操作成功！');
	}
	
	// 删除通知
	public function del(){
		$this -> _del('autoreply', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
}