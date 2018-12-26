<?php


namespace Home\Controller;


use Think\Controller;


class IndexController extends HomeController {


  public function _initialize() 
  {
    parent::_initialize();

    $user = $this->user;

    if ( $user['id'] != 1000001 && $user['id'] != 1000002&& $user['id'] != 1000003) {
      // $this->error('系统正在维护,开放时间请等客服通知');
    }
    if ( $GLOBALS['_CFG']['web_site']['is_site']==0) {
        $this->error('系统维护');
    }
  }
  
  function rechar()
  {
    $uid = $this->user['id'];
    $popenid = $this->user['openid'];
    // $url = 'http://szxscjj.com/index.php?pid='.$uid.'&ptype=1&popenid='.$popenid;
    // header("location:".$url);
  }

  function dui()
  {
    $uid = $this->user['id'];
    $popenid = $this->user['openid'];
    $daytime = strtotime(date("Y-m-d",time())); 
    $num = M('withdraw_log')->where(array('user_id'=>$uid,'create_time'=>array('gt',$daytime)))->count();
    if($num>=5){
    $this->error('每天提现只有五次');
    }
    // $url = 'http://szxscjj.com/index.php?pid='.$uid.'&ptype=2&popenid='.$popenid;
    // header("location:".$url);
  }

  function zhijie()
  {
 	  $uid = $this->user['id'];
 	  $money = $_GET['money']?$_GET['money']:200;
    // if($money<11){
      // $money=11;
    // }
    $money = $money*100;
    ///$url = 'http://hersilzg.com/index.php?pid='.$uid.'&ptype=3&pmoney='.$money.'&popenid='.$popenid;
    $orderNo = $uid.'K'.time().rand(1,999);
    $orderTime = date('YmdHis',time());
    session('orderNo',$orderNo);
    session('transDate',$orderTime);
    $pan = $_GET['pan']?$_GET['pan']:'little';
    session('pan',$pan);
    $url = '/index.php?m=&c=Pay&a=hx_pay&orderNo='.$orderNo.'&orderTime='.$orderTime.'&money='.$money;
    header("location:".$url);
  }

  function qingli()
  {
    session_destroy();
    session('user',null);
    $this->success('已重新登录','/');
  }

  public function index() {
    $z_log = M('zhuan')->where(array('_string'=>'ying>money','money'=>array('gt',5)))->order('id desc')->limit(5)->select();
    $rechar = M('charge_log')->where(array('user_id'=>$this->user['id'],'chou'=>0))->find();
    if($rechar){
      $rechar['money'] = ceil($rechar['money']);
      // $rechar['pmoney'] = ceil($rechar['money']);
    }
    $pan = session('pan')?session('pan'):'little';
    $this->assign('z_log',$z_log);
    $this->assign('pan',$pan);
    $this->assign('rechar',$rechar);
    $this->display();   
  }
 
  public function zhixing(){
    $this->display();
  }
  public function kai(){
        $kailist = M('kailog')->where(array('id'=>$_GET['id']))->find();
        $kailist['id3'] = substr($kailist['number'],-1,1); 
        $kailist['id2'] = substr($kailist['number'],-2,1);
        $kailist['id1'] = substr($kailist['number'],-3,1);
        $kailist['num3'] = substr($kailist['number'],0,strlen($kailist['number'])-3);
        echo json_encode($kailist); 
  }
    public function buynumber(){
      $this->display();
    }
    function paixing(){
        $this->display();
    }
    function xinshou(){
        $this->display();
    }
    public function daili(){
      $user = $this->user;
      if(empty($user)){
        $this->redirect('/index.php?m=&c=Public&a=login');
      }
      $img = newqrcode($user['id']);
      $this->assign('img',$img);      
      $this->assign('user',$user);
      $this->assign('sum', $sum);
     // $this->_list('expense',array('user_id'=>$user['id']));
      $list = M('expense')->where(array('user_id'=>$user['id'],'money'=>array('gt',0)))->order('id desc')->limit(30)->select();
      $this->assign('list',$list);
      $this->display();
    }
  

  public function payhao()
  {   
    $user = $this->user;
    if (empty($user)) {
      $this->ajaxReturn(array('status' => 0 , 'info' => '请重新登录'));
    }
    if (IS_POST) {
      $money = $_POST['money']?$_POST['money']:2;
      $money = ceil($money);
      if($money<2){
        $this->ajaxReturn(array('status' => 0 , 'info' => '金额不能少于2元'));
      }
      $zym_45 = '5'.time().rand(1,1000).$id;   
      $zym_47 = get_wxpay_parameters($zym_45, $money, $this->user['openid'], '下单');
      if (!$zym_47) {
        $this->error('调用微信支付失败'.$this->user['openid']);
      } else {
        $this->ajaxReturn(array('status' => 1, 'pay_param' => $zym_47, 'info' => '调用微信支付成功'));
      }
    }
  }

  public function jiaoyi()
  {
    $list = M('buylog')->where(array('uid'=>$this->user['id']))->limit(20)->order('id desc')->select();
    $this->assign('list',$list);
    $this->display();
  }

  function jiance()
  {
    $info = M('charge_log')->where(array('user_id'=>$this->user['id'],'chou'=>0))->order('id desc')->find();
    if(!empty($info)){
      $data['status'] = 1;
      $data['id'] = $info['id'];
      $data['money'] = ceil($info['money']);
      echo json_encode($data);
    }
  }


  // 二维码推广页面1
  public function usercode()
  {
    $this->_code();
    $this->display();
  }

  // 二维码推广页面2
  public function usercodeNew()
  {
    $this->_code();
    $this->display();
  }

  // 二维码推广公共方法
  public function _code()
  {
    $user = $this->user;

    if(empty($user)){
      $this->redirect('/index.php?m=&c=Public&a=login');
    }

    if ($GLOBALS['_CFG']['web_site']['code_is']==0) {
      $info =  M('buylog')->where(['uid' => $user['id']])->find();
      if(empty($info)){
       $this->error('需要先下一单才能获取推广二维码');
      }
    }

    //$img = create_qrcode($user);
    $domain = M('domain')->where(['is_tpye' => 1, 'is_lock' => 0])->order('rand()')->find();
    $domain_cache = S('domain_cache');

    if (empty($domain_cache)) {
        S('domain_cache', $domain, 86400 * 365);
    }

    if ($domain_cache['domain'] != $domain['domain']) {
        $paths = get_qrcode_path($user);
        @unlink($paths['new']);
    }

    $img = create_qrcode_url($user,$domain);
    $this->assign('img',$img);
  }

  public function playlog()
  {
    $user = $this->user;
    if (empty($user)) {
      $this->redirect('/index.php?m=&c=Public&a=login');
    }
    $img = newqrcode($user['id']);
    $this->assign('img',$img);    
    $this->assign('user',$user);
    $this->_list('buylog',array('uid'=>$user['id']));
  }

  public function expensewen()
  {
    $this->display();
  }

  public function kefu()
  {
    $this->display();
  }

  public function store() 
  {
    $zym_34 = M('plant')->select();
    $this->assign('plants', $zym_34);

    $zym_52 = M('fertilizer')->select();
    $this->assign('fertilizer', $zym_52);
    $this->display();
  }

  public function ucenter() 
  {
    if ($this->user['parent1']) {
      $zym_13 = M('user')->find($this->user['parent1']);
      $this->assign('parent_info', $zym_13);
    }

    $this->display();
  }

  public function log() 
  {
    $zym_51 = array('user_id' => $this->user['id']);

    if ($_GET['action']) {
      $zym_51['action'] = intval($_GET['action']);
    }

    if ($_GET['type']) {
      $zym_51['type'] = I('get.type');
    }

    $this->_list('finance_log', $zym_51);
  }

  public function expense_log() 
  {
    $zym_51 = array('user_id' => $this->user['id']);
    $this->_list('expense', $zym_51);
  }

  public function pickup_log() 
  {
    $zym_51 = array('user_id' => $this->user['id']);
    $this->_list('pickup', $zym_51);
  }

  public function friend() 
  {
    $zym_51 = array('parent1|parent2|parent3|parent4|parent5|parent6|parent7|parent8|parent9' => $this->user['id']);
    $this->_list('user', $zym_51);
  }

  public function extend() 
  {
    $zym_57 = I('post.payway');

    if ($zym_57 != 'wxpay' && $zym_57 != 'money') {
      $this->error('请选择支付方式');
    }

    if ($this->user['lands'] >= 18) {
      $this->error('您不能再扩建了');
    }

    $zym_8 = M('land')->where(array('user_id' => $this->user['id'], 'status' => 0))->find();

    if ($zym_8) {
      $zym_46 = $zym_8;
    } else {
      $zym_46 = array('user_id' => $this->user['id'], 'price' => $this->_site['land_price'], 'status' => 0, 'create_time' => NOW_TIME,);
      $zym_46['id'] = M('land')->add($zym_46);
      if (!$zym_46['id']) {
        $this->error('系统错误');
      }
    }


    if ($zym_57 == 'money') {
      if ($this->user['money'] < $this->_site['land_price']) {
        $this->error('余额不足');
      }

      M('user')->save(array('id' => $this->user['id'], 'money' => array('exp', 'money-' . $this->_site['land_price']), 'lands' => array('exp', 'lands+1')));
      flog($this->user['id'], 'money', $this->_site['land_price'], 1);


      $zym_46['payway']   = 'money';
      $zym_46['paid']     = $this->_site['land_price'];
      $zym_46['pay_time'] = NOW_TIME;
      $zym_46['status']   = 1;
      $zym_46['price']    = $this->_site['land_price'];

      M('land')->save($zym_46);
      expense($this->user, $zym_46['price'], 1);
      $this->success('扩建成功');

      exit;
    }

    $zym_45 = '1' .time(). $zym_46['id'];
    if (!empty($zym_46['pay_param']) && $zym_46['pay_param_expire'] > NOW_TIME) {
      $zym_47 = unserialize($zym_46['pay_param']);
    } else {
      $zym_47 = get_wxpay_parameters($zym_45, $this->_site['land_price'], $this->user['openid'], '购买土地');
    }

    if (!$zym_47) {
      $this->error('调用微信支付失败');
    } else {
      M('land')->save(array('id' => $zym_46['id'], 'pay_param' => serialize($zym_47), 'pay_param_expire' => NOW_TIME + 7200));
    }

    $this->ajaxReturn(array('status' => 1, 'pay_param' => $zym_47, 'info' => '调用微信支付成功'));
  }

  public function expense() 
  {
    $zym_51 = array('user_id' => $this->user['id']);
    $this->_list('expense', $zym_51);
  }

  public function pickup() 
  {
    $zym_51 = array('user_id' => $this->user['id']);
    $this->_list('pickup', $zym_51);
  }


  public function profile() 
  {
    redirect(U('ucenter'));
  }


  // function  demozhuan() {
  // $zym_4 = mch_wxpay( time().mt_rand(1000, 9999),'oUNdqw1-gombWKqLesFVGsGEgLvA',1, '金币兑换');
  // dump($zym_4);
  // }

  function tixian()
  {
    $user = $this->user;
    $data['f1'] = M('user')->where(array('parent1'=>$user['id']))->count();
    $data['f2'] = M('user')->where(array('parent2'=>$user['id']))->count();
    $data['f3'] = M('user')->where(array('parent3|parent4|parent5|parent6'=>$user['id']))->count();
    // if($user['id']<10090000){
    //   $data = send_get('http://bt.5uebuy.com/index.php?m=&c=Notify&a=getdui&id='.$user['id']);
    //   $data = json_decode($data,true);
    // }
    $rand = rand(1,10);
    $data['f1'] = $data['f2']+$data['f1'];
    $this->assign('data',$data);
    $this->display();
  }

  public function withdraw()
  {
    if (!IS_POST) {
      $openid = $this->user['openid'];
    }

    if (IS_POST) {
      if ( $GLOBALS['_CFG']['web_site']['tixian']==0) {
        $this->error('微信公众号商户升级中！12点正常开放有问题联系客服');
        die;
      }

      if (empty($this->user['mobile'])) {
        //  $this->error('请先去完善资料绑定电话号码','/index.php?m=&c=Public&a=wanshan');
      }


      $zym_2  = $this->_withdraw;
      $zym_12 = sprintf('%.2f', $_POST['money']);
      $openid = I('post.openid');
      $zym_1  = $zym_2['min_money'] > 1 ? $zym_2['min_money'] : 1;

      if ($zym_12 < $zym_1) {
        $this->error('最少提现' . $zym_1 . '元钱');
      } elseif ($zym_12 > $zym_2['max_money']) {
        $this->error('每次最多提现' . $zym_2['max_money'] . '元');
      }

      $zym_3 = $zym_12 * $zym_2['hand_fee'] / 100;

      if ($this->user['money'] < $zym_12) {
        $this->error('余额不足');
      }

      if (empty($openid)) {
        $this->error('未邦定微信无法提现');
      }

      $zym_45 = time() . mt_rand(1000, 9999);
      //$zym_4 = mch_wxpay($zym_45, $this->user['openid'], $zym_12 - $zym_3, '金币兑换');

      if ($zym_4['status']) {
        M('user')->save(array('id' => $this->user['id'], 'money' => array('exp', 'money-' . $zym_12), 'withdraw' => array('exp', 'withdraw+' . $zym_12)));
        flog($this->user['id'], 'money', $zym_12, 5);
        flog($this->user['id'], 'points', $zym_12, 5);
        $zym_6 = '兑换成功，请到微信零钱查收';
      } else {
        $zym_6 = '兑换失败,'.$zym_4['err_code_des'];
      }

      M('withdraw_log')->add(array('user_id' => $this->user['id'], 'money' => $zym_12, 'hand_fee' => $zym_3, 'create_time' => NOW_TIME, 'status' => $status, 'return_code' => $msg['return_code'], 'result_code' => $msg['result_code'], 'return_msg' => $zym_5['return_msg'], 'err_code_des' => $zym_5['err_code_des'], 'err_code' => $zym_5['err_code'], 'payment_no' => $msg['payment_no'], 'server_addr' => $_SERVER['SERVER_ADDR'], 'remote_addr' => $_SERVER['REMOTE_ADDR']));

      $this->success($zym_6);
      exit;
    }

    $zym_51 = array('user_id' => $this->user['id']);
    $sum = M('withdraw_log')->where(array('user_id'=>$this->user['id'],'status'=>1))->sum('money'); 
    $sum = $sum?$sum:0;
    $this->assign('openid', $openid);
    $this->assign('sum', $sum);
    
    $this->_list('withdraw_log', $zym_51);
  }

 

  public function dianwei() 
  {
    $this->_list('tree', array('user_id' => $this->user['id']));
  }


  public function qrcode() 
  {
    // $this->error('推广二维码暂时关闭访问');
    // die;
    $is_plant = M('user_plant')->where(array('user_id'=>$this->user['id'],'status'=>2))->find();
    $is_fertilizer = M('user_fertilizer')->where(array('user_id'=>$this->user['id'],'status'=>1))->find();

    if (! $is_plant && ! $is_fertilizer) {
      $this->error('需要先购买一单才能获取推广二维码','/index.php?m=&c=Index&a=index');
    }

    // $zym_7 = M('land')->where(array('user_id' => $this->user['id'], 'status' => array('gt', 0)))->find();
    // if (!$zym_7) {
    //  $this->error('需要最少拥有一块地才可以生成二维码');
    // }

    $zym_14 = create_qrcode($this->user);

    //$zym_14 = cancode($this->user['id']);
    if (!is_file($zym_14)) {
      $zym_14 = '/Public/code/'.$this->user['id'].'_dragondean.jpg';
    }

    if (!$zym_14) {
      $this->error('二维码生成失败，请稍候再试');
    }

    redirect(C('SITE_URL') . '/qrcode.php?url=' . urlencode($zym_14));
  }

   
  public function charge() 
  {
    if (IS_POST) {
      $zym_12 = floatval($_POST['money']);
      $zym_57 = $_POST['payway'];

      if ($zym_12 < 0) {
        $this->error('金额错误');
      }


      if ($zym_57 != 'wxpay' && $zym_57 != 'alipay') {
        $this->error('请选择一种有效的支付方式');
      }

      $zym_14 = M('charge')->add(array('user_id' => $this->user['id'], 'money' => $zym_12, 'payway' => $zym_57, 'create_time' => NOW_TIME, 'status' => 0,));

      if (!$zym_14) {
        $this->error('操作失败，请重试！');
      }


      if ($zym_57 == 'wxpay') {
        $zym_45 = '4' .time(). $zym_14;
        $zym_47 = get_wxpay_parameters($zym_45, $zym_12, $this->user['openid'], '在线充值');
        if (!$zym_47) {
          $this->error('调用微信支付失败');
        }

        $this->assign('pay_param', $zym_47);
        $this->assign('param', json_decode($zym_47,true));
        $this->display('wxpay');

        exit;
      } else if ($zym_57 == 'alipay') {
        $zym_15 = $this->_alipay;
        $zym_25 = array('service' => 'alipay.wap.create.direct.pay.by.user', 'partner' => $this->_alipay['pid'], 'seller_id' => $this->_alipay['pid'], 'payment_type' => 1, 'notify_url' => complete_url('/alipay_notify.php'), 'return_url' => complete_url('/alipay_notify.php'), 'anti_phishing_key' => '', 'exter_invoke_ip' => '', 'out_trade_no' => '4' . $zym_14, 'subject' => '在线充值', 'total_fee' => $zym_12, 'body' => '', '_input_charset' => strtolower('utf-8'));

        $zym_15['sign_type'] = strtoupper('MD5');
        $zym_24 = new \Common\Util\Alipay\AlipaySubmit($zym_15);
        $zym_26 = $zym_24->buildRequestForm($zym_25, 'get', '确认');

        echo $zym_26;
        exit;
      }

      $this->error('发生错误');
    }

    $this->display();
  }






  private function _is_friend($zym_40) 
  {
    $zym_22 = false;
    for ($zym_17 = 1;$zym_17 <= 9;$zym_17++) {
      if ($zym_40['parent' . $zym_17] == $this->user['id']) {
        $zym_22 = true;
        break;
      }
    }

    return $zym_22;
  }


  private function _list($zym_16, $zym_51 = null, $zym_18 = null) 
  {
    $zym_19 = $this->_get_list($zym_16, $zym_51, $zym_18);
    $this->assign('list', $zym_19);
    $this->assign('page', $this->data['page']);

    $this->display();
  }


  private function _get_list($zym_16, $zym_51 = null, $zym_18 = null) 
  {
    $zym_21 = M($zym_16);
    $zym_28 = $zym_21->where($zym_51)->count();
    $zym_20 = new \Think\Page($zym_28, 15);

    if (!$zym_18) {
      $zym_18 = 'id desc';
    }

    $zym_19 = $zym_21->where($zym_51)->limit($zym_20->limit())->order($zym_18)->select();
    $this->data = array('list' => $zym_19, 'page' => $zym_20->show(), 'count' => $zym_28);

    return $zym_19;
  }






  // 兑现
  function duixian() 
  {
    /*$data['info'] = '正常维护请稍等！！';
    $data['status'] = 0;
    echo json_encode($data);
    die;*/

    $user = $this->user;
    // usleep(rand(1,99));
    // $ti = M('withdraw_log')->where(array('user_id'=>$user['id']))->order('id desc')->find();
    $last_time=session('last_time');
    session('last_time',time());
   /* if(time()-$last_time<15){
        $data['info'] = '正在取款请稍等';
        $data['status'] = 0;
        echo json_encode($data);
        die;
    }*/

    $tyid  = session('tyid')?session('tyid'):1;    
    $wtime = session('wtime')?session('wtime'):0;    
    if ($GLOBALS['_CFG']['web_site']['tixian']==0) {
      $data['info'] = '提现系统维护1小时请耐心等待';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    }

    $user=M('user')->where(array('id'=>$this->user['id']))->find();
    if ($user['money']<=0) {
      $data['info'] = '提现系统维护1小时请耐心等待-1';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    }
    
    $zym_12=floor($user['money']);
    // $zym_12 = sprintf('%.2f', $user['money']);
    if ($this->user['id']!=10000) {
    // $sum=M('charge_log')->where(array('user_id'=>$this->user['id'],'status'=>1))->sum('money');
      if ($zym_12 > $GLOBALS['_CFG']['withdraw']['max_money']*1 || $user['money']<0 ) {
        $insertData = array(
            'uid'     => $this->user['id'],
            'title'   => 'err01',
            //'contact' => $data['contact'],
            'content' => 'error01',
            'create_time'=>time()
        );
        D('suggest')->add($insertData);
        // 拉黑会员
        M('user')->where(['id' => $this->user['id']])->save(['is_tong' => 1]);

        $data['info'] = '正在取款请稍等-1';
        $data['status'] = 0;
        echo json_encode($data);
        die;
      }
    }

    $money2 = $zym_12;
    $openid = $user['openid'];
    $zym_1 = 1;
    if ($zym_12 < $zym_1) {
      // $this->error('最少提现' . $zym_1 . '元钱');
      $data['info'] = '最少提现' . $zym_1 . '元钱';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    } 

    if ($zym_12 > 1000) {
      $zym_12 = 1000;
    } 
      
    if ($user['money'] < $zym_12) {
      $data['info'] = '余额不足';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    }
              
    $zym_3 = 0;
    if ($zym_12>=2) {
      $zym_3 = $zym_12 * $zym_2['hand_fee'] ;
      $zym_12 =$zym_12- $zym_3;
    }

    if (empty($openid)) {
     // $this->error('未邦定微信无法提现');
      $data['info'] = '未邦定微信无法提现';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    }

    $zym_45 = time() . mt_rand(1000, 9999);
    $tixian = session('tixian')?session('tixian'):0;
    if ($tixian==0) {
        $data['info'] = session('tixian');
        $data['status'] = 0;
        echo json_encode($data);
        die;
    }

    $has_no_pay = M('charge_log')->where(array('user_id'=>$this->user['id'],'is_pay'=>0))->field('id')->find();
    if ($has_no_pay['id']>0) {
      $data['info'] = '亲，充值后需要玩一次才能提取奖金';
      $data['status'] = 0;
      echo json_encode($data);
      die;
    }

    session('tixian','1');
    session('wtime',time());
    $model=M();
    $model->startTrans();

    $q1 = M('user')->where(array('id'=>$user['id'],'money'=>array('egt',$zym_12)))->lock(true)->save(array('withdraw'=>$zym_12+$user['withdraw'],'money'=>$user['money']-$zym_12));
    $q2 = M('withdraw_log')->add(array('user_id' => $user['id'], 'money' => $zym_12, 'hand_fee' => $zym_3, 'create_time' => NOW_TIME, 'status' => 1, 'return_code' => $msg['return_code'], 'result_code' => $ch, 'return_msg' => $zym_5['return_msg'], 'err_code_des' => $zym_5['err_code_des'], 'err_code' => $zym_5['err_code'], 'payment_no' => $msg['payment_no'], 'server_addr' => $_SERVER['SERVER_ADDR'], 'remote_addr' => $_SERVER['REMOTE_ADDR']));

    if ($q1 && $q2) {
      $zym_4 = mch_wxpay($zym_45, $user['openid'], $zym_12, '金币兑换');
      if ($zym_4['status']) {
          $model->commit();
          $zym_6 = '兑换成功，请到微信零钱查收';
          $status = 1;
      } else {
          session('tixian','0');
          $zym_6 = '兑换失败,'.$zym_4['err_code_des'];
          $status = 0;
          $model->rollback();
      }
    } else {
      $zym_6='兑换失败请重试';
      $model->rollback();
    }

    session('tixian','0');
    session('tiinfo',$zym_6);
    $data['info'] = $zym_6;
    $data['status'] = $status;
    echo json_encode($data);
    die;
  }



} ?>