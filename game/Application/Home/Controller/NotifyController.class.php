<?php

namespace Home\Controller;

use Think\Controller;

class NotifyController extends Controller
{


    public function _initialize()
    {

        // 加载配置
        $config = M('config')->select();

        if (!is_array($config)) {
            die('请先在后台设置好各参数');
        }

        foreach ($config as $v) {
            $key = '_' . $v['name'];
            $this->$key = unserialize($v['value']);
            $_CFG[$v['name']] = $this->$key;
        }

        $GLOBALS['_CFG'] = $_CFG;

    }


    function demo2()
    {
        $data = getSignPackage();
        $this->assign('data', $data);
        $this->display();
    }


    public function xpay()
    {
        die();
        $orderNo = $_GET['orderNo'] ? $_GET['orderNo'] : '';
        $money = $_GET['money'] ? $_GET['money'] : 200;
        if ($money < 200) {
            $this->error('金额不能少于2元');
        }

        $money = $money - rand(1, 10);
        $merch = '1714984915';
        $mch_secret = 'e9fa1fc2745718eb5e3e94bb9e5165d5';
        $orderip = '112.112.112.1';
        $amount = $money;
        $type = 11;
        $desc = time();
        $notifyurl = 'http://doudousoft.cn/apay.php';
        $backurl = 'http://doudousoft.cn/tiao.html';
        $product = $orderNo;
        $extra = $orderNo;
        $sign = md5("amount=" . $amount . "&backurl=" . $backurl . "&desc=" . $desc . "&extra=" . $extra . "&merch
	=" . $merch . "&notifyurl=" . $notifyurl . "&product=" . $product . "&type=" . $type . "&key=" . $mch_secret);
        $url = 'http://api.xiipay.com/Pay?notifyurl=' . $notifyurl . '&mch_secret=' . $mch_secret . '&amount=' . $amount . '&type=' . $type . '&desc=' . $desc . '&merch=' . $merch . '&backurl=' . $backurl . '&product=' . $product . '&extra=' . $extra . '&sign=' . $sign;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        $info = curl_exec($ch);
        curl_close($ch);
        $info = json_decode($info, true);
        if ($info['err'] != '0') {
            $this->error('支付失败,' . $info['msg']);
        }
        $url = $info['payurl'];
        header('location:' . $url);
    }


    public function pay()
    {
        die();
        $merchant = '1003170518717521';
        $sn = getsn($merchant);
        $uid = $_GET['uid'] ? $_GET['uid'] : 1;
        $sn = $uid . 'K' . time() . rand(1, 999);;
        $deviceInfo = 'deviceInfo1';
        $money = $_GET['money'] ? $_GET['money'] : 1;
        $totalAmount = $money;
        $subject = iconv("GB2312", "UTF-8", "apple-1");
        $callBack = "http://wx.muziwu.net/pay.php";
        $channel = "weixin";
        $remark = iconv("GB2312", "UTF-8", "充值");
        $key = 'NORD8XW7';
        $content = $sn . $merchant . $deviceInfo . $totalAmount . $subject . $callBack . $channel . $remark . $key;
        $sign_md5 = md5($content);
        $sign = sign_sha1($sign_md5);
        $keyStr16 = generate_randsn(16);
        $busiType = '100001';
        $aes_content = $sign_md5 . $busiType;
        $encryptData = sign_aes($aes_content, $keyStr16);
        $encryptKey = sign_rsa($keyStr16);
        $curlPost = 'merchant=' . $merchant . '&sn=' . $sn . '&deviceInfo=' . $deviceInfo . '&totalAmount=' . $totalAmount . '&subject=' . $subject . '&callBack=' . $callBack . '&channel=' . $channel . '&busiType=' . $busiType . '&remark=' . $remark . '&sign=' . urlencode($sign) . '&encryptData=' . urlencode($encryptData) . '&encryptKey=' . urlencode($encryptKey);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://pay.dingzx.net:8170/BusiM/AL001/Check');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataJson = json_decode($data);
        $codeRsp = $dataJson->codeRsp;
        $code = $codeRsp->code;
        $encrypt = $dataJson->encrypt;
        if ($encrypt) {
            $en_encryptKey = $encrypt->encryptKey;
            $en_encryptData = $encrypt->encryptData;
            $deKeyStr16 = decode_rsa(urldecode($en_encryptKey));
            $deData = decode_aes($deKeyStr16, urldecode($en_encryptData));
            $dataJsonRs = json_decode(substr($deData, 0, strrpos($deData, '}') + 1));
        }
        if ($code == '8001') {
            $codetext = $codeRsp->qrCode;
            $save_path = './Public/codeimg/';  //图片存储的绝对路径
            $web_path = './Public/codeimg/';        //图片在网页上显示的路径
            $qr_data = $codetext;
            $qr_level = 'H';
            $qr_size = '10';
            $save_prefix = 'ZETA';
            if ($filename = createQRcode($save_path, $qr_data, $qr_level, $qr_size, $save_prefix)) {
                $pic = $web_path . $filename;
                $this->assign('pic', $pic);
                $this->assign('sn', $sn);
                $this->assign('totalAmount', $totalAmount);
                $data = getSignPackage();
                $this->assign('data', $data);
                $this->display();
            } else {
                $this->error('生成支付二维码失败');
            }

        } else {
            $this->error('生成支付失败,请返回重新点击');
        }
    }


    function xcallblack()
    {
        die();
        $info = $_POST;
        if (!empty($info['extra'])) {
            $check = M('charge_log')->where(array('remark' => $info['extra']))->find();
            $add['user_id'] = strtok($info['extra'], "K");
            $user = M('user')->where(array('id' => $add['user_id']))->find();
            if (empty($check) && !empty($user)) {
                $add['money'] = ceil($info['amount'] / 100);
                $add['remark'] = $info['extra'];
                $add['create_time'] = time();
                M('charge_log')->add($add);
                //M('user')->where(array('id'=>$user['id']))->save(array('money'=>$info['payAmount']+$user['money']));
                echo 'SUCCESS';
            }

        }
    }


    function callb()
    {
        die();
        $data = $_POST['message'];
        $info = json_decode($data, true);
        if ($info['code'] == '0000') {
            $check = M('charge_log')->where(array('remark' => $info['responeSN']))->find();
            $add['user_id'] = strtok($info['srcReqSN'], "K");
            $user = M('user')->where(array('id' => $add['user_id']))->find();
            if (empty($check) && !empty($user)) {
                $add['money'] = $info['payAmount'];
                $add['remark'] = $info['responeSN'];
                $add['create_time'] = time();
                M('charge_log')->add($add);
                M('user')->where(array('id' => $user['id']))->save(array('money' => $info['payAmount'] + $user['money']));
            }

        }
    }


    function apay()
    {
        die();
        $uid = $_GET['uid'] ? $_GET['uid'] : 1;
        $sn = $uid . 'K' . date("YmdHis") . rand(1, 9999);;
        $money = $_GET['money'] ? $_GET['money'] : 1;
        $money = $money - rand(1, 20) / 100;
        $params = array(
            'order_id' => $sn,
            'channel' => '2997568282@qq.com',
            'mch' => '1255046197962',
            'key' => '43da6b7021667754983e22fb4f0eab8d',
            'money' => $money * 100,
            'pay_type' => 'wxwap',
            'time' => time(),
            'extra' => $uid,
            'return_url' => 'http://bt.pvblv.cn/',
            'notify_url' => 'http://bt.pvblv.cn/apay.php',
            'sign' => ''
        );
        $sign = $params['order_id'] . $params['money'] . $params['pay_type'] . $params['time'] . $params['mch'] . md5($params['key']);
        $params['sign'] = md5($sign);
        $link = 'http://api.viapay.cn/waporder/order_add?notify_url=' . $params['notify_url'] . '&mch=' . $params['mch'] . '&key=' . $params['key'] . '&channel=' . $params['channel'] . '&money=' . $params['money'] . '&time=' . $params['time'] . '&sign=' . $params['sign'] . '&order_id=' . $params['order_id'] . '&return_url=' . $params['return_url'] . '&pay_type=' . $params['pay_type'] . '&extra=' . $params['extra'];
        header("location:" . $link);
    }


    // 微信支付通知异步页面
    function apaycall()
    {
        die();
        $info = $_POST;
        if (!empty($info['order_id'])) {
            $check = M('charge_log')->where(array('remark' => $info['order_id']))->find();
            $add['user_id'] = strtok($info['order_id'], "K");
            $user = M('user')->where(array('id' => $add['user_id']))->find();
            if (empty($check) && !empty($user)) {
                $add['money'] = $info['money'] / 100;
                $add['remark'] = $info['order_id'];
                $add['create_time'] = time();
                M('charge_log')->add($add);
                M('user')->where(array('id' => $user['id']))->save(array('money' => $add['money'] + $user['money']));
                echo 'success';
            }

        }
    }

    public function index()
    {
        die();
        $jsapi = new \Common\Util\wxjspay;
        $jsapi->set_param('key', $this->_pay_mp['key']);

        // 验证签名之前必须调用get_notify_data方法获取数据
        $data = $jsapi->get_notify_data();
        file_put_contents('wxpay.log', json_encode($data) . "\r\n", FILE_APPEND);
        if (!$jsapi->check_sign()) {
            file_put_contents('wxpay.log', "\r\nCHECK SIGN FAIL\r\n", FILE_APPEND);
            // 签名验证失败
            die('FAIL');
        }

        if ($data['return_code'] != 'SUCCESS' || $data['result_code'] != 'SUCCESS') {
            file_put_contents('wxpay.log', "\r\RETURN CODE FAIL\r\n", FILE_APPEND);
            die('FAIL');
        }

        $sn = $data['out_trade_no'];
        $money = $data['total_fee'] / 100;
        $payway = 'wxpay';
        $type = substr($sn, 0, 1);
        $buyid = substr($sn, strlen($sn) - 1, 1);
        $buyid = $buyid ? $buyid : 1;
        $data['log_time'] = NOW_TIME;
        $is_pay = M('wxpay_log')->where(array('out_trade_no' => $data['out_trade_no']))->find();
        if ($is_pay) {
            die;
        }

        // 记录支付日志
        $data['log_time'] = NOW_TIME;
        $data['type'] = $type;
        if ($data) {
            M('wxpay_log')->add($data);
        }

        if ($data['out_trade_no']) {
            $check = M('charge_log')->where(array('remark' => $sn))->find();
            $user = M('user')->where(array('bopenid' => $data['openid']))->find();
            if (empty($check) && !empty($user)) {
                $add['user_id'] = $user['id'];
                $add['money'] = $money;
                $add['remark'] = $sn;
                $add['create_time'] = time();
                $add['chou'] = 1;
                M('charge_log')->add($add);
                M('user')->where(array('id' => $user['id']))->save(array('money' => $add['money'] + $user['money']));
            }
        }

        // 支付日志
        if (M('wxpay_log')->where(array('transaction_id' => $data['transaction_id']))->find()) {
            // redirect(U('Index/index'));
            die('SUCCESS');
        }

        // redirect(U('Index/index'));
        die('SUCCESS');
    }// index


    public function hx_pay()
    {
        $param = $_POST;
        //  $param= file_get_contents('php://input');
        $this->mylog(json_encode($param));
        $signIn = $param['sign'];
        unset($param['sign']);
        ksort($param);
        $key = C('hx_pay.key');
        $str='';
        foreach($param as $k=>$v){
            $str .= '&'.$k.'='.$v;
        }
        $str=substr($str,1);
        $sign = md5($str . $key);
        $sign = strtoupper($sign);
        if (strcmp($signIn, $sign) == 0) {
            $data=json_decode($param['data']);
            $out_trade_no=htmlspecialchars($data->merchant_order_sn);
            $money=htmlspecialchars($data->total_fee);
            $check = M('charge_log')->where(array('remark'=>$out_trade_no))->find();
            if($check['money']!=$money){
               die();
            }
            $user_id=$check['user_id'];
            // $openid = $open_arr[1];
            if ($user = M('user')->where(array('id' => $user_id))->find()) {
                $model = M();
                $model->startTrans();
                $q1 = M('charge_log')->where(array('remark' => $out_trade_no, 'status' => 0))->save(array('status' => 1));
                // $q2=M('user')->where(array('id'=>$user['id']))->save(array('money'=>$add['money']+$user['money']));
                $q2 = M('user')->where(array('id' => $user['id']))->setInc('money', $money);
                if ($q1 && $q2) {
                    $model->commit();
                    echo 'SUCCESS';
                } else {
                    $model->rollback();
                    echo 'err01';
                }
            }else{
                echo 'err02';
            }
        }else{
            echo 'success';
        }

    }


    public function hx_pay_old()
    {
        $param = $_POST;
        // $param= file_get_contents('php://input');
        $this->mylog(json_encode($param));
        $signIn = $param['signIn'];
        unset($param['signIn']);
        ksort($param);
        $key = 'fdb2841c18114a14b283c15cf8dc47b1';
        // echo '<pre>';
        $str='';
        foreach($param as $k=>$v){
            $str .= '&'.$k.'='.$v;
        }
        $str=substr($str,1);
        $sign = md5($str . $key);
        $sign = strtoupper($sign);
        if (strcmp($signIn, $sign) == 0) {
        // $check = M('charge_log')->where(array('remark'=>I('orderNo')))->find();
            $open_arr = explode('|||', $param['uuid']);
            $openid = $open_arr[1];
            if ($user = M('user')->where(array('openid' => $openid))->find()) {
                $model = M();
                $model->startTrans();
                $q1 = M('charge_log')->where(array('remark' => I('orderNo'), 'status' => 0))->save(array('status' => 1));
                // $q2=M('user')->where(array('id'=>$user['id']))->save(array('money'=>$add['money']+$user['money']));
                $q2 = M('user')->where(array('id' => $user['id']))->setInc('money', round(I('amount') / 100, 2));
                if ($q1 && $q2) {
                    $model->commit();
                    echo 'ok';
                } else {
                    $model->rollback();
                    echo 'err01';
                }
            }else{
                echo 'err02';
            }
        }else{
            echo 'err03';
        }

    }


    public function mylog($str)
    {
        $file = './pay.log';
        if (file_exists($file)) {
            $file_str = file_get_contents($file);
            $str = $file_str . "\n" . $str;
        }
        file_put_contents($file, $str);
    }


    function mpay()
    {
        die();
        $unSignKeyList = array("sign");
        $respJson = $_POST;
        $sign = $respJson['sign'];
        $respSign = signMD5($respJson, $unSignKeyList);

        if ($sign != $respSign) {
            $info = M('user')->where(array('id' => 1))->save(array('qingli' => '113232'));
            echo "验证签名失败！";
        } else {

            if ($sign == $_REQUEST["sign"]) {
                if ($_REQUEST["returnCode"] == "0") {
                    //请在此添加支付成功后的操作,修改数据库
                    $info = M('user')->where(array('id' => 1))->save(array('qingli' => '3232'));
                }
            }
        }

    }


    function get_buydata()
    {

        die();
        $openid = I('post.openid');
        $user_info = M('user')->where(array('id' => $openid))->find();
        if ($user_info && I('post.money') && I('post.buyid')) {

            $add['uid'] = $user_info['id'];
            $add['money'] = I('post.money');
            $type = 1;
            $money = I('post.money');
            $add['buyid'] = $buyid = I('post.buyid');
            if ($buyid == 1) {
                $name = '香蕉';
            }
            if ($buyid == 2) {
                $name = '西瓜';
            }
            if ($buyid == 3) {
                $name = '苹果';
            }
            $add['buyname'] = $name;
            $kailist = M('kailog')->where(array('status' => 1))->order('id desc')->find();
            $time = time();
            $now = strtotime(date('Y-m-d H:i'));
            if (empty($kailist)) {
                $data['starttime'] = $now;
                $data['endtime'] = $now + 60;
                $data['status'] = 1;
                M('kailog')->add($data);
                $kailist = M('kailog')->where(array('status' => 1))->order('id desc')->find();
            }
            $add['kid'] = $kailist['id'];
            $add['starttime'] = $now;
            $add['status'] = 1;
            $add['endtime'] = $kailist['endtime'];
            $info = M('buylog')->add($add);
            if ($info) {
                if ($buyid == 1) {
                    M('kailog')->where(array('id' => $kailist['id']))->save(array('kid1' => $kailist['kid1'] + $money, 'allmoney' => $kailist['allmoney'] + $money, 'allnum' => $kailist['allnum'] + 1));
                } else if ($buyid == 2) {
                    M('kailog')->where(array('id' => $kailist['id']))->save(array('kid2' => $kailist['kid2'] + $money, 'allmoney' => $kailist['allmoney'] + $money, 'allnum' => $kailist['allnum'] + 1));
                } else if ($buyid == 3) {
                    M('kailog')->where(array('id' => $kailist['id']))->save(array('kid3' => $kailist['kid3'] + $money, 'allmoney' => $kailist['allmoney'] + $money, 'allnum' => $kailist['allnum'] + 1));
                }
            }
            M('user')->where(array('id' => $user_info['id']))->save(array('num' => $user_info['num'] + 1, 'yingkui' => $user_info['yingkui'] - $money, 'count_money' => $user_info['count_money'] + $money));
            expense($user_info, $money, $type);
        }
    }

    // 支付宝同步/异步通知
    public function alipay()
    {

        die();
        $alipay_config['partner'] = $GLOBALS['_CFG']['alipay']['pid'];
        $alipay_config['transport'] = 'http';
        $alipay_config['sign_type'] = strtoupper('MD5');
        $alipay_config['key'] = $GLOBALS['_CFG']['alipay']['key'];
        $alipayNotify = new \Common\Util\Alipay\AlipayNotify($alipay_config);

        if (IS_POST) {
            $verify_result = $alipayNotify->verifyNotify();
        } else {
            $verify_result = $alipayNotify->verifyReturn();
        }


        if ($verify_result) {//验证成功
            //商户订单号
            $out_trade_no = $_REQUEST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_REQUEST['trade_no'];

            //交易状态
            $trade_status = $_REQUEST['trade_status'];


            if ($_REQUEST['trade_status'] == 'TRADE_FINISHED' || $_REQUEST['trade_status'] == 'TRADE_SUCCESS') {
                // 先判断是否处理
                $has_done = M('alipay_log')->where(array(
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $trade_no
                ))->find();


                // 没有处理过才处理
                if (!$has_done) {
                    $money = $_REQUEST['total_fee'];
                    $payway = 'alipay';
                    $type = substr($out_trade_no, 0, 1);
                    $tables = array(
                        1 => 'land',
                        2 => 'user_plant',
                        3 => 'user_fertilizer',
                        4 => 'charge',
                        8 => 'chou'
                    );

                    $table = $tables[$type];
                    if (empty($table)) {
                        elog('alipay out_trade_no type error');
                        die('FAIL');
                    }

                    $id = substr($out_trade_no, 1);
                    $info = M($table)->find($id);

                    M($table)->save(array(
                        'id' => $id,
                        'status' => 1,
                        'pay_time' => NOW_TIME,
                        'payway' => 'wxpay',
                        'paid' => $money
                    ));


                    // 如果是扩建，则需要增加用户的土地

                    if ($type == 1) {
                        M('user')->where('id=' . $info['user_id'])->setInc('lands');
                    } elseif ($type == 4) { // 如果是充值
                        M('user')->where('id=' . $info['user_id'])->save(array(
                            'money' => array('exp', 'money+' . $info['money'])
                        ));
                    }


                    M('alipay_log')->add(array(
                        'out_trade_no' => $out_trade_no,
                        'trade_no' => $trade_no,
                        'total_fee' => $_REQUEST['total_fee'],
                        'create_time' => NOW_TIME,
                        'seller_email' => $REQUEST['seller_email'],
                        'buyer_email' => $REQUEST['buyer_email'],
                        'seller_id' => $REQUEST['seller_id'],
                        'buyer_id' => $REQUEST['buyer_id'],
                    ));

                }

            } else die('error');

            if (IS_POST) {
                echo "success";        //请不要修改或删除
            } else {
                redirect('index.php?a=ucenter');
            }
        } else {
            //验证失败
            echo "fail";
        }
    }

    // 生活圈付呗支付成功异步回调
    public function livePay()
    {
        /*M('charge_log')->add(['remark' => json_encode($_POST)]);
        echo 'success';*/

        // 获取回调数据
        $post           = $_POST;
        $p_data         = json_decode(json_encode($post), true);
        $param          = json_decode($p_data['data'], true);
        $order_sn       = $param['merchant_order_sn'];
        $total_fee      = $param['total_fee']*1;
        $time           = $_SERVER['REQUEST_TIME'];
        if ($p_data['result_code'] == 200 && $order_sn && $total_fee) {
            // 记录返回的数据
            M('pay_return_data')->add(['data' => json_encode($post), 'ctime' => $time]);

            $order = M('pay_order')->where(['order_sn' => $order_sn, 'status' => 1])->find();
            if ($order && $order['total_fee']*1 == $total_fee) {
                $user = M('user')->where(['id' => $order['userid']])->find();
                if ($user) {
                    // 记录充值表
                    $ins_data = [
                        'userid'    => $order['userid'],
                        'order_sn'  => $order['order_sn'],
                        'trade_sn'  => $order['trade_sn'],
                        'total_fee' => $total_fee,
                        'golds'     => $order['golds'],
                        'amount'    => $user['money'] + $total_fee,
                        'from'      => 3,
                        'ctime'     => $time,
                    ];
                    M('pay_record')->add($ins_data);

                    // 订单状态修改
                    M('pay_order')->where(['id' => $order['id']])->save(['status' => 2, 'uptime' => $time]);

                    // 用户余额增加
                    M('user')->where(['id' => $user['id']])->setInc('money', $total_fee);
                    M('user')->where(['id' => $user['id']])->setInc('count_money', $total_fee);

                    echo 'success1';

                } else {
                    echo 'error2';
                }
            } else {
                echo 'error3';
            }
        } else {
            echo 'sucess';
        }
        
    }


}

?>