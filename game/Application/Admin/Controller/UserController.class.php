<?php

namespace Admin\Controller;
use Think\Controller;

class UserController extends AdminController 
{
	//用户列表
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
		$order = $order.'id desc';
		$model = M('user_base');
		$count = $model -> where($where) -> count();
		$page = new \Think\Page($count, 25);
	
	  
       $list  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.id,a.nickname,a.sex,a.id,a.parent1,a.parent2,a.parent3,a.join_time,u.club_id,u.active_point,u.rank,u.money")
                        ->where($where)//需要显示的字段
                        ->order($order)//需要显示的字段
                        ->limit($page -> firstRow . ',' . $page -> listRows )
                        ->select(); 
        foreach ($list as $k => $v) {
        	$list[$k]['fun_game'] =  M('fun_play_log')->where(array('user_id'=>$v['id']))->count();//娱乐赛房间总数
        	$list[$k]['play_game']=  M('play_log')->where(array('user_id'=>$v['id']))->count();//竞技赛房间总数
        	$list[$k]['advert']   =  M('action_log')->where(array('type'=>array('in',array(6,7))))->count();  //广告次数
        	$list[$k]['share']    =  M('action_log')->where(array('user_id'=>$v['id'],'type'=>array('in',array(1,2))))->count();
			if($list[$k]['club_id']){
				$list[$k]['club_id'] = M('club_info')->where(array('id'=>$v['club_id']))->getField('club_name');
			}else{
				$list[$k]['club_id'] = "无";
			}
        }
       
        $this -> assign('list',$list);
        $this -> assign('page',$page -> show());
		$this -> display();
	}
	public function feedback() 
    {
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['number'])) {
			$where['number'] = array('like','%'.$_GET['number'].'%');
			
		}
		$this -> _list('feedback',$where,'id desc');
	}
	//导出用户数据
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

    
     	 Vendor("Excel.excel");  //加载解密文件，在官方有下载
        
        $excel = new \excel();
		$aHead = array('UID','注册时间','昵称','性别','等级','体力值', '货币剩余', '所属公会','娱乐赛参与次数','竞技赛参与次数','广告点击次数','分享次数','最近一次登录时间');

        
        // $excel = A('Excel');
        
        
        
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

		$info = M('user')->where(array('id'=>$id))->find();
		// var_dump($info);exit;
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
		// $this -> _del('user', $_GET['id']);
	  $user_id= $_GET['id'];
	  $user = M('user')->where(array('user_id'=>$user_id))->delete();
      $userBase = M('user_base')->where(array('id'=>$user_id))->delete();
      $userSlime = M('user_slime')->where(array('u_id'=>$user_id))->delete();
      if($user && $userBase && $userSlime){
        $this -> success($user_id.'用户删除成功！');
      }
		
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


	




}