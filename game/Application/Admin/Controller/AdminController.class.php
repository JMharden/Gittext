<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller 
{

    public function _initialize()
    {

		// 权限判断，Index控制器的不需要登录，其他的必须登录后才可以浏览
		if (CONTROLLER_NAME != 'Index' && !session('?admin')) {
			$this -> error('请登陆后操作!', U('Index/login'));
			exit;
		}

		
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
     *用户相关： 新增用户，总用户，留存，平均在线时长
     * 对局相关：总对局场次，今日对局场次，比赛平均时长 ，初级场总场次，中级场总场次，高级场总场次
     * 提成相关：总门票收入，提成收入，俱乐部提成收入，上级提成收入
     * 金币相关：总金币 ，玩家留存金币， 代理商留存金币  ，总门票收入 ，玩家总充值金额
     * 代理商周交易记录
     * 俱乐部长周交易记录
     */
	public function dayReport(){


    }
	// 后台首界面
	public function welcome()
	{
	    $today = strtotime(date('Y-m-d'));
        $today_end = $today + 86400 -1;


        // 名称块
        $data['user_count'] 	= M('user')->where(['sub_time' => [['egt', $today], ['elt', $today_end], 'and']])->count();
        $data['user_count_all'] = M('user')->count();

        $data['pay_total'] 		= M('pay_record')->where(['ctime' => [['egt', $today], ['elt', $today_end], 'and']])->sum('total_fee');
        $data['pay_total_all']	= M('pay_record')->sum('total_fee');
        if (!$data['pay_total']) {
        	$data['pay_total'] = 0;
        }
        if (!$data['pay_total_all']) {
        	$data['pay_total_all'] = 0;
        }

        $data['out_total'] 		= M('withdraw_log')->where(['status' => 1, 'create_time' => [['egt', $today], ['elt', $today_end], 'and']])->sum('money');
        $data['out_count_all'] 	= M('withdraw_log')->where(['status' => 1])->sum('money');
        if (!$data['out_total']) {
        	$data['out_total'] = 0;
        }
        if (!$data['out_total_all']) {
        	$data['out_total_all'] = 0;
        }

        // $data['buy_count'] 		= M('buylog')->where(array('starttime'=>array(array('egt',$today),array('elt',$today_end),'and')))->count();
        // $data['buy_count_all'] 	= M('buylog')->count();


  		// 今日盈亏块
        $data['user_in'] 		= M('user')->sum('money');
		// $data['today_in'] = M('charge_log')->where(array('create_time'=>array(array('egt',$today),array('elt',$today_end),'and'),'status'=>1))->sum('money');
        $data['today_in'] 		= M('charge_log')->where(['status' => 1])->sum('money');
        if (!$data['today_in']) {
            $data['today_in'] = 0;
        }

        $data['zuo_in'] = M('charge_log')->where(array('create_time'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24),'and'),'status'=>1))->sum('money');
        if (!$data['zuo_in']) {
            $data['zuo_in'] = 0;
        }

        $data['qian_in'] = M('charge_log')->where(array('create_time'=>array(array('egt',$today-3600*24*2),array('elt',$today_end-3600*24*2),'and'),'status'=>1))->sum('money');
        if (empty($data['qian_in'])) {
            $data['qian_in'] = 0;
        } else {
            $data['qian_in'] = $data['qian_in'] / 100;
        }

        $data['today_win'] = M('buylog')->where(array('yingmoney'=>array('gt',0),'starttime'=>array(array('egt',$today),array('elt',$today_end),'and')))->sum('yingmoney');
        if (!$data['today_win']) {
            $data['today_win'] = 0;
        }

        $data['today_win_ext'] 	= M('expense')->where(array('create_time'=>array(array('egt',$today),array('elt',$today_end),'and')))->sum('money');
        $data['today_win_ext'] 	= empty($data['today_win_ext']) ? 0 : $data['today_win_ext'];
        $data['zuo_win_ext'] 	= M('expense')->where(array('create_time'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24),'and')))->sum('money');
        $data['zuo_win_ext'] 	= empty($data['zuo_win_ext']) ? 0 : $data['zuo_win_ext'];
        // $data['today_win']  	= $data['today_win'] + $data['today_win_ext'];
        $data['today_out'] 		= M('withdraw_log')->where(array('status'=>1,'create_time'=>array(array('egt',$today),array('elt',$today_end),'and')))->sum('money');
        $data['today_out'] 		= M('withdraw_log')->where(array('status'=>1))->sum('money');
        if (!$data['today_out']) {
            $data['today_out'] = 0;
        }

        $data['zuo_out'] = M('withdraw_log')->where(array('status'=>1,'create_time'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24),'and')))->sum('money');
        if (empty($data['zuo_out'])) {
            $data['zuo_out'] = 0;
        }

        $data['qian_out'] = M('withdraw_log')->where(array('status'=>1,'create_time'=>array(array('egt',$today-3600*24*2),array('elt',$today_end-3600*24*2),'and')))->sum('money');
        if (empty($data['qian_out'])) {
            $data['qian_out'] = 0;
        }

        $month 				= strtotime(date('Y-m').'-01');
        $month_end 			= strtotime($this->GetMonth(0).'01') - 1;
        $data['xiadan'] 	= M('zhuan')->where(array('addtime'=>array(array('egt',$today),array('elt',$today_end),'and')))->sum('money');
        $data['ying'] 		= M('zhuan')->where(array('addtime'=>array(array('egt',$today),array('elt',$today_end),'and')))->sum('ying');
        $data['yue_xiadan'] = M('zhuan')->where(array('addtime'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('money');
        $data['yue_ying'] 	= M('zhuan')->where(array('addtime'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('ying');
        $data['zuo_xiadan'] = M('zhuan')->where(array('addtime'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24),'and')))->sum('money');
        $data['zuo_ying'] 	= M('zhuan')->where(array('addtime'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24),'and')))->sum('ying');
        $data['qian_xiadan']= M('zhuan')->where(array('addtime'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24*2),'and')))->sum('money');
        $data['qian_ying'] 	= M('zhuan')->where(array('addtime'=>array(array('egt',$today-3600*24),array('elt',$today_end-3600*24*2),'and')))->sum('ying'); 
        $data['all_xiadan'] = M('zhuan')->sum('money');
        $data['all_ying'] 	= M('zhuan')->sum('ying'); 
        
        $data['month_in'] 	= M('wxpay_log')->where(array('log_time'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('total_fee');
        if (empty($data['month_in'])) {
            $data['month_in'] = 0;
        } else {
            $data['month_in'] = $data['month_in'] / 100;
        }

        $data['month_win'] = M('buylog')->where(array('yingmoney'=>array('gt',0),'starttime'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('yingmoney');
        if (empty($data['month_win'])) {
            $data['month_win'] = 0;
        }

        $data['month_win_ext'] 	= M('expense')->where(array('create_time'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('money');
        $data['month_win_ext'] 	= empty($data['month_win_ext']) ? 0 : $data['month_win_ext'];
        //$data['month_win']  = $data['month_win'] + $data['month_win_ext'];
        $data['month_out'] 		= M('withdraw_log')->where(array('status'=>1,'create_time'=>array(array('egt',$month),array('elt',$month_end),'and')))->sum('money');
        if (empty($data['month_out'])) {
            $data['month_out'] = 0;
        }

        $data['all_in'] = M('wxpay_log')->sum('total_fee');
        if (empty($data['all_in'])) {
            $data['all_in'] = 0;
        } else {
            $data['all_in'] = $data['all_in'] / 100;
        }

        $data['all_win'] = M('buylog')->where(array('yingmoney'=>array('gt',0)))->sum('yingmoney');
        $data['all_win_ext'] = M('expense')->sum('money');
        $data['all_win_ext'] = empty($data['all_win_ext']) ? 0 : $data['all_win_ext'];
        if (empty($data['all_win'])) {
            $data['all_win'] = 0;
        }

        //$data['all_win']  = $data['all_win'] + $data['all_win_ext'];
        $data['all_out'] = M('withdraw_log')->where(array('status'=>1))->sum('money');
        if (empty($data['all_out'])) {
            $data['all_out'] = 0;
        }

        //dump($data);
		$this -> assign($data);
		$this -> display();
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