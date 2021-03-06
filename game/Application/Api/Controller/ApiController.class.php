<?php
namespace Api\Controller;
use Api\Service\UserService;
use Api\Service\ActivityService;
use Think\Controller;
class ApiController extends Controller {
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

        $this->_load_config();
        $APPID = $this->_bei_mp['appid'];
        $AppSecret = $this->_bei_mp['appsecret'];
      
        $code = $_POST['code'];

        if($code == null ){
           echo json_encode(['status'=>'-2','msg'=>'code不能为空']);
           exit;
        }
        $signature = $_POST['signature'];
        $rawData   = $_POST['rawData'];
        $iv        = $_POST['iv'];
        $uid       = $_GET['uid'];//推荐人用户ID
        $introduceType = $_GET['source'];

        $encryptedData = $_POST['encryptedData'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = $this->vget($url);  // 一个使用curl实现的get方法请求
        
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


        if($user_info['city']){
          $area = $user_info['city'];
        }else if($user_info['country']){
          $area = $user_info['country'];
        }else{
          $area = '中国';
        }

        $session3rd = $this->randomFromDev(16);
        $user_info['session3rd'] = $session3rd;
        $userService = new UserService();
        // $user  = $userService->getUserFullInfoByOpen($user_info['openId']);
        $isNew = $userService->getUserBaseInfoByOpen($user_info['openId']);
        $time = date('Y-m-d H:i:s',time());
        if(!$isNew){//新用户
            
            $user_data['openid']    = $user_info['openId'];
            $user_data['nickname']  = $user_info['nickName'];
            $user_data['headimg']   = $user_info['avatarUrl'];
            $user_data['sex']       = $user_info['gender'];
            $user_data['area']      = $area;
            $user_data['join_time'] = $time;
            $user_data['source'] = $introduceType;//用户引入方式
            
            //获取推荐关系
            if($uid){
               $intro1User =  $userService->getUserBaseInfo($uid);                   
               if($intro1User){
                   $parent2= $intro1User['parent1'];
                   $parent3= $intro1User['parent2'];
               }
                $user_data['parent1'] = $uid;
                $user_data['parent2'] = $parent2;
                $user_data['parent3'] = $parent3;
                M('relation')->add(array(
                    'openid' => $user_data['openid'],
                    'parent_id' => $uid,
                    'create_time' => $time
                ));
            }
            $users = $userService->addUser($user_data,$user_info['openId']);
            $user = $userService->getUserFullInfoByOpen($users['openid']);
            $user['is_new'] = 1;
            $userService->addSlime($user['openid'],$user['id']);
            $userService->addReceive($user['id']);

        }else{
          
            $user = $userService->getUserFullInfoByOpen($user_info['openId']);
            $user['firstLogin'] = $this->firstLogin($user['id']);
            $user['is_new'] = 2;
            $save['area']      = $area;
            $save['nickname']  = $user_info['nickName'];
            $save['headimg']   = $user_info['avatarUrl'];
            $save['last_login_time']      = $time;
            M('user_base')->where(array('openid'=>$user['openid']))->save($save);

        }

        $user['session3rd'] = $session3rd;
        $user['firstLogin'] = $this->firstLogin($user['id']);
        //累计登陆奖励
        $activityService =  new ActivityService();
        $acclogin = $activityService->accuLogin($user['openid'],$user['id']);
        $slime = M('user_slime')->where(array('u_id'=>$user['id'],'is_check'=>1,'is_lock'=>1))->field('u_id,s_id,level,hat')->find();
        $this->write_log();
        $this->loginLog($user['id'],$introduceType,$uid);
        $sessionkey = array($session_key,$openid,$user['id']);
        S($session3rd,$sessionkey,18000);//存入session
        $data =[
            "slime"   =>$slime, 
            "userInfo"=>$user,
            "accLoginActivity"=>$acclogin
        ];
        echo  json_encode(['status'=>'1','msg'=>'返回成功','data'=>$data]);
    }
    //线下
    public function devLogin()
    {
        session_start();
        // 开发者使用登陆凭证 code 获取 session_key 和 openid

        $this->_load_config();
        $APPID = $this->_bei_mp['appid'];
        $AppSecret = $this->_bei_mp['appsecret'];
      
        $code = $_POST['code'];

        if($code == null ){
           echo json_encode(['status'=>'-2','msg'=>'code不能为空']);
           exit;
        }
        $signature = $_POST['signature'];
        $rawData   = $_POST['rawData'];
        $iv        = $_POST['iv'];
        $uid       = $_GET['uid'];//推荐人用户ID
        $introduceType = $_GET['source'];


        $encryptedData = $_POST['encryptedData'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = $this->vget($url);  // 一个使用curl实现的get方法请求
        
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
        

        if($user_info['city']){
          $area = $user_info['city'];
        }else if($user_info['country']){
          $area = $user_info['country'];
        }else{
          $area = '中国';
        }
        
      
        $session3rd = $this->randomFromDev(16);
        $user_info['session3rd'] = $session3rd;
        $userService = new UserService();
        $user  = $userService->getUserFullInfoByOpen($user_info['openId']);
        $isNew = $userService->getUserBaseInfoByOpen($user_info['openId']);

        $user['is_new'] = 2;
        
        $time = date('Y-m-d H:i:s',time());
            if(!$isNew){//新用户
                
                $user_data['openid']    = $user_info['openId'];
                $user_data['nickname']  = $user_info['nickName'];
                $user_data['headimg']   = $user_info['avatarUrl'];
                $user_data['sex']       = $user_info['gender'];
                $user_data['area']      = $area;
                $user_data['join_time'] = $time;
                
                //用户引入方式

                $user_data['source'] = $introduceType;

                //获取推荐关系
                if($uid){

                   $intro1User =  $userService->getUserBaseInfo($uid);
                   
                   if($intro1User){
                       $parent2= $intro1User['parent1'];
                       $parent3= $intro1User['parent2'];
                       // $parent3= M('user_base')->where(array('id'=>$intro1User['parent1']))->getField('parent1');
                   }
                    $user_data['parent1'] = $uid;
                    $user_data['parent2'] = $parent2;
                    $user_data['parent3'] = $parent3;
                   
                    M('relation')->add(array(
                        'openid' => $user_data['openid'],
                        'parent_id' => $uid,
                        'create_time' => $time
                    ));
                }
             
                $users = $userService->addUser($user_data,$user_info['openId']);
                $user = $userService->getUserFullInfoByOpen($users['openid']);
                // array_merge($user,$users)
                $user['is_new'] = 1;
                $userService->addSlime($user['openid'],$user['id']);
                $userService->addReceive($user['id']);
                M('receive')->add($user['id']);
              
            }
            $save['area']      = $area;
            $save['nickname']  = $user_info['nickName'];
            $save['headimg']   = $user_info['avatarUrl'];
            $save['last_login_time']      = $time;
            M('user_base')->where(array('openid'=>$user['openid']))->save($save);
            //接口访问令牌
            $user['session3rd'] = $session3rd;
            $user['firstLogin'] = $this->firstLogin($user['id']);
            $user = $userService->getUserFullInfoByOpen($user_info['openId']);
            //累计登陆奖励
            $activityService =  new ActivityService();
            $acclogin = $activityService->accuLogin($user['openid'],$user['id']);
            $slime = $userService->slimeLevel($user['openid']);
            $this->write_log();
            $this->loginLog($user['id'],$introduceType,$uid);
            $sessionkey = array($session_key,$openid,$user['id']);
            S($session3rd,$sessionkey,18000);//存入session
            $data =[
                "slime"   =>$slime, 
                "userInfo"=>$user,
                "accLoginActivity"=>$acclogin
            ];
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
    public function firstLogin($user_id){
       $start = strtotime(date('Ymd'));
       $end = strtotime(date('Ymd'))+ 86400;

       $isLogin =  M('login_log')->where(array('user_id'=>$user_id,'login_time'=>array('between',array($start,$end))))->find();

       if($isLogin){
          return 2;
       }else{
          return 1;
       }
    }
    public function loginLog($user_id,$souce,$uid){
       $start = strtotime(date('Ymd'));
       $end = strtotime(date('Ymd'))+ 86400;
       $isLogin =  M('login_log')->where(array('user_id'=>$user_id,'login_time'=>array('between',array($start,$end))))->find();
       if($isLogin){
          return '今天已登录';
       }else{
            $data = array(
              'user_id' => $user_id,
              'ip'      => getonlineip(),
              'login_time' => NOW_TIME,
              'souce'      => $souce,
              'invite_id'  => $uid
            );
            M('login_log')->add($data);
        }
    }
   

      //日志写入
    public function write_log(){ 

        $nowUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
 
        $data = json_encode(array(date('Y-m-d H:i:s'),getonlineip(),$nowUrl,getPostData()));
        $years = date('Y-m');
        //设置路径目录信息
        $url = './Public/log/'.$years.'/'.date('Ymd').'_log.txt';  
          // var_dump($url);exit;
        $dir_name=dirname($url);
          //目录不存在就创建
          if(!file_exists($dir_name))
          {
            //iconv防止中文名乱码
           $res = mkdir(iconv("UTF-8", "GBK", $dir_name),0777,true);
          }
          $fp = fopen($url,"a");//打开文件资源通道 不存在则自动创建   
        fwrite($fp,var_export($data,true)."\r\n");//写入文件
        fclose($fp);//关闭资源通道
    }
    // 生成邀请海报
    public function qrcode($uid)
    {  
        if (!$$GLOBALS['current_uid']) {
            $this->error('请登陆后操作！', U('Api/login'));
        }
        
        $path = "./Public/invite/" . date('Y-m') . '/' . $uid . ".png";
        $invite_path = "./Public/invite/" . date('Y-m') . '/' . $uid . "_invite.png";
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, 1);
        }
        if (!file_exists($path)) {
            $url = complete_url(U('Api/login?uid=' . $uid));
            // 生成二维码图片
            if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
            include COMMON_PATH . 'Util/phpqrcode/phpqrcode.php';
            \QRcode::png($url, $path, 'M', 4);
        }
        if (!file_exists($invite_path)) {
          
            // $im_dst = imagecreatefromjpeg("./Public/images/bg.jpg");
            $im_src = imagecreatefrompng($path);
            list($width, $height) = getimagesize($path);
             // 合成二维码（二维码大小282*282)
   
            imagecopyresized( $im_dst, $im_src,210,545, 0, 0, 320, 320, $width, $height);
           
            // 保存
            imagejpeg($im_dst, $invite_path);
        }
      
            header("Content-type: image/jpeg");
            echo file_get_contents($invite_path);
        
    }
  //获取用户推广海报路径
     public  function get_qrcode_path($uid){
          // $this->_load_config();
          $url = $GLOBALS['_CFG']['web_site']['url'];
          $path = './Public/invite/'.date('Y-m').'/';
           return array(
              'path'      => $path,
              'invite'       => $url. substr($path,1).$user.'_invite.png',
          );
      }
}
