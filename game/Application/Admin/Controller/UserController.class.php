<?php

namespace Admin\Controller;
use Think\Controller;

class UserController extends AdminController 
{

	public function index(){
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['nickname'])) {
			$where['nickname'] = array('like','%'.$_GET['nickname'].'%');
		}
		 if(!empty($_GET['money'])){
            $order = $order.' money '.$_GET['money'].', ';
        }

        if(!empty($_GET['active_point'])){
            $order = $order.' active_point '.$_GET['active_point'].', ';
        }

        if(!empty($_GET['rank'])){
            $order = $order.' rank '.$_GET['rank'].', ';
        }
		$order = $order.' id desc';
		$model = M('user_base');
		$count = $model -> where($where) -> count();
		$page = new \Think\Page($count, 25);
	
	  
       $list  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.nickname,a.sex,a.id,a.parent1,a.parent2,a.parent3,a.join_time,u.active_point,u.rank,u.money")
                        ->where($where)//需要显示的字段
                        ->order($order)//需要显示的字段
                        ->limit($page -> firstRow . ',' . $page -> listRows )
                        ->select(); 
                       
        $this -> assign('list',$list);
        $this -> assign('page',$page -> show());
		$this -> display();
	}

	public function excelUser(){
		$aList = M('user_base')->alias('a')
                    ->join("dd_user u on a.id=u.user_id") //附表连主表
                    ->field("a.id,a.join_time,a.nickname,a.sex,u.rank,u.stamina,u.money,u.club_id,u.fun_amount,u.match_amount,u.advert,u.share,a.last_login_time")
                    ->order('id desc')//需要显示的字段
                    ->select(); 
         
         
        foreach ($aList as $k => $v) {
        	
	    	if($aList[$k]['sex']==1){
				$aList[$k]['sex']="男";
			}else if($aList[$k]['sex']==2){
				$aList[$k]['sex']="女";
			}else{
				$aList[$k]['sex']='未知';
			}
			$aList[$k]['rank'] = $this->getDuan($v['rank'])['level']; 

			
			if($aList[$k]['club_id']){
				$aList[$k]['club_id'] = M('club_info')->where(array('id'=>$v['club_id']))->getField('club_name');
			}else{
				$aList[$k]['club_id'] = "无";
			}
			$aList[$k]['advert'] = M('action_log')->where(array('user_id'=>$v['id'],'type'=>array('in',array(6,7))))->count();  //广告次数;

			$aList[$k]['share']  = M('action_log')->where(array('user_id'=>$v['id'],'type'=>array('in',array(1,2))))->count();  //分享次数;
			
			
        }

    
     	
		$aHead = array('UID','注册时间','昵称','性别','等级','体力值', '货币剩余', '所属公会','娱乐赛参与次数','竞技赛参与次数','广告点击次数','分享次数','最近一次登录时间');
		// $dd = new \Common\Util\excel();
		
        
        $excel = A('Excel');
        
        
        
 	    $excel->arr2ExcelDownload($aList,$aHead,'会员信息');
      
        // Excel::arr2ExcelDownload($aList,$aHead,'会员信息');
	}

public function getDuan($level){
          $filter = [
            ['level' => '青铜',  'min' => 0,  'max' => 1320],
            ['level' => '白银',  'min' => 1321,  'max' => 1500],
            ['level' => '黄金',  'min' => 1501,  'max' => 1800],
            ['level' => '铂金',  'min' => 1801,  'max' => 2000],
            ['level' => '钻石',  'min' => 2001,  'max' => 2600],
            ['level' => '王者',  'min' => 2601,  'max' => 3000],
          ];

          $result = search($level, $filter);

          return  current($result);
     }
    // 列表
	public function indexs()
	{
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['id'])) {
			$where['id'] = intval($_GET['id']);
		}

		if (!empty($_GET['openid'])) {
			echo($where['openid|bopenid'] = $_GET['openid']);
		}

		/*if (!empty($_GET['mobile'])) {
			$where['mobile'] = $_GET['mobile'];
		}

		if(!empty($_GET['name'])){
			$where['name'] = $_GET['name'];
		}*/

		if (IS_POST) {
			if ($_POST['id']) {
				$id = '';
				foreach ($_POST['id'] as $vo) {
					$ids = $vo.','.$ids;
				}

				$ids = substr($ids,0,strlen($ids)-1);
				if ($_POST['tongdao']) {
				  if ($_POST['tongdao']=='拉黑会员') {
                    $up['is_tong'] = 1;
				  } else {
                    $up['is_tong'] = 0;
				  }
				  M('user')->where(['id' => ['in',$ids]])->save($up);

				  // 取消拉黑同时删掉投诉记录
				  if (!$up['is_tong']) {
				  	M('suggest')->where(['uid' => ['in',$ids]])->delete();
				  }

                  $this->success('设置成功');
                  die; 
				}

				if ($_POST['gongpai']) {
	                if ($_POST['gongpai'] == '增加一个公排会员') {
	                    foreach($_POST['id'] as $vo){
	                    	$userinfo  = M('user')->where(array('id'=>$vo))->find();
	                    	//执行公排
	                    	//$is_gong = is_gongpai($userinfo['id']); //检查是否已经公排
	                    	//if( !$is_gong ){
	                           paiwei($userinfo);
	                    	//}
	                    }
					}

				  	if ($_POST['gongpai']=='删除一个公排会员') {
	                    //删除公排信息
	                    foreach($_POST['id'] as $vo){
	                    	$gong  = M('tree')->where(array('user_id'=>$vo))->order('id desc')->find();
	                    	M('tree')->where(array('id'=>$gong['id']))->delete();

	                    	$gong  = M('tree')->where(array('user_id'=>$vo))->order('id desc')->find();
	                    	//如果用户已经没有公排位置
	                    	if(! $gong){
	                    	 M('land')->where(array('user_id'=>$vo))->delete();
	                    	}
	                    }
				  	}
				}
			//	$this->success('操作成功','/bst_admin.php?m=Admin&c=User&a=index');
			}
		}

        $order = null;

        if(!empty($_GET['money_order'])){
            $order = $order.' money '.$_GET['money_order'].', ';
        }

        if(!empty($_GET['yingkui_order'])){
            $order = $order.' yingkui '.$_GET['yingkui_order'].', ';
        }

        if(!empty($_GET['num_order'])){
            $order = $order.' num '.$_GET['num_order'].', ';
        }

        if(!empty($_GET['withdraw_order'])){
            $order = $order.' withdraw '.$_GET['withdraw_order'].', ';
        }

        if(!empty($_GET['count_money_order'])){
            $order = $order.' count_money '.$_GET['count_money_order'].', ';
        }

        if(!empty($_GET['expense_order'])){
            $order = $order.' expense '.$_GET['expense_order'].', ';
        }

        $order = $order.' id desc';

		// if(!IS_POST){
		// 	$data['user_count'] = M('')->where()->count();
		// }

		$this -> _list('user',$where,$order);
	}

	
    public function qiehuan()
    {
	    //die();
    	if (IS_POST) {
    		if($_POST['oldid'] && $_POST['newid']){
    		$oldid = $_POST['oldid'];
    		$newid = $_POST['newid'];
//    		$olduser = send_get('http://bt.5uebuy.com/index.php?m=&c=Notify&a=getuser&id='.$oldid);
            $olduser  = json_decode($olduser,true);
            $newuser = M('user')->where(array('id'=>$newid))->find();
            $olduser['money'] = $newuser['money']+$olduser['money'];
            $olduser['openid'] = $newuser['openid'];
            if($olduser && $newuser){
             $info = M('user')->where(array('id'=>$newid))->save($olduser);
            }
            if($info){
            	$this->success('调整成功');
            	die;
            }else{
            	$this->success('调整失败');
            	die;
            }
           }
    	}
    	$this->display();
    }


	// 编辑、添加
	public function edit()
	{

		$id = intval($_GET['id']);
		$info = M('user') -> find($id);
		if(!$info){
			$this -> error('操作错误');
		}

		if(IS_POST){
			// 无上级才能修改上级
			if(!$info['parent1'] && !empty($_POST['parent1']) && $_POST['parent1'] != $info['parent1']){
				$parent_info = M('user') -> find(intval($_POST['parent1']));
				if(!$parent_info){
					$this -> error('推荐人无效');
				}

				$_POST['parent1'] = $parent_info['id'];
				for($i=1;$i<=9;$i++){
					$ii = $i+1;
					$_POST['parent'.$ii] = $parent_info['parent'.$i];
				}

				M('user') -> where(array('id' => $parent_info['id'])) -> setInc('agent1');
				M('user') -> where(array('id' => $parent_info['parent1'])) -> setInc('agent2');
				M('user') -> where(array('id' => $parent_info['parent2'])) -> setInc('agent3');
			}
		}

		$this -> _edit('user');
	}

	

	// 删除商品
	public function del()
	{
		$this -> _del('user', $_GET['id']);
		$this -> success('删除成功！');
	}



	   // 列表
	public function agent_list()
	{
		

		$this -> _list('agent_info');
	}

	public function add_agent(){
		$user_id =  intval($_GET['id']);
		if(IS_POST){
			$data = array(
				'user_id' => $user_id,
				'tel'     => $_POST['tel'],
				'remark'  => $_POST['remark'],
				'discount'=> $_POST['discount'], 
				'active'  => $_POST['active'],
				'create_time'=>date('Y-m-d H:i:s',time()),
			);
			$result = M('agent_info')->add($data);
			if($result){
				M('user')->where(array('id'=>$user_id))->setField('is_agents',1);

			}
			$this -> success('设置成功', U('agent_list'));
		}
		$this->display();
	}

	public function edit_agent(){

		$id = intval($_GET['id']);
		$info = M('agent_info') -> find($id);
		if(!$info){
			$this -> error('操作错误');
		}

		$this -> _edit('agent_info',U('agent_list'));
	}

		// 删除商品
	public function del_agent()
	{
		$this -> _del('agent_info', $_GET['id']);
		$this -> success('删除成功！');
	}


	// 充值
	public function charge()
	{
       //$this->error('该功能已关闭');
		/*if (IS_POST) {
			$user_id = intval($_POST['user_id']);
			$find = M('user') -> find($user_id);
			if(!$find){
				$this -> error('用户不存在');
			}

			$money = floatval($_POST['money']);

			M('charge_log') -> add(array(
				'user_id' => $user_id,
				'money' => $money,
				'chou' => 1,
				'remark' => $_POST['remark'],
				'create_time' => NOW_TIME
			));

			M('user') -> save(array(
				'id' => $user_id,
				'money' => array('exp','money+'.$money)
			));

			flog($user_id,'money',$money,7);

			$this -> success('操作成功');
			exit;
		}

		$this -> display();*/
	}




}