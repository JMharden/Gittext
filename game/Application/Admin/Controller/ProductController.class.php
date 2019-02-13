<?php


namespace Admin\Controller;


use Think\Controller;


class ProductController extends AdminController 
{

    // 植物列表
	public function plant()
	{
		$this -> _list('plant',$where,'id desc');
	}


	// 编辑、添加
	public function plant_edit()
	{
		$this -> _edit('plant', U('plant'));
	}


	// 删除通知
	public function plant_del()
	{
		$this -> _del('plant', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}


	// 肥料列表
	public function fertilizer()
	{
		// 查询所有的植物
		$plants_rs = M('plant') -> select();
		$plants = array();

		foreach ($plants_rs as $plant) {
			$plants[$plant['id']] = $plant;
		}

		$this -> assign('plants',$plants);
		$this -> _list('fertilizer',$where,'id desc');
	}


	function kai()
	{
        $_GET = array_merge($_GET,$_POST);
		$where  = array();
		$allnum = I('get.allnum');
        if (!empty($allnum)) {
            $where['allnum'] = array('egt',intval($allnum));
        }

        $this->assign('allnum',$allnum);
		$this->_list('kailog',$where,'id desc');
	}


	function buy()
	{
		$_GET 	= array_merge($_GET,$_POST);
		$where  = array();

		if ($_GET['uid']) {
			$where['uid'] = (int)$_GET['uid'];
		}

		$this->_list('zhuan',$where,'id desc');
	}


	function zhong()
	{
		$sum = M('zhuan')->where(array('money'=>$_GET['money']))->count();
		$this->assign('sum',$sum);
		$zhong = M('zhuan')->where(array('_string'=>'ying>money','money'=>$_GET['money']))->count();
		$this->assign('zhong',$zhong);
		$where  = array('_string'=>'ying>money','money'=>$_GET['money']);
		$this->_list('zhuan',$where,'id desc');
	}


	function bizhong()
	{
		$where  = array();
		$this->_list('wuguili',$where,'id desc');
	}


    function kong()
    {
    	$kid = $_POST['kid'];
    	$id = $_POST['id'];
    	$info = M('kailog')->where(array('id'=>$kid))->find();
    	if($info){
            if($info['status']==2){
    		 $data['info'] = '这期已结束';            
            }else{
            	M('kailog')->where(array('id'=>$kid))->save(array('kongid'=>$id));
            	$data['status'] = '1';
            	if($id==1){
                  $data['name'] = '香蕉';
            	}
            	if($id==2){
            	  $data['name'] = '西瓜';
            	}
            	if($id==3){
            	  $data['name'] = '苹果';
            	}
            	
            }
    	}else{
    		$data['info'] = '没有这期';
    	}
      echo json_encode($data);
    }


	// 编辑、添加通知
	public function fertilizer_edit()
	{
		// 不是提交表单则查询所有植物
		if (!IS_POST) {
			// 查询所有的植物
			$plants_rs = M('plant') -> select();
			$plants = array();
			foreach($plants_rs as $plant){
				$plants[$plant['id']] = $plant;
			}

			$this -> assign('plants',$plants);
		}

		$this -> _edit('fertilizer', U('fertilizer'));
	}


	// 删除通知
	public function fertilizer_del()
	{
		$this -> _del('fertilizer', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}


	


}