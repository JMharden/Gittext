<?php


namespace Admin\Controller;


use Think\Controller;


class ProductController extends AdminController 
{

    // 植物列表
	public function index()
	{	
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['number'])) {
			$where['number'] = array('like','%'.$_GET['number'].'%');
			
		}
		$this -> _list('product',$where,'id desc');
	}


	// 编辑、添加
	public function edit()
    {
		$id = intval($_GET['id']);
        if ($id > 0) {
            $info = M('product')->find($id);
            if (!$info) {
                $this->error('操作错误');
            }
        }

        if (IS_POST) {

            if ($id > 0) {
                $temp_agent = M('product')->where(array('id' => $id))->find();
                if (!is_array($temp_agent)) {
                    $this->error('信息不存在');
                }
            } else {
                if (empty($_POST['number'])) {
                    $this->error('商品编号不能为空');
                }
                $temp_agent = D('product')->where(array('number' => $_POST['number']))->find();
                if (is_array($temp_agent)) {
                    $this->error('商品已存在');
                }
            }

           
        }
        if (!isset($_GET['type'])) {
            $this -> _edit('product');
        } else {
            $this -> _edit('product', U('index'));
        }
	}


	// 删除商品
	public function del()
    {
		$this -> _del('product', $_GET['id']);
		$this -> success('删除成功！');
	}
  

 

	


}