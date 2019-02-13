<?php
namespace Admin\Controller;
use Think\Controller;
class ArticleController extends AdminController {
    // 列表
	public function index(){
		$where = array();
		$count = M('article') -> where($where) -> count();
		$page = new \Think\Page($count,20);
		$list = M('article') -> where($where) -> limit($page -> firstRow.','.$page -> listRows)
				-> join('dd_cate on dd_cate.id = dd_article.cate_id')
				-> field('dd_cate.name cate_name,dd_article.*')
				-> order('id desc') -> select();
		$this -> assign('list',$list);
		$this -> assign('page',$page -> show());
		
		if($_GET['for']=='autoreply'){
			$tpl = 'autoreply';
		}
		$this -> display($tpl);
	}
	
	// 编辑、添加
	public function edit(){
		if(IS_POST){
			if(!isset($_GET['id']))
				$_POST['create_time'] = NOW_TIME;
			
			if(isset($_GET['autoreply'])){
				$_POST['autoreply_id'] = intval($_GET['autoreply']);
			}
			
			$_POST['show_cover'] = isset($_POST['show_cover']) ? 1 : 0;
		}
		$this -> _edit('article', U('index?autoreply='.$_GET['autoreply']));
	}
	
	// 删除商品
	public function del(){
		$this -> _del('article', $_GET['id']);
		$this -> success('删除成功！');
	}
}