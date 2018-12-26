<?php

namespace Admin\Controller;

use Think\Controller;


class FinanceController extends AdminController 
{

    // 佣金列表
	public function expense()
	{
		$_GET 	= array_merge($_GET,$_POST);
		$where 	= array();

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		if (!empty($_GET['buyre_id'])) {
			$where['buyre_id'] = intval($_GET['buyre_id']);
		}

		if (isset($_GET['level']) && $_GET['level'] != '') {
			$where['level'] = intval($_GET['level']);
		}

		if (isset($_GET['type']) && $_GET['type'] != '') {
			$where['type'] = intval($_GET['type']);
		}

		if (isset($_GET['sn']) && $_GET['sn'] != '') {
			$where['sn'] = I('get.sn');
		}

		$this -> _list('expense',$where);
	}

	

	// 采摘列表
	public function pickup()
	{
		$_GET 	= array_merge($_GET,$_POST);
		$where 	= array();

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		$this -> _list('pickup',$where);
	}

	

	// 偷菜列表

	public function tou()
	{
		$_GET 	= array_merge($_GET,$_POST);
		$where 	= array();

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		$this -> _list('tou',$where);
	}

	

	// 提现列表
	public function withdraw()
	{
		$_GET 	= array_merge($_GET,$_POST);
		$where 	= array();

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		if (isset($_GET['status']) && $_GET['status'] != '') {
			$where['status'] = intval($_GET['status']);
		}

		if (isset($_GET['pay_result']) && $_GET['pay_result'] != '') {
			$where['pay_result'] = intval($_GET['pay_result']);
		}

		$this -> _list('withdraw_log',$where);
	}

	
	// 系统转帐列表
	public function mch_pay()
	{
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		if (isset($_GET['status']) && $_GET['status'] != '') {
			$where['status'] = intval($_GET['status']);
		}

		$this -> _list('mch_pay_log',$where);
	}

	

	// 财务记录
	public function logs()
	{
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['type'])) {
			$where['type'] = $_GET['type'];
		}

		if (!empty($_GET['user_id'])) {
			$where['user_id'] = intval($_GET['user_id']);
		}

		$this -> _list('finance_log',$where);
	}


	// 充值记录
	public function payOrder()
	{
		$_GET = array_merge($_GET,$_POST);
		$where = array();

		if (!empty($_GET['userid'])) {
			$where['userid'] = intval($_GET['userid']);
		}

		if (!empty($_GET['order_sn'])) {
			$where['order_sn'] = $_GET['order_sn'];
		}

		if (!empty($_GET['prepay_id'])) {
			$where['prepay_id'] = $_GET['prepay_id'];
		}

		if (!empty($_GET['status'])) {
			$where['status'] = $_GET['status'];
		}

		$this -> _list('pay_order',$where);
	}



}?>