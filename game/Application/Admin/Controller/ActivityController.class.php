<?php

namespace Admin\Controller;

use Think\Controller;

class ActivityController extends AdminController {

    // 游戏域名列表
	public function index() 
    {
        
         $list = M('activity_info')->select();

        $this -> assign('list', $list);
        $this -> display();
	}

    // 推广域名列表
    public function add() 
    {
        // 公共列表
       
        // $this -> assign('page', $this -> data['page']);
        $this -> display();
    }

	// 编辑、添加
	public function edit()
    {
		$id = intval($_GET['id']);
        if ($id > 0) {
            $info = M('activity_info')->find($id);
            if (!$info) {
                $this->error('操作错误');
            }
        }

        if (IS_POST) {
            if ($_POST['id'] > 0) {
                $temp_agent = M('activity_info')->where(array('id' => $_POST['id']))->find();
                if (!is_array($temp_agent)) {
                    $this->error('信息不存在');
                }
            } else {
                if (empty($_POST['title'])) {
                    $this->error('活动标题不能为空');
                }
                $temp_agent = D('activity_info')->where(array('title' => $_POST['title']))->find();
                if (is_array($temp_agent)) {
                    $this->error('活动已存在');
                }
            }


        }
        if (!isset($_GET['type'])) {
            $this -> _edit('activity_info');
        } else {
            $this -> _edit('activity_info', U('index'));
        }
	}


	// 删除商品
	public function del()
    {
		$this -> _del('activity_info', $_GET['id']);
		$this -> success('删除成功！');
	}


 


}