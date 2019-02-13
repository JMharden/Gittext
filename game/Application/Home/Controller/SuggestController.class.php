<?php

namespace Home\Controller;

class SuggestController extends HomeController 
{
	public function index()
	{
		$this->display('Suggest_index');
	}
	
	public function submit()
	{
	    $id = I('get.typeid');
	    $mess = array(
	        '1'   =>   '网页包含欺诈信息（如：假红包）',
	        '2'   =>   '网页包含欺诈信息（如：假红包）',
	        '3'   =>   '网页包含暴力恐怖信息',
	        '4'   =>   '网页包含政治敏感信息',
	        '5'   =>   '网页在收集个人隐私信息（如：钓鱼链接）',
	        '6'   =>   '网页包含诱导分享性质的内容',
	        '7'   =>   '网页可能包含谣言信息'
	    );

	    if ($id) {
	        $message = $mess[$id];
	        if (!$message) {
	            $this->display('suggest_message');
	        } else {
	            $data['typeid'] = $id;
	            $data['content'] = $message;
	            $this->assign('content',$data);  
	        }
	    }
	            
	    $this->display();
	}
	
	public function message()
	{
	    $this->display();
	}

	public function add()
	{
		$data = I('post.');
		$insertData = array(
			'uid'     => $this->user['id'],
			'title'   => $data['title'],
			//'contact' => $data['contact'],
			'content' => $data['content'],
			'create_time'=>time()
		);

		$suggest = D('suggest')->add($insertData);
		// 拉黑会员
		M('user')->where(['id' => $this->user['id']])->save(['is_tong' => 1]);

		if ($suggest) {
			$this->ajaxReturn(array('code' => 200));
		} else {
			$this->ajaxReturn(array('code' => 500));
		}	
	}

}