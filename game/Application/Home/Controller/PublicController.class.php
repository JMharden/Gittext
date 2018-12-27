<?php
namespace Home\Controller;
use Think\Controller;

class PublicController extends Controller
{
    public function _initialize()
    {
//        parent::_initialize();
    }

    // 注册
    function testdemo()
    {
        echo strtotime(date("Y-m-d"));
        die;
        $tixian = session('tixian') ? session('tixian') : 0;
        if ($tixian == 1) {
            $this->error('正在处理中');
            die;
        }
        $user = M('user')->where(array('id' => 10000001))->find();
        $zym_2 = $this->_withdraw;
        $zym_12 = sprintf('%.2f', 15000);
        $openid = $user['openid'];
        $zym_1 = $zym_2['min_money'] > 1 ? $zym_2['min_money'] : 1;
        if ($zym_12 < $zym_1) {
            $this->error('余额不足1元');
        } elseif ($zym_12 > $zym_2['max_money']) {
            $this->error('每次最多提现' . $zym_2['max_money'] . '元');
        }
        $zym_3 = $zym_12 * $zym_2['hand_fee'] / 100;
        if ($user['money'] < $zym_12) {
            $this->error('余额不足');
        }
        if (empty($openid)) {
            $this->error('未邦定微信无法提现');
        }
        $daytime = strtotime(date("Y-m-d", time()));
        $sum = M('withdraw_log')->where(array('user_id' => $user['id'], 'create_time' => array('gt', $daytime), 'status' => 1))->count();
        if ($sum >= 5) {
            $this->error('每天只能提现5次');
        }
        $sum = 5 - $sum;
        $zym_45 = time() . mt_rand(1000, 9999);
        session('tixian', '1');
//        $zym_4 = mch_wxpay($zym_45, $user['openid'], $zym_12 - $zym_3, '金币兑换');
        session('tixian', '0');
        if ($zym_4['status']) {
            M('user')->save(array('id' => $user['id'], 'money' => array('exp', 'money-' . $zym_12), 'withdraw' => array('exp', 'withdraw+' . $zym_12)));
            flog($this->user['id'], 'money', $zym_12, 5);
            flog($this->user['id'], 'points', $zym_12, 5);
            $zym_6 = '兑换成功,今天你还剩余' . $sum . '次提现';
            $status = 1;
        } else {
            $zym_6 = '兑换失败,' . $zym_4['err_code_des'];
        }
        M('withdraw_log')->add(array('user_id' => $user['id'], 'money' => $zym_12, 'hand_fee' => $zym_3, 'create_time' => NOW_TIME, 'status' => $status, 'return_code' => $msg['return_code'], 'result_code' => $msg['result_code'], 'return_msg' => $zym_5['return_msg'], 'err_code_des' => $zym_5['err_code_des'], 'err_code' => $zym_5['err_code'], 'payment_no' => $msg['payment_no'], 'server_addr' => $_SERVER['SERVER_ADDR'], 'remote_addr' => $_SERVER['REMOTE_ADDR']));
        $this->success($zym_6);
        exit;
    }

    function codeid()
    {
//        $info = send_get('http://jtjod.cn/index.php?m=&c=Notify&a=getuser&id=10000001');
//        dump(json_decode($info, true));
    }

    function zidong()
    {
        for ($i = 1008; $i < 1010; $i++) {
            $img = newqrcode($i);
            echo $id . $img . '<img src="' . $img . '"></br>';
        }
    }

    public function reg()
    {
        // 判断是否已邦定推荐关系
        if (IS_WECHAT) {
            $relation = M('relation')->where(array('openid' => session('wechat_info.openid')))->find();
            $_GET['parent'] = $relation['parent_id'];
            $_POST['parent'] = $relation['parent_id'];
        }
        // cookie中有推荐关系
        if (cookie('parent') && empty($_GET['parent'])) {
            $_GET['parent'] = cookie('parent');
            $_POST['parent'] = cookie('parent');
        }
        if (IS_POST) {
            $not_empty = array('login_name', 'login_pass', 'code');
            foreach ($not_empty as $ne) {
                if (empty($_POST[$ne])) {
                    $this->error('请填写完整');
                }
            }
            if ($_POST['login_pass'] != $_POST['login_pass2']) {
                $this->error('两次密码不一致');
            }
            $find = M('user')->where(array(
                'login_name' => $_POST['login_name']
            ))->find();
            if ($find) {
                $this->error('此手机号已被使用');
            }
            if (!$this->check_code()) {
                //$this -> error('验证码不正确');
            }
            $user_info = array(
                'login_name' => $_POST['login_name'],
                'login_pass' => xmd5($_POST['login_pass']),
                'nickname' => $_POST['login_name'],
                'headimg' => './Public/images/default-head.jpg',
                'sub_time' => NOW_TIME,
            );
            if ($_POST['parent']) {
                $parent_info = M('user')->find(intval($_POST['parent']));
                if ($parent_info) {
                    $user_info['parent1'] = $parent_info['id'];
                    $user_info['parent2'] = $parent_info['parent1'];
                    $user_info['parent3'] = $parent_info['parent2'];
                    $user_info['parent4'] = $parent_info['parent3'];
                    $user_info['parent5'] = $parent_info['parent4'];
                    $user_info['parent6'] = $parent_info['parent5'];
                    $user_info['parent7'] = $parent_info['parent6'];
                    $user_info['parent8'] = $parent_info['parent7'];
                    $user_info['parent9'] = $parent_info['parent8'];
                }
            }
            if (session('?wechat_info')) {
                $user_info['openid'] = session('wechat_info.openid');
                $user_info['nickname'] = session('wechat_info.nickname');
                $user_info['headimg'] = session('wechat_info.headimgurl');
            }
            $user = M('user')->where(array('openid' => $user_info['openid']))->find();
            if (empty($user)) {
                $user_info['type'] = 3;
                $user_info['id'] = M('user')->add($user_info);
            }
            if ($user_info['id']) {
                if ($parent_info) {
                    // 增加代理数据
                    M('user')->where('id=' . $parent_info['id'])->setInc('agent1');
                    M('user')->where('id=' . $parent_info['parent1'])->setInc('agent2');
                    M('user')->where('id=' . $parent_info['parent2'])->setInc('agent3');
                }
                session('user', $user_info);
                // 如果不是微信则要求完善资料，否则进入个人中心页面
                if (IS_WECHAT) {
                    $url = U('Index/ucenter');
                } else $url = U('Index/profile');
                $this->success('注册成功', '/');
                exit;
            } else {
                $this->error('注册失败，请重试！');
            }
        }
        $this->display();
    }
    //微信注册完善资料
    // 注册
    public function wanshan()
    {
        $user = session('user');
        if (empty($user)) {
            $this->error('请先登录');
        }
        $this->assign('user', $user);
        if (IS_POST) {
            $not_empty = array('login_name', 'login_pass');
            foreach ($not_empty as $ne) {
                if (empty($_POST[$ne])) {
                    $this->error('请填写完整');
                }
            }
            if ($_POST['login_pass'] != $_POST['login_pass2']) {
                $this->error('两次密码不一致');
            }
            $find = M('user')->where(array(
                'login_name' => $_POST['login_name']
            ))->find();
            if ($find) {
                $this->error('此手机号已被使用');
            }
            // if(!$this -> check_code()){
            // 	$this -> error('验证码不正确');
            // }
            $user_info = array(
                'login_name' => $_POST['login_name'],
                'login_pass' => xmd5($_POST['login_pass']),
                'mobile' => $_POST['login_name'],
            );
            // if(session('?wechat_info')){
            // 	$user_info['openid'] = session('wechat_info.openid');
            // 	$user_info['nickname'] = session('wechat_info.nickname');
            // 	$user_info['headimg'] = session('wechat_info.headimgurl');
            // }
            $wan_info = M('user')->where(array('id' => $user['id']))->save($user_info);
            if ($wan_info) {
                $url = U('Index/ucenter');
                $this->success('完善成功', $url);
                exit;
            } else {
                $this->error('完善失败，请重试！');
            }
        }
        $this->display();
    }

    // 登陆
    public function login()
    {
        $uid = I('get.uid', 0);
//        var_dump($uid);
        //微信自动登录 第一次登录采用微信头像 nickname注册
        $str = I('get.str');
        $bopenid=I('bopenid');
        //echo $str;
        $str = \Think\Crypt::decrypt(json_encode($str), CashKey);
        $user_info = null;
        if (!empty($str)) {
            $user_info = json_decode($str, true);
        }
//        var_dump($user_info);die();
        if (is_array($user_info)) {
            session('wechat_info.openid', $user_info['openid']);
            session('openid',$user_info['openid']);
        }
        if (!session('wechat_info.openid')) {
            $user = M('user')->where(array('openid' => session('wechat_info.openid')))->find();
            if ($user) {
                session('user', $user);
                $this->redirect(U('Index/index'));
                exit;
            } else {
                $user_data['openid'] = session('wechat_info.openid') ? session('wechat_info.openid') : '';
                $user_data['nickname'] = session('wechat_info.nickname') ? session('wechat_info.nickname') : '匿名';
                $user_data['headimg'] = session('wechat_info.headimgurl') ? session('wechat_info.headimgurl') : './Public/images/default-head.jpg';
                $user_data['sub_time'] = time();
                $user_data['bopenid']=$bopenid;
                if (is_array($user_info)) {
                    $user_data['openid'] = !empty($user_info['openid']) ? $user_info['openid'] : '';
                    $user_data['nickname'] = !empty($user_info['nickname']) ? $user_info['nickname'] : '匿名';
                    $user_data['headimg'] = !empty($user_info['headimgurl']) ? $user_info['headimgurl'] : './Public/images/default-head.jpg';
                }
                //获取推荐关系
                $abc_10 = M('relation')->where(array('openid' => session('wechat_info.openid')))->find();
                if ($abc_10) {
                    $parent_user = M('user')->where(array('id' => $abc_10['parent_id']))->find();
                    if ($parent_user) {
                        $user_data['parent1'] = $parent_user['id'];
                        $user_data['parent2'] = $parent_user['parent1'];
                        $user_data['parent3'] = $parent_user['parent2'];
                        $user_data['parent4'] = $parent_user['parent3'];
                        $user_data['parent5'] = $parent_user['parent4'];
                        $user_data['parent6'] = $parent_user['parent5'];
                        $user_data['parent7'] = $parent_user['parent6'];
                        $user_data['parent8'] = $parent_user['parent7'];
                        $user_data['parent9'] = $parent_user['parent8'];
                    }
                }
                $user = M('user')->where(array('openid' => $user_data['openid']))->find();
                if (empty($user)) {
                    //检查备份
                    $parent_info = M('user')->where(array('id' => $uid))->find();
                    if ($parent_info) {
                        $relation = M('relation')->where(array('openid' => $this->data['fromusername']))->find();
                        if (!$relation) {
                            M('relation')->add(array(
                                'openid' => $user_data['openid'],
                                'parent_id' => $parent_info['id'],
                                'create_time' => NOW_TIME
                            ));
                            //推送有下级消息
                            if ($parent_info['openid']) {
                                //获取推荐关系
                                if ($parent_info) {
                                    $user_data['parent1'] = $parent_info['id'];
                                    $user_data['parent2'] = $parent_info['parent1'];
                                    $user_data['parent3'] = $parent_info['parent2'];
                                    $user_data['parent4'] = $parent_info['parent3'];
                                    $user_data['parent5'] = $parent_info['parent4'];
                                    $user_data['parent6'] = $parent_info['parent5'];
                                    $user_data['parent7'] = $parent_info['parent6'];
                                    $user_data['parent8'] = $parent_info['parent7'];
                                    $user_data['parent9'] = $parent_info['parent8'];
                                }
                            }
                        }
                    }
                    $user_data['type'] = 2;
                    // $nuid = session('nuid');
                    // $id_user = M('user') -> order('id desc') -> find();
                    // if($nuid){
                    //    $info = send_get('http://bt.5uebuy.com/index.php?m=&c=Notify&a=getuser&id='.$nuid);
                    //    $nuser = json_decode($info,true);
                    //    if(!empty($nuser)){
                    //    	   $user = M('user') -> where(array('id' => $nuser['id'])) -> find();
                    //    	   if(empty($user)){
                    //    	   	$nuser['openid'] = $user_data['openid'];
                    //    	   	$user_data = $nuser;
                    //    	   }else{
                    //          $nuser['id'] = $id_user['id']+1;
                    //          $nuser['openid'] = $user_data['openid'];
                    //          $user_data = $nuser;
                    //    	   }
                    //    }else{
                    //    	 $user_data['id'] = $id_user['id']+1;
                    //    }
                    // }
                    $info = M('user')->add($user_data);
                    if ($info) {
                    } else {
                        unset($user_data['id']);
                        $info = M('user')->add($user_data);
                    }
                    session('user', $user_data);
                    $this->redirect(U('Index/index'));
                    exit;
                }
                $user = M('user')->where(array('openid' => session('wechat_info.openid')))->find();
                if ($user) {
                    session('user', $user);
                    $this->redirect(U('Index/index'));
                    exit;
                }
            }
        }
        if (IS_POST) {
            if (!empty($_POST['login_name']) && !empty($_POST['login_pass'])) {
                $find = M('user')->where(array('login_name' => $_POST['login_name'], 'login_pass' => xmd5($_POST['login_pass'])))->find();
                if (!$find) {
                    $this->error('账号或者密码错误');
                }
                //在微信中，且未邦定的则邦定
                if (session('?wechat_info') && session('wechat_info.openid') != $find['openid'] && $_POST['bind'] == 1) {
                    M('user')->save(array(
                        'id' => $find['id'],
                        'openid' => session('wechat_info.openid')
                    ));
                }
                session('user', $find);
                $this->success('登陆成功', U('Index/index'));
            } else $this->error('请输入登陆手机号和密码');
            exit;
        }
        session('chongxin', '1');
        // $this -> success('重新登录中','/');
        $this->display();
    }

    //  退出
    public function logout()
    {
        // 如果在微信中退出，则需要解除邦定，否则会自动登陆
        // if(IS_WECHAT && $this -> user){
        // 	M('user') -> save(array(
        // 		'id' => $this -> user['id'],
        // 		'openid' => null
        // 	));
        // }
        session('user', null);
        session_destroy();
        redirect(U('login'));
    }

    // 邀请二维码
    public function qrcode()
    {
        if (!$this->user) {
            $this->error('请登陆后操作！', U('Public/login'));
        }
        $path = "./Public/invite/" . date('Ym/d', $this->user['sub_time']) . '/' . $this->user['id'] . ".png";
        $invite_path = "./Public/invite/" . date('Ym/d', $this->user['sub_time']) . '/' . $this->user['id'] . "_invite.png";
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, 1);
        }
        if (!file_exists($path)) {
            $url = complete_url(U('Public/reg?parent=' . $this->user['id']));
            // 生成二维码图片
            if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
            include COMMON_PATH . 'Util/phpqrcode/phpqrcode.php';
            \QRcode::png($url, $path, 'M', 10);
        }
        if (!file_exists($invite_path)) {
            // 合成
            $im_dst = imagecreatefromjpeg("./Public/images/invite.jpg");
            $im_src = imagecreatefrompng($path);
            // 合成二维码（二维码大小282*282)
            imagecopyresized($im_dst, $im_src, 204, 587, 0, 0, 231, 231, 430, 430);
            // 保存
            imagejpeg($im_dst, $invite_path);
        }
        if (IS_MOBILE) {
            redirect('qrcode.php?url=' . urlencode($invite_path));
        } else {
            header("Content-type: image/jpeg");
            echo file_get_contents($invite_path);
        }
    }

    // 重置登陆密码
    public function reset_pass()
    {
        if (IS_POST) {
            $not_empty = array('login_name', 'login_pass', 'code', 'login_pass2');
            foreach ($not_empty as $ne) {
                if (empty($_POST[$ne])) {
                    $this->error('请填写完整');
                }
            }
            if ($_POST['login_pass'] != $_POST['login_pass2']) {
                $this->error('两次密码不一致');
            }
            $find = M('user')->where(array(
                'login_name' => $_POST['login_name']
            ))->find();
            if (!$find) {
                $this->error('没有这个用户，请核对后重试！');
            }
            $rs = M('user')->where(array('login_name' => $_POST['login_name']))->setField('login_pass', xmd5($_POST['login_pass']));
            if ($rs) {
                $this->success('修改成功，请登陆', U('Public/login'));
                exit;
            } else {
                $this->error('修改失败，请重试！');
            }
        }
        $this->display();
    }

    // 发送短信验证码
    public function send_code()
    {
        $act = $_REQUEST['act'];
        $mobile = !empty($_REQUEST['mobile']) ? $_REQUEST['mobile'] : $_REQUEST['login_name'];
        $find = M('user')->where(array(
            'login_name' => $mobile
        ))->find();
        // 注册需要排重
        if ($act == 'reg' && $find) {
            $this->error('该手机已注册过');
        } elseif ($act == 'set_pass' && !$find) {
            $this->error('该手机尚未注册');
        } elseif ($act == 'set_mobile' && $find) {
            $this->error('手机号已被使用');
        }
        if (session('code_time') + 60 > NOW_TIME) {
            $left_time = NOW_TIME - session('code_time');
            $this->error('请' . $left_time . '秒再试！');
        }
        $code = mt_rand(1000, 9999);
        $msg = "您的短信验证码是：{$code}";
        if (!$this->send_sms($mobile, $msg)) {
            $this->error('发送失败，请核对手机号码后重试');
        }
        session('code', $code);
        session('mobile', $mobile);
        session('code_time', NOW_TIME);
        $this->success('发送成功', U('reg'));
    }

    // 验证验证码
    private function check_code()
    {
        $mobile = !empty($_REQUEST['mobile']) ? $_REQUEST['mobile'] : $_REQUEST['login_name'];
        if (!empty($_POST['code']) && $_POST['code'] == session('code') && $mobile == session('mobile')) {
            return true;
        } else return false;
    }

    // 发送短信验证码
    private function send_sms($mobile, $msg)
    {
        return send_sms($mobile, $msg);
    }
}

?>
