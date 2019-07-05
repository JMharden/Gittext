<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller 
{

    public function _initialize()
    {

		// 权限判断，Index控制器的不需要登录，其他的必须登录后才可以浏览
		// if (CONTROLLER_NAME != 'Index' && !session('?admin')) {
		// 	$this -> error('请登陆后操作!', U('Index/login'));
		// 	exit;
		// }

		
		// _开头的函数为内部函数，不能直接访问
		if(substr(ACTION_NAME,0,1) == '_'){
			$this -> error('访问地址错误！', U('Index/index'));
		}

		

		// -------加载配置 START----------
		$_CFG = S('sys_config');

		if(empty($_CFG) || APP_DEBUG){
			$config = M('config') -> select();
			if(!is_array($config)){
				die('请先在后台设置好各参数');
			}

			foreach($config as $v){
				$_CFG[$v['name']] = unserialize($v['value']);
			}

			unset($config);			
			S('sys_config',$_CFG);
		}

		// 循环将配置写道成员变量
		foreach($_CFG as $k => $v){
			$key  = '_'.$k;
			$this -> $key = $v;
		}

		$this -> assign('_CFG', $_CFG); // 指配到模板
		$GLOBALS['_CFG'] = $_CFG;		// 保存到全局变量
    }

	
	// 清除缓存
	public function clear_cache()
	{
		S('sys_config',null); // 系统配置缓存
		S('index_html',null); // 首页缓存

		$this -> success('操作成功');
	}

    //报表
    /**
     * 用户相关： 新增用户，总用户，留存，平均在线时长
     * 对局相关：总对局场次，今日对局场次，比赛平均时长 ，初级场总场次，中级场总场次，高级场总场次
     * 提成相关：总门票收入，提成收入，俱乐部提成收入，上级提成收入
     * 金币相关：总金币 ，玩家留存金币， 代理商留存金币  ，总门票收入 ，玩家总充值金额
     * 代理商周交易记录
     * 俱乐部长周交易记录
     */
	public function dayReport(){

		$today = strtotime(date('Y-m-d'));//时间戳
        $today_end = $today + 86400 -1;   
        $start = date('Y-m-d 0:0:0'); //日期
        $end   = date('Y-m-d 23:59:59');

        $data['user_count'] 	= M('user_base')->where(array('join_time'=>array('between',array($start,$end))))->count();//今日新增用户
        $data['user_count_all'] = M('user_base')->count();               //总用户
        $data['user_nature']    = M('user_base')->where(array('source'=>1))->count(); //自然登录注册
        $data['user_share']     = M('user_base')->where(array('source'=>array('in',array(2,3))))->count(); //分享注册用户
        $data['user_invite']    = M('user_base')->where(array('source'=>4))->count();   //邀请注册用户
        //娱乐赛
        $data['fun_share_people'] = M('action_log')->where(array('type'=>1))->count('distinct(user_id)');//分享人数
        $data['fun_share_num'] = M('action_log')->where(array('type'=>1))->count();  //分享次数
        $data['fun_advert_people'] = M('action_log')->where(array('type'=>6))->count('distinct(user_id)');//广告人数
        $data['fun_advert_num'] = M('action_log')->where(array('type'=>6))->count();  //广告次数
        $data['fun_game_count']   = M('fun_match_info')->count();//娱乐赛房间总数
        $data['fun_people_count'] = M('fun_play_log')->count('distinct(user_id)');//娱乐赛游戏人数


       //竞技赛赛
        $data['play_advert_people'] = M('action_log')->where(array('type'=>7))->count('distinct(user_id)');//广告人数
        $data['play_advert_num']    = M('action_log')->where(array('type'=>7))->count();  //广告次数
    	$data['ten_game_count']     = M('fun_match_info')->where(array('type'=>1))->count();//娱乐赛房间总数
    	$data['ten_people_count']     = M('play_log')->where(array('type'=>1))->count();//娱乐赛房间人数
    	$data['fifty_game_count']   = M('fun_match_info')->where(array('type'=>2))->count();//娱乐赛房间总数
    	$data['fifty_people_count']     = M('play_log')->where(array('type'=>2))->count();//娱乐赛房间人数
    	$data['hundred_game_count'] = M('fun_match_info')->where(array('type'=>3))->count();//娱乐赛房间总数
    	$data['hundred_people_count']     = M('play_log')->where(array('type'=>3))->count();//娱乐赛房间人数
        
        $data['game_aver_time']  = $this->match_time();//竞技赛游戏平均时长
        $data['game_fun_time']  = $this->fun_time();//竞技赛游戏平均时长
		$this -> assign($data);
		$this -> display();

    }
    //竞技赛平均时长
    public function match_time(){
        $time=M('play_match_info')->alias('a')
                ->join("dd_play_log i on a.match_id=i.match_id") //附表连主表
                ->field("i.start_time,i.end_time")
                ->where(array('i.status'=>1))//需要显示的字段
                ->select();
		$times = count($time);
		$diff = 0;

        foreach ($time as $key => $value) {

        	$time[$key]['start_time'] = strtotime($time[$key]['start_time']);
        	$time[$key]['end_time'] = strtotime($time[$key]['end_time']);
        	$time['diff'] =$time[$key]['end_time']-$time[$key]['start_time'];
			$diff+=$time['diff'];
        }
        $a = $diff/$times;
        $minute = floor($a%86400/60);
        $second = floor($a%86400%60);
        return $minute.'分'.$second.'秒';
   
    }
    //娱乐赛平均时长
	  public function fun_time(){
        $time=M('fun_match_info')->alias('a')
                ->join("dd_fun_play_log i on a.match_id=i.match_id") //附表连主表
                ->field("i.start_time,i.end_time")
                ->where(array('i.status'=>2))//需要显示的字段
                ->select();
        $times = count($time);
        $diff = 0;

        foreach ($time as $key => $value) {

            // $time[$key]['start_time'] = strtotime($time[$key]['start_time']);
            // $time[$key]['end_time'] = strtotime($time[$key]['end_time']);
            $time['diff'] =$time[$key]['end_time']-$time[$key]['start_time'];
            $diff+=$time['diff'];
        }
      
        $a = $diff/$times;
        
        $minute = floor($a%86400/60);
        $second = floor($a%86400%60);
        return $minute.'分'.$second.'秒';
   
    }


	private function GetMonth($sign="1")
    {
        //得到系统的年月
        $tmp_date=date("Ym");
        //切割出年份
        $tmp_year=substr($tmp_date,0,4);
        //切割出月份
        $tmp_mon =substr($tmp_date,4,2);
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
        $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
        if($sign==0){
            //得到当前月的下一个月
            return $fm_next_month=date("Ym",$tmp_nextmonth);
        } else {
	        //得到当前月的上一个月
	        return $fm_forward_month=date("Ym",$tmp_forwardmonth);
	    }
    }

	
	// 设置字段的值
	public function set_col($table=null)
	{
		$id = intval($_REQUEST['id']);
		$col = htmlspecialchars($_REQUEST['col']);
		$value = - abs(intval($_REQUEST['value']));
		
        $table='user';
        $money=M('charge_log')->where(array('id'=>$id,'status'=>1))->sum('money');
        if ($money<2) {
            M($table) -> where('id='.$id) -> setField($col,$value);
        }

		$this -> success('操作成功',$_SERVER['HTTP_REFERER']);
	}

	
	// 通用简单列表方法
	protected function _list($table, $where= null, $order = null)
	{
		$list = $this -> _get_list($table, $where, $order);
		$this -> assign('list', $list);
		$this -> assign('page', $this -> data['page']);
		$this -> display();
	}

	
	// 获得一个列表,返回而不输出
	protected function _get_list($table, $where= null, $order = null)
	{
		$model = M($table);
		$count = $model -> where($where) -> count();
		$page = new \Think\Page($count, 25);
		if (!$order) {

			$order = "id desc";
		}

		$list = $model -> where($where) -> limit($page -> firstRow . ',' . $page -> listRows ) -> order($order) -> select();
		
		// 将数据保存到成员变量
		$this -> data = array(
			'list' => $list,
			'page' => $page -> show(),
			'count' => $count
		);

		return $list;
	}

	
	// 通用编辑方法,根据POST自动增加或者修改
	protected function _edit($table, $url = null)
	{
		$model = M($table);
		$id = intval($_GET['id']);

		if ($id>0) {
			$info = $model -> find($id);
			if(!$info)
				die('信息不存在');

			$this -> assign('info', $info);
		}

		if (IS_POST) {
			if (!$url)
				$url = U('index');
			if ($id>0) {
				$_POST['id'] = $id;
				$model -> save($_POST);
				$this -> success('操作成功！', $url);
				exit;
			} else {
				$model -> add($_POST);
				$this -> success('添加成功！', $url);
				exit;
			}

		}

		$this -> display();
	}

	
	// 通用删除
	protected function _del($table,$id)
	{
		if($id>0 && !empty($table)) {
			M($table) -> delete($id);
		}
	}

	
	// 上传图片
	public function upload()
	{
		if(!empty($_GET['url']))
			$this -> assign('url', $_GET['url']);

		if(IS_POST) {
			if($_GET['field'])
				$field = $_GET['field'];
			if(empty($field))
				$field = 'file';

			if($_FILES[$field]['size'] < 1 && $_FILES[$field]['size']>0){
				$this -> assign('errmsg', '上传错误！');
			} else {
				$ext = $this -> _get_ext($_FILES[$field]['name']);
				$new_name = $this -> _get_new_name($ext, 'images');
				if(!in_array(strtolower($ext),C('ALLOWED_FILE_TYPES'))){
					$this -> error('上传文件不允许');
				}

				if(move_uploaded_file($_FILES[$field]['tmp_name'], $new_name['fullname'])){
					$this -> assign('url', $new_name['fullname']);
				}else
					$this -> assign('errmsg', '文件保存错误！');
			}

		}

		C('LAYOUT_ON', false);
		$this -> display('Admin/upload');
	}

	
	/**
	 *
	 *	根据文件名获取后缀
	 *
	 */
	private function _get_ext($file_name)
	{
        return substr(strtolower(strrchr($file_name, '.')),1);
    }



    /**
    *
	*	根据文件类型(后缀)生成文件名和路径
	*
	*	@param return array('name', 'fullname' )
	*
	*	* 文件名取时间戳和随机数的36进制而不是62进制是为了防止windows下大小写不敏感导致文件重名
	*
	*/
	private function _get_new_name($ext, $dir = 'default')
	{

        $name 		= date('His') . substr(microtime(),2,8) . rand(1000,9999) . '.' . $ext;
        $path 		= './Public/upload/' . $dir . date('/ym/d') .'/';

        // 如果路径不存在则递归创建
        if (!is_dir($path)) {
        	mkdir($path, 0777, 1);
        }

        return array(
    		'name'		=> $name,
    		'fullname'	=> $path . $name
    	);
    }



}?>