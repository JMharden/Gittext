<?php
namespace Api\Controller;
use Think\Controller;


class ApiController extends Controller {
     public function _initialize_bak()
     {
     	 $this->_load_config();
         $token = $_POST['token'];
         if ($token == null || S($token) == null) {
             echo json_encode(['status' => '403', 'message' => 'request forbidden']);
             exit;
         }
        //  var_dump(S($token));
         //exit;
         $uid =S($token)[2];
         $useInfo =S('user_info_'.$uid);
        if(!$useInfo){
             $user  = M('user')->where(array('id'=>$uid))->field('id,nickname,money2,headimg,integration,empiric,active_point')->find();
             $scene = M('play_log')->where(array('user_id'=>$uid))->count();
             $win   = M('play_log')->where(array('user_id'=>$uid,'result'=>'赢'))->count();
             $probability =round($win/$scene*100,2)."%";
             $grade = $this->grade($win);
             $userInfo = array(
                 'id'       => $user['id'] ,
                 'openid'    =>$user['openid'],
                 'club_id'    =>$user['club_id'],
                 'club_role'    =>$user['is_club_owner '],
                 'nickname' => $user['nickname'],
                 'money'   => $user['money'],
                 'headimg'  => $user['headimg'],
                 'empiric'  => $user['empiric'],//经验值
                 'active' => $user['active_point'],//活跃度
                 'inter'    => $user['integration'],//积分
                 'grade'    => $grade,//段位
                 'probability' =>$probability,//胜率
             );
            if(!$userInfo){
                echo json_encode(['status' => '403', 'msg' => 'userInfo not find']);
                exit;
            }
            //后面有接口要取用户信息（推荐关系啥的）直接从缓存里拿就行
            S('user_info_'.$uid, $userInfo,18000);//用户信息存入Redis
            $GLOBALS['current_use_info'] =$userInfo;
        }
         $GLOBALS['current_uid'] =$uid;

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

/*
     *登录（调用wx.login获取）
     * @param $code string
     * @param $rawData string
     * @param $signatrue string
     * @param $encryptedData string
     * @param $iv string
     * @return $code 成功码
     * @return $session3rd  第三方3rd_session
     * @return $data  用户数据
 */
    public function login()
    {
        session_start();
        // 开发者使用登陆凭证 code 获取 session_key 和 openid
        // $APPID = 'wx1234d2031a772642';//自己配置
        // $AppSecret = '15a280992dba65df7986bed3b168ebef';//自己配置
        $this->_load_config();
        $APPID = $this->_mp['appid'];
        $AppSecret = $this->_mp['appsecret'];
        // var_dump($APPID);exit;
        $code = $_POST['code'];
        session('code',$code);
        if($code == null ){
           echo json_encode(['status'=>'-2','msg'=>'code不能为空']);
           exit;
        }

        $signature = $_POST['signature'];
        $rawData   = $_POST['rawData'];
        $iv        = $_POST['iv'];
        $uid       = $_GET['uid'];//推荐人用户ID
        // var_dump($uid);exit;
        $encryptedData = $_POST['encryptedData'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = $this->vget($url);  // 一个使用curl实现的get方法请求
        // var_dump($arr);exit;
        $arr = json_decode($arr, true);
        
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];
        // 数据签名校验
       
        $signature2 = sha1($rawData . $session_key);//签名密钥
        
        if ($signature != $signature2) {
            echo json_encode(['status'=>'0', 'msg' => '数据签名验证失败！']);exit;
        }
        Vendor("PHP.wxBizDataCrypt");  //加载解密文件，在官方有下载
        
        $pc = new \WXBizDataCrypt($APPID, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);  //其中$data包含用户的所有数据
        if ($errCode !== 0) {
           echo json_encode(['status'=>'-1','msg'=>'数据为空']);
           exit;
        }

        // 7.生成第三方3rd_session，以 $session3rd 为key，sessionKey+openId为value，写入memcached
        
        $user_info = json_decode($data,true);
       
        $session3rd = $this->randomFromDev(16);
        
        $user_info['session3rd'] = $session3rd;

        $user = M('user')->where(array('openid'=>$user_info['openId']))->find();
        $time = date('Y-m-d H:i:s',time());
        // if (session('wechat_info.openid')) {
           
            if($user){//老用户
                $user_info['club_id'] = (int)$user['club_id'];
                $user_info['avatarUrl'] = $user['headimg'];
                $user_info['active_point'] = (int)$user['active_point'];
                $user_info['money'] = $user['money'];
                $user_info['id'] = (int)$user['id'];
                $loginNum = $user['all_login_time']+1;
                M('user')->where(array('openid'=>$user_info['openId']))->save(array('last_login_time'=>$time,'all_login_time'=>$loginNum));

            }else{//新用户

                $user_data['openid']    = $user_info['openId'];
                $user_data['nickname']  = $user_info['nickName'];
                $user_data['headimg']   = $user_info['avatarUrl'];
                $user_data['sub_time']  = time();
                $user_data['join_time'] = $time;
                $user_data['last_login_time'] = $time;
                // var_dump($user_info);exit;
                //获取推荐关系
                if($uid){
                    $introducer_2id = M('user')->where(array('id'=>$uid))->getField('introducer_id');//推荐人的上级用户ID
                    $introducer_3id = M('user')->where(array('id'=>$introducer_2id))->getField('introducer_id');//推荐人的上级用户ID
                    $user_data['introducer_id'] = $uid;
                    $user_data['introducer_2id'] = $introducer_2id;
                    $user_data['introducer_3id'] = $introducer_3id;
               
                    M('relation')->add(array(
                        'openid' => $user_data['openid'],
                        'parent_id' => $uid,
                        'create_time' => $time
                    ));
                }

                $user_data['type'] = 2;
                $info = M('user')->add($user_data);
                        
            }
            $uinfo = M('user')->where(array('openid'=>$user_info['openId']))->find();
            // var_dump($user);exit;
        //累计登陆奖励
        $activityService =  new ActivityService();
        $acclogin = $activityService->accuLogin($uinfo['id']);
            if ($uinfo) {

                $sessionkey = array($session_key,$openid,$user['id']);
                S($session3rd,$sessionkey,18000);//存入session
                S('user_info_'.$user['id'], $user_info,18000);//用户信息存入Redis
            }
          $data =[
              "userInfo"=>$user_info,
              "accLoginActivity"=>$acclogin
          ]  ;
          echo  json_encode(['status'=>'1','msg'=>'返回成功','data'=>$data]);
    }

    public function vget($url){
        $info=curl_init();
        curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_NOBODY,0);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($info,CURLOPT_URL,$url);
        $output= curl_exec($info);
        curl_close($info);
        return $output;
    }

    /**
     * 读取/dev/urandom获取随机数
     * @param $len
     * @return mixed|string
     */
    public function randomFromDev($len) {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }
        else
        {
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');

        return substr($result, 0, $len);
    }



}