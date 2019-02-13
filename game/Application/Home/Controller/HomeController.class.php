<?php
namespace Home\Controller;
use Think\Controller;

class HomeController extends Controller
{
    public function _initialize()
    {
        $uid = I('get.uid', 0);
        if ($_GET['nuid']) {
            session('nuid', $_GET['nuid']);
        }
        //$this->error('系统维护时间,9:00开放');
        $this->_load_config();
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            $this->error('请在微信中打开');
            $this->display('Index/code');
            exit;
        }


        // $this->user = M('user')->find(25);
        // session('openid',$this->user['openid']);
        // var_dump($GLOBALS['_CFG']['bei_mp']);


        /****** 获取OPENID START *****/
        // if (!isMobile()) {
            // $this->error('请在手机微信中打开');
        // }
        // session_destroy();
        // exit();
        $this->gpc_filter();

        // 通过调试模式的地址中的用户id 自动登录
        if (APP_DEBUG && !empty($_GET['debug_user_id'])) {
           // $this->user = M('user')->find($_GET['debug_user_id']);
           // session('user', $this->user);
        } elseif (session('?user')) {           // 从session中获取登陆信息
            $this->user = M('user')->find(session('user.id'));
        }
        $this->openid = session('openid');
        if (!$this->openid && isset($_GET['openid'])) {
            $this->openid = $_GET['openid'];
        }
        if (!empty($this->openid)) {
            $this->user=M('user')->where(array('openid'=>$this->openid))->find();
        }

        // session中有openid
        if (!$this->openid && session('?openid')) {
            /*$this->openid = session('openid');
            if (session('openid')) {
                $user_info = wxuser(session('openid'));
                session('wechat_info', $user_info);
            }*/
        } elseif (!$this->openid  && IS_WECHAT) { // 没登录，没有获取openid且在微信中，则获取openid
            $this->isDomain();          // 调用当前域名是否禁用
            $this->check_user();        // 调用备份公众号的授权
            if (!isset($_GET['code'])) {
                // 网页认证授权
                $custome_url = "http://". $this->_mp['pinless_url'];
                // $custome_url = get_current_url(1);
                $custome_url .= U('Api/wx_login',$_GET);
                $scope = 'snsapi_userinfo';
                $oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->_mp['appid'] . '&redirect_uri=' . urlencode($custome_url) . '&response_type=code&scope=' . $scope . '&state=dragondean#wechat_redirect';
                 echo json_decode(http_curl_get($oauth_url));exit;
                header('Location:' . $oauth_url);
                die();
            }
        }
        /****** 获取OPENID END *****/


        if ($this->openid) {
            if (!M('user')->where(array('openid'=>$this->openid))->count()) {
               session_destroy();
               header('Location:'.U('Index/index'));
               die();
            }
            session('openid', $this->openid);
        }
        if ($this->user) {

            $bopenid = I('bopenid','');
            $user = M('user')->where(array('openid'=>$this->openid))->find();
            $nowtime = date('Y-m-d h:i:s', time());
            $login_num = $user['all_login_time']+1;
            $d_main = $this->ramGameDomian();
            if ($d_main) {
                redirect($d_main . U('Index/index','bopenid='. I('bopenid') .'&openid='. $this->openid));
            }

            if (!empty($bopenid) && !M('user')->where(array('bopenid' => $bopenid))->count() && !empty($this->openid)) {
                M('user')->where(['openid' => $this->openid])->save(array('bopenid' => $bopenid,'last_login_time'=>$nowtime,'all_login_time'=>$login_num));
            }
            if (M('suggest')->where('uid=' . $this->user['id'])->count() || $this->user['is_tong'] > 0) {
                header('Location:'. $this->_web_site['black_url']); 
                exit();
            }

            /*$domain_jump = M('domain')->where(array('is_home' => 1, 'is_qr_code' => 0, 'is_lock' => 0))->order('id DESC')->find(); //主域名
            if ($domain_jump['domain'] == $_SERVER['HTTP_HOST']) {
                $domain_x = D('Domain')->getWhiteDomain();
                $user_data['openid'] = \Think\Crypt::encrypt($this->openid, CashKey, 60);
                $user_data['url'] = \Think\Crypt::encrypt(get_url_with_out_domain(), CashKey, 60);
                redirect('http://' . $domain_x['domain'] . U('Home/Init/auto_login') . '&' . http_build_query($user_data));
                exit();
            }*/

            session('user', $this->user);
        }

        // 调用jssdk
        $dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);

        // 引入生活圈付呗类
        Vendor('live.LiveCurl');

    }

    // 备份公众号拿openid
    public function check_user()
    {
        $bopenid = I('bopenid');
        if (empty($bopenid)) {
            $custome_url = "http://". $this->_bei_mp['pinless_url'];
            // $custome_url = get_current_url(1);
            $custome_url = $custome_url.U('Api/bopenid','uid='.$_GET['uid']);
            $scope = 'snsapi_userinfo';
            $oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->_bei_mp['appid'] . '&redirect_uri=' . urlencode($custome_url) . '&response_type=code&scope=' . $scope . '&state=dragondean#wechat_redirect';
            header('Location:' . $oauth_url);
            die();
        }
    }

    // 加载配置
    protected function _load_config()
    {
        $_CFG = S('sys_config');
        if (empty($_CFG) || APP_DEBUG) {
            $config = M('config')->select();
            if (!is_array($config)) {
                die('请先在后台设置好各参数');
            }
            foreach ($config as $v) {
                $_CFG[$v['name']] = unserialize($v['value']);
            }
            unset($config);
            S('sys_config', $_CFG);
        }
        // 循环将配置写道成员变量
        foreach ($_CFG as $k => $v) {
            $key = '_' . $k;
            $this->$key = $v;
        }
        $this->assign('_CFG', $_CFG); // 指配到模板
        $GLOBALS['_CFG'] = $_CFG;        // 保存到全局变量
    }


    // GPC 预处理
    private function gpc_filter()
    {
        // 过滤id数据
        $_GET = $this->id_filter($_GET);
        if (IS_POST) $_POST = $this->id_filter($_POST);
    }


    // 过滤各种ＩＤ数据
    private function id_filter($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$val) {
                if ('id' == strtolower($key) || !strpos($key, '_id') === false) {
                    $val = intval($val);
                }
            }
            return $data;
        } else {
            return intval($data);
        }
    }


    // 判断当前域名是否禁用
    public function isDomain($url = '')
    {
        $now_url = $_SERVER['HTTP_HOST'];
        if ($url) {
            $now_url = $url;
        }
        
        /*// 判断域名是否禁用
        $h_code  = http_code($now_url);

        if (!$h_code) {
            // 把数据库有的域名 设置黑名单
            $one = M('domain')->where(['domain' => $now_url, 'is_lock' => 0])->count();
            if ($one) {
                M('domain')->where(['domain' => $now_url])->save(['is_lock' => 1]); 
            }

            $this->error('系统维护中');
        }*/
    }


    // 用游戏域名进游戏
    public function ramGameDomian()
    {
        $url = '';
        $d_where = ['is_lock' => 0, 'is_type' => 0, 'domain' => $_SERVER['HTTP_HOST']];
        if (!M('domain')->where($d_where)->count()) {
            unset($d_where['domain']);

            // 设置默认游戏域名先默认，不设置用随机域名
            $d_main = M('domain')->where(['is_home' => 1])->where($d_where)->find();
            if (!$d_main) {
                $d_main = M('domain')->where($d_where)->order('rand()')->find(); //随机游戏域名
            }
            
           $url = 'http://' . $d_main['domain'];
              //$url =$d_main['domain']; 
              
 
       }   
      
         return $url;

    }

}

?>

