<?php
namespace Api\Controller;
use Think\Controller;


class ApiController extends Controller {


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
        //开发者使用登陆凭证 code 获取 session_key 和 openid
        $APPID = 'wx1234d2031a772642';//自己配置
        $AppSecret = '15a280992dba65df7986bed3b168ebef';//自己配置
        $code = $_POST['code'];
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
        
        // var_dump($signature2);exit;
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
          /**
             * 7.生成第三方3rd_session，用于第三方服务器和小程序之间做登录态校验。为了保证安全性，3rd_session应该满足：
             * a.长度足够长。建议有2^128种组合，即长度为16B
             * b.避免使用srand（当前时间）然后rand()的方法，而是采用操作系统提供的真正随机数机制，比如Linux下面读取/dev/urandom设备
             * c.设置一定有效时间，对于过期的3rd_session视为不合法
             *
             * 以 $session3rd 为key，sessionKey+openId为value，写入memcached
        */
        $user_info = json_decode($data,true);
        $rand = rand();
        $session3rd = md5($rand);
        $user_info['session3rd'] = $session3rd;
        $sessionkey = array($session_key,$openid);
        session($session3rd,$sessionkey);//存入session


        $user = M('user')->where(array('openid'=>$user_info['openId']))->find();
    
        $time = date('Y-m-d H:i:s',time());


        // var_dump($_SESSION);exit;
        // if (session('wechat_info.openid')) {
           
            if($user){//老用户
      
                $user_info['avatarUrl'] = $user['headimg'];
                $user_info['money'] = $user['money2'];
                $user_info['id'] = $user['id'];
                $login_time = strtotime($user['last_login_time']);// 
                $time = time() - $login_time;     
                $times = $time / 3600; 
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
                        'create_time' => NOW_TIME
                    ));
                }

                $user_data['type'] = 2;
                $info = M('user')->add($user_data);
                        
            }
            if ($user_info) {
                // S('name',$value);
                S('user_info', $user_info,3000);//用户信息存入Redis
            }

        echo  json_encode(['status'=>'1','msg'=>'返回成功','data'=>$user_info]);
       
     
        
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
     * [getUserId description]
     * @Author   佳民
     * @DateTime 2019-02-21
     * @Function [获取用户ID]
     * @return   [type]     [description]
     */
    public function getUserId(){
        $user_info = S('user_info');
        if($user_info){
            $uid = $user_info['id'];
            return $uid;
        }else{

            redirect(U('Api/login'));
        }
       
    }
     /**----------------  历史战绩部分start    ---------------------**/
  public function record(){
     $uid = 76;


      $data=M('play_log')->where(array('user_id'=>$uid))->select();
      $auname = M('user')->where(array('id'=>$uid))->getField('nickname');
      $game_id = array_column($data,'game_id');
      $a = count($game_id);
      $result=array();

     foreach ($game_id as $key => $value) {
        // var_dump($value);exit;
       $play=array();
        $data1 = M('play_log')->where(array('game_id'=>$value))->select();
        $buname = M('user')->where(array('id'=>$data1[1]['user_id']))->getField('nickname');
        // var_dump($buname);exit;
        $play = array(
            'auname' => $auname,
            'buname' => $buname,
            'result' => $data1[0]['result'],
            'end_time' => $data1[0]['end_time']
        );
       
        $result[] = $play;

        // echo  json_encode($play);
     }
     $record = array_column($result, 'end_time'); 

     array_multisort($record, SORT_ASC, $result); 

      // var_dump($result);
    // echo   json_encode(array('data'=>$result));
        return array('record'=>$result);
      // echo "str $result ;
    
  }
  /**
   * [userInfo description]
   * @Author   佳民
   * @DateTime 2019-02-22
   * @Function [用户信息及历史战绩数据]
   * @return   [type]     [description]
   */
  public function userInfo(){
     $uid = 76; 
     // $uid = $this->getUserId();
     $userInfo = M('user')->where(array('id'=>$uid))->field('id,nickname,money2,headimg,integration')->find();
     $scene = M('play_log')->where(array('user_id'=>$uid))->count();
     $win =   M('play_log')->where(array('user_id'=>$uid,'result'=>'赢'))->count();
     // var_dump($win);exit;
     $probability =round($win/$scene*100,2)."%";

     $grade = $this->grade($win);
     $info = array(
        'infos'=>array(
            'id'    => $userInfo['id'] ,
            'nickname' => $userInfo['nickname'],
            'money2' => $userInfo['money2'],
            'headimg' => $userInfo['headimg'],
            'integration' => $userInfo['integration'],
            'grdae' => $grade,
            'probability' =>$probability,
        ),
     );
     $record = $this->record();
     $result = $info+$record;
     echo json_encode($result);
     // var_dump($results);
  }


 /**----------------  历史战绩部分end    ---------------------**/
 /**
  * [duan description]
  * @Author   佳民
  * @DateTime 2019-02-22
  * @Function [评级判断]
  * @param    [type]     $integrl [description]
  * @return   [type]              [description]
  */
    public function grade($win){
        if(0 <= $win&$win <= 5){
            return '青铜';
        }elseif (6 <= $win&$win <= 10){
            return '白银';
        }elseif (11 <= $win&$win <= 20){
            return '黄金';
        }elseif (21 <= $win&$win <= 30){
            return '白金';
        }elseif (31 <= $win&$win <= 40){
            return '钻石';
        }else{
            return '大师';
        }
    }

    // public function slimeRule(){
    //     $uid = $this->getUserId();
    //     $play_num = M('play_log')->where(array('uid'=>$uid,'status'=>2))->count();
    //     // if($play_num ){

    //     // }
    //     var_dump($play_num);exit;
    // }   





    public function sign(){
   
        $this->display();
    }
     /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [查询领取记录]
     * @return   [type]     [description]
     */
     public function receive(){
      $uid = $this->user['id'];
       $wintegration   = M('user')->where(array('id'=>$uid))->getField('wintegration');               //有无待领取积分
       $pending_amount = M('account_info')->where(array('uid'=>$uid))->getField('pending_amount');    //有无待领取佣金
       $receive        = array(
            'wintegration'   => $wintegration,
            'pending_amount' => $pending_amount,
       );
       if($wintegration != 0 && $pending_amount != 0){
            echo json_encode(array('status'=>1,'msg'=>'有待领取积分和佣金','data'=>$receive));
       }else if($wintegration != 0 && $pending_amount == 0 ){
            echo json_encode(array('status'=>2,'msg'=>'有待领取积分','data'=>$wintegration));
       }else if($pending_amount != 0 && $wintegration == 0 ){
            echo json_encode(array('status'=>3,'msg'=>'有待领取佣金','data'=>$pending_amount));
       }else{
            echo json_encode(array('status'=>4,'msg'=>'无待领取积分和佣金'));
       }
     }
    /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [今日签到记录判断]
     * @return   [type]     [description]
     */
    public function signYn($uid){
       $dateStr = date('Y-m-d', time());
       $timestamp0 = strtotime($dateStr); //当日0点的时间
       $timestamp24 = strtotime($dateStr) + 86400;   //当日24点的时间

       return M('user_sign')->where(array('uid'=>$uid,'sign_time'=>array('between',array($timestamp0,$timestamp24))))->find();
       
    }

    /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [签到,领取积分]
     * @return   [type]     [description]
     */
    public function signs(){
   
       $uid = $this->user['id'];
      // $uid = 76;
        if(IS_POST){
            $type = $_POST['type'];
            // var_dump($type);exit;
            if($type == 1){
                $signYn = $this->signYn($uid);//今天是否签到
                if($signYn){
                     echo json_encode(array('status'=>2,'msg'=>'今日已签到'));
                }else{
                    $data=array(
                        'is_sign'   => 1,
                        'sign_time' =>strtotime(date('Y-m-d H:i:s',time())),
                        'uid'       =>$uid,
                    );
            
                    $sign = M('user_sign')->add($data);
                    if($sign){
                        M('user')->where(array('id'=>$uid))->setInc('wintegration',3);//修改用户积分

                        echo json_encode(array('status'=>1,'msg'=>'签到成功'));
                    }else{
                        echo json_encode(array('status'=>3,'msg'=>'签到失败'));
                    }
                }
            }else if($type == 2){
                $wintegration = M('user')->where(array('id'=>$uid))->getField('wintegration');
                if($wintegration == 0){

                    echo json_encode(array('status'=>-1,'msg'=>'无积分可领取'));
                }else{
                    $data = M('user')->where(array('id'=>$uid))->setInc('integration',$wintegration);
                    if($data){
                        $datas = M('user')->where(array('id'=>$uid))->setDec('wintegration',$wintegration);
                        echo json_encode(array('status'=>1,'msg'=>'领取成功'));
                    }
                }
            }else if($type == 3){
                $pending_amount = M('account_info')->where(array('uid'=>$uid))->getField('pending_amount');

                if($pending_amount == 0){

                    echo json_encode(array('status'=>-1,'msg'=>'无佣金可领取'));
                }else{
                    $data = M('account_info')->where(array('uid'=>$uid))->setInc('received_amount',$pending_amount);
                     // var_dump($data);exit;
                    if($data){
                        $time = date('Y-m-d H:i:s',time());
                        // var_dump($time);exit;
                        M('account_info')->where(array('uid'=>$uid))->setDec('pending_amount',$pending_amount);
                        M('account_info')->where(array('uid'=>$uid))->setField('last_receive_time',$time);
                        echo json_encode(array('status'=>1,'msg'=>'领取成功'));
                    }
                }
            }else{
                echo json_encode(array('status'=>0,'msg'=>'系统错误'));
            }
        }


    }
  


    /**
     * [startGame description]
     * @Author   佳民
     * @DateTime 2019-01-22
     * @Function [单人游戏]
     * @return   [type]     [description]
    */
    public function singleGame(){
         $uid = $this->user['id'];
        if(IS_POST){
            
            $status = $_POST['status'];//1位开始游戏2为结束游戏
                if($type == 1){//单人游戏   
                    if($status == 1){//游戏开始
                        $data = array(
                            'uid' => 76,
                            'start_time' => $_POST['start_time'],//游戏开始时间
                            'mark' => $_POST['mark'],//每局游戏的唯一标志
                        );
                $game = M('singleplay_log')->where(array('mark'=>$_POST['mark']))->find();
                if($game){
                    echo json_encode(array('status'=>-1,'msg'=>'该局游戏已存在'));exit;
                    exit;
                }else{

                  $addlog = M('singleplay_log')->add($data);
                  if($addlog){
                    echo json_encode(array('status'=>1,'msg'=>'新增游戏记录成功'));exit;
                  }
                }
                        
                    }else if ($status == 2) {
                        $datas = array(
                            // 'uid'      => $uid,
                            'result'   => $_POST['result'],//游戏结果
                            'end_time' => strtotime(date('Y-m-d H:i:s',$_POST['end_time'])),//游戏结束时间
                            // 'map'     => $_POST['map'],
                        );
                        $savelog = M('singleplay_log')->where(array('mark'=>$_POST['mark']))->save($datas);
                        if($savelog){
                            echo json_encode(array('status'=>2,'msg'=>'修改游戏记录成功'));exit;
                        }
                    }else{
                         echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
                    }
            }              
      }else{
             echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
             exit;
        
        }
    }
        /**
     * [Commission description]
     * @Author   佳民
     * @DateTime 2019-01-28
     * @Function [佣金分配]
     */
    public function Commission(){
      // $uid = $this->user['id'];
      $uid = 75;

      $introducer = M('play_log')->where(array('user_id'=>$uid,'game_id'=>666,'result'=>'赢'))->field(array('introducer_id','introducer2_id','introducer3_id'))->find();
      // var_dump($introducer);exit;
      $rate = array(
          'introducer_money' => 0.1,
          'introducer2_money' => 0.07,
          'introducer3_money' => 0.03,
      );
      $newdata = array_combine($introducer,$rate);
      // var_dump($newdata);exit;
       foreach ($newdata as $k => $v){
            $money = 100*$v;
// var_dump($money);exit;
          M('user')->where(array('id'=>$k))->setInc('money',$money);//添加用户游戏数据

        }
    }


    /**
     * [multiGame description]
     * @Author   佳民
     * @DateTime 2019-01-27
     * @Function [1VS1竞技]
     * @return   [type]     [description]
     */
    public function multiGame(){
      if(IS_POST){

          $status = $_POST['status'];
          $uids = $_POST['uid'];
          $num = count($uids);
        if($status == 1){
          
            $game = M('play_log')->where(array('game_id'=>$_POST['game_id']))->select();
            if($game){
                echo json_encode(array('status'=>-1,'msg'=>'该局游戏已存在'));exit;
                exit;
            }else{
                
                for ($i=0; $i<$num ; $i++) { 
                   
                  $data = array(
                    'user_id' => $_POST['uid'][$i],
                    'start_time' => $_POST['start_time'],//游戏开始时间
                    'game_id' => $_POST['game_id'],//每局游戏的唯一标志
                  );

                   // var_dump($data);
                   $addlog = M('play_log')->add($data);//添加用户游戏数据

                }
               
                $game =array(
                    'auser_id' => $uids[0],
                    'buser_id' => $uids[1],
                    'game_id' => $_POST['game_id'],
                    // 'start_time' => $_POST['start_time'],
                );
                 $addgamelog = M('game_log')->add($game);//添加游戏详细数据
                if($addlog && $addgamelog){
                  echo json_encode(array('status'=>1,'msg'=>'新增游戏记录成功'));
                }
            }
          
        }else if ($status == 2) {
       

          $results = $_POST['result'];
          $a = array_combine($_POST['uid'], $_POST['result']);

          foreach ($a as $k=>$v){

             $datas = array(
                  'result' => $v,
                  'end_time' => $_POST['end_time'],//游戏开始时间
                  'game_id' => $_POST['game_id'],//每局游戏的唯一标志
                );
             // var_dump($datas);exit;
              $savelog = M('play_log')->where(array('user_id'=>$k,'game_id'=>$_POST['game_id']))->save($datas);

          } 
          $games =array(
                  'auser_id' => $uids[0],
                  'auser_step' => $results[0],
                  'auser_score' => $_POST['auser_score'],
                  'buser_id' => $uids[1],
                  'buser_step' => $results[1],
                  'buser_score' => $_POST['b_userscore'],
                  'game_id' => $_POST['game_id'],
                  'end_time' => $_POST['end_time'],
          );
          $gamelog = M('game_log')->where(array('game_id'=>$_POST['game_id']))->save($games);
          // var_dump($gamelog);
          if($savelog && $gamelog){

                echo json_encode(array('status'=>2,'msg'=>'修改游戏记录成功'));exit;
          }else{

                echo json_encode(array('status'=>-2,'msg'=>'未修改任何数据'));exit;
          }
        }else{

             echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
        }
      }else{

             echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
      }
    }

  /**----------------  挑战书部分start    ---------------------**/

  /**
   * [challenge description]
   * @Author   佳民
   * @DateTime 2019-01-22
   * @Function [挑战书详情]
   * @return   [type]     [description]
   */
  public function challengeInfo(){
        $challenge = M('challenge_info')->where(array('id'=>$_POST['cid']))->find();
        echo json_encode(array('status'=>1,'msg'=>'获取成功','data'=>$challenge));
  } 
  /**
   * [addChallenge description]
   * @Author   佳民
   * @DateTime 2019-01-23
   * @Function [发布挑战书]
   */
  public function addChallenge(){
    $uid = 76;
    
    $money = M('user')->where(array('id'=>$uid))->getField('money');
    if(IS_POST){ 
        if($_POST['deposit'] < $money){
            echo json_encode(array('status'=>-1,'msg'=>'用户余额不足,请先充值'));

        }else{
           echo "待定";
        }
    }
  }
  /**
   * [challenge description]
   * @Author   佳民
   * @DateTime 2019-01-23
   * @Function [发起挑战]
   * @return   [type]     [description]
   */
  public function challenge(){
      $uid = 76;
    
      $money = M('user')->where(array('id'=>$uid))->getField('money');//用户余额
      if(IS_POST){ 
          if($_POST['deposit'] < $money){
              echo json_encode(array('status'=>-1,'msg'=>'用户余额不足,请先充值'));

          }else{
              echo "待定";
          }
      }
  }

  /**----------------  挑战书部分end    ---------------------**/


  /**----------------  俱乐部部分start    ---------------------**/
    /**
     * [clubs description]
     * @Author   佳民
     * @DateTime 2019-01-23
     * @Function [推荐俱乐部]
     * @return   [type]     [description]
     */
    public function clubs(){
        $club = M('club_info')->order('')->select();
    }
    /**
     * [createClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [创建俱乐部]
     * @return   [type]     [description]
     */
    public function createClub(){
       $uid = 76;
       $subordinate =  M('user')->where(array('parent1'=>$uid))->count();
       $active_point = M('user')->where(array('id'=>$uid))->getField('active_point');
       $username = M('user')->where(array('id'=>$uid))->getField('nickname');

       if(IS_POST){
          if($subordinate < 1 || $active_point <100){
              echo json_encode(array('status'=>-1,'msg'=>'对不起,暂无资格创建俱乐部'));
          }else{
              $data = array(
                  'ower_id'     => $uid,
                  'ower_name'   => $username,
                  'tel'         => $_POST['tel'],
                  'club_name'   => $_POST['club_name'],
                  'declaration' => $_POST['declaration'],
                  'area'        => $_POST['area'],
                  'create_fee'  => $_POST['create_fee'],
                  'create_number'  => $_POST['create_number'],
              ); 
              $club_name = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
              if($club_name){
                 echo json_encode(array('status'=>-2,'msg'=>'该俱乐部已存在'));exit;
              }else{
                $result = M('club_info')->add($data);
                if($result){
                  $club = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
                  // $data = array(
                  //   ''
                  //  );
                  M('user')->where(array('id'=>$uid))->save($data);
                  echo json_encode(array('status'=>1,'msg'=>'创建俱乐部成功'));exit;
                }
              }
              
          }
       }
    }
    /**
     * [fingClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [查找俱乐部]
     * @return   [type]     [description]
     */
    public function fingClub(){
        if(IS_POST){
            $aWhere['name'] = array('like','%'.$_POST['keyword'].'%');
            $data =  M('club_info')->where($aWhere)->getField('id,name,ower_name');
            if($data){
                echo json_encode(array('status'=>1,'msg'=>'搜索成功','data'=>$data));
            }
        }
    }
    /**
     * [joinClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [加入俱乐部]
     * @return   [type]     [description]
     */
    public function joinClub(){
        $uid = 76;
        if(IS_POST){
          $number = M('club_info')->where(array('id'=>$_POST['club_id']))->find();
          // var_dump($number);exit;
          if($number['create_number'] == $number['club_number']){
            echo json_encode(array('status'=>-1,'msg'=>'人数已达上限'));exit;
          }
          // else{

          // }
        } 
    }
    /**
     * [clubMembers description]
     * @Author   佳民
     * @DateTime 2019-01-25
     * @Function [俱乐部成员列表]
     * @return   [type]     [description]
     */
    public function  clubMembers(){
    
        $uid = 76;
        $club_id = M('user')->where(array('id'=>$uid))->getField('club_id');

        $clubMember = M('user')->where(array('club_id'=>$club_id))->select();
        $number = M('user')->where(array('club_id'=>$club_id))->count();
        array_push($clubMember, $number);
        
        echo json_encode($clubMember);
       
    }


  /**----------------  俱乐部部分end    ---------------------**/




 /**----------------  好友部分start    ---------------------**/

  /**
      * [addFriend description]
      * @Author   佳民
      * @DateTime 2019-01-16
      * @Function [查找好友]
      */
    public function findFriend(){
        if(IS_POST){
            if(is_numeric($_POST['keyword'])){
                $aWhere['mobile'] = array('like','%'.$_POST['keyword'].'%');
           
            }else{
            
                $aWhere['nickname'] = array('like','%'.$_POST['keyword'].'%');
            }

        }
        $data =  M('user')->where($aWhere)->getField('id,nickname,mobile');
       
        echo json_encode(array('data'=>$data));
    }
     /**
      * [addFriend description]
      * @Author   佳民
      * @DateTime 2019-01-16
      * @Function [添加好友]
      */
    public function addFriend(){
        
        if(IS_POST){
             $sname = M('user')->where(array('id'=>$_POST['fid']))->getField('nickname');
             $data['uid'] = $_POST['uid'];//被申请人ID
             $data['sid'] = $_POST['fid'];//申请人ID
             $data['text'] = $_POST['text'];//申请说明
             $data['stime'] = strtotime(date('Y-m-d H:i:s',time()));
             $data['status'] = 1;
             // $data['sname']  = $sname;
             $addFriend = M('apply')->add($data);
             if($addFriend){
                echo json_encode(array('status'=>1,'msg'=>'等待验证'));
             }
        }
    }
    /**
     * [applyList description]
     * @Author   佳民
     * @DateTime 2019-01-16
     * @Function [申请列表]
     * @return   [type]     [description]
     */
    public function applyList(){
        $uid = $this->user['id'];
        $apply=M('apply')->alias('a')
                        ->join("dd_user u on a.sid=u.id") //附表连主表
                        ->field("u.nickname,a.status,a.sid,a.stime")
                        ->where(array('uid'=>$uid,'status'=>1))//需要显示的字段
                        ->select();

        echo json_encode(array('status'=>1,'msg'=>'获取成功','data'=>$apply)); 
    }
    
    /**
     * [applyList description]
     * @Author   佳民
     * @DateTime 2019-01-16
     * @Function [同意好友申请]
     * @return   [type]     [description]
     */

    public function agree(){
        $uid = $this->user['id'];
        if(IS_POST){
            
             $addFriend =  M('apply')->where(array('id'=>$_POST['aid'],'uid'=>$uid))->setField('status',2);
             if($addFriend){
                echo json_encode(array('status'=>1,'msg'=>'添加成功'));
             }
        }
    }


    /********      好友部分结束  end   ********/





    /********      史莱姆部分  start   ********/
    // public function Slime(){
      
    // }

    /********      史莱姆部分  end   ********/





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








}