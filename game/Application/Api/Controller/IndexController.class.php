<?php

namespace Api\Controller;

use Api\Service\UserService;
use Think\Controller;

/**
 * Class IndexController
 * @package Api\Controller把需要接口权限校验的都单独放到一个controller 和login分开
 */
class IndexController extends ApiController
{
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
        $userService = new UserService();
       $userInfo =  $userService->getUserBaseInfo($uid);
        if(!$userInfo){
            echo json_encode(['status' => '403', 'msg' => 'userInfo not find']);
            exit;
        }
         $GLOBALS['current_use_info'] =$userInfo;
          $GLOBALS['current_uid'] =$uid;

    }
 
    /**----------------  历史战绩部分start    ---------------------**/
    /**
     * [record description]
     * @Author   佳民
     * @DateTime 2019-02-26
     * @Function [历史战绩]
     * @return   [JSON]     [description]
     */
    public function record(){
    	
         // $uid =session($_GET['code'])[2];
        $uid =S($token)[2];
        // $start = $_GET['start'];
        // $limit = $_GET['limit'];
        //根据gameid，type分类查询的可以后面做
        $data=M('game_log')->where(array('auser_id'=>182))->select();

        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
    }

    /**
     * [userInfo description]
     * @Author   佳民
     * @DateTime 2019-02-26
     * @Function [用户信息]
     * @return   [JSON]     [description]
     */
    public function userInfo(){
   		
        //这部分信息可以直接放缓存，从缓存里拿就行
        $uid =S($_POST['token'])[2];
        // var_dump($uid);exit;
        $info = S('user_info_'.$uid)[2];

        // $userInfo = M('user')->where(array('id'=>$uid))->field('id,nickname,money2,headimg,integration')->find();
        // $scene = M('play_log')->where(array('user_id'=>$uid))->count();
        // $win =   M('play_log')->where(array('user_id'=>$uid,'result'=>'赢'))->count();
        // $probability =round($win/$scene*100,2)."%";

        // $grade = $this->grade($win);
        // $info = array(
        //     'id'    => $userInfo['id'] ,
        //     'nickname' => $userInfo['nickname'],
        //     'money2' => $userInfo['money2'],
        //     'headimg' => $userInfo['headimg'],
        //     'integration' => $userInfo['integration'],//积分
        //     'grade' => $grade,//段位
        //     'probability' =>$probability,//胜率
        // );
       
        // var_dump($info);exit;
        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data'=>$info]);

    }






    /**
     * [Commission description]
     * @Author   佳民
     * @DateTime 2019-01-28
     * @Function [佣金分配]
     */
    public function Commission(){
      $uid =S($_POST['token'])[2];
      // $uid = 75;

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
          M('user')->where(array('id'=>$k))->setInc('money',$money);//添加用户游戏数据

        }
    }


    /**
     * [startGame description]
     * @Author   佳民
     * @DateTime 2019-01-22
     * @Function [单人游戏]
     * @return   [JSON]     [description]
    */
    public function singleGame(){
        // $uid =S($_POST['token'])[2];
        $uid = 183;
        if(IS_POST){
            
            $status = $_POST['status'];//1位开始游戏2为结束游戏
              // if($type == 1){//单人游戏   
            if($status == 1){//游戏开始
                $data = array(
                    'uid' => $uid,
                    'start_time' => $_POST['start_time'],//游戏开始时间
                    'mark' => $_POST['mark'],//每局游戏的唯一标志
                    'type' => $_POST['type'],
                );
              $game = M('singleplay_log')->where(array('mark'=>$_POST['mark']))->find();
              if($game){
                  echo json_encode(array('status'=>-1,'msg'=>'该局游戏已存在'));exit;
                 
              }else{

                $addlog = M('singleplay_log')->add($data);
                if($addlog){
                  echo json_encode(array('status'=>1,'msg'=>'新增游戏记录成功'));exit;
                }
              }    
            }else if ($status == 2) {
                $datas = array(
                    'uid'      => $uid,
                    'result'   => $_POST['result'],//游戏结果
                    'end_time' => strtotime(date('Y-m-d H:i:s',$_POST['end_time'])),//游戏结束时间
                    'map'     => $_POST['map'],
                    'rate'    =>  implode(',', $_POST['rate']),

                );
                $savelog = M('singleplay_log')->where(array('mark'=>$_POST['mark']))->save($datas);
                if($savelog){
                    echo json_encode(array('status'=>2,'msg'=>'修改游戏记录成功'));exit;
                }
            }else{
                 echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
            }
            // }              
      }else{
          echo json_encode(array('status'=>0,'msg'=>'系统错误'));exit;
      }
    }



    /**
     * [multiGame description]
     * @Author   佳民
     * @DateTime 2019-01-27
     * @Function [1VS1竞技]
     * @return   [JSON]     [description]
     */
    public function multiGame(){
      // $uid =S($_POST['token'])[2];
      // echo "123";
      if(IS_POST){

          $status = $_POST['status'];
          $uids = $_POST['uid'];
          $results = $_POST['result'];
          $num = count($uids);
        if($status == 1){//开始游戏
          
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
                    'ticket'  =>$_POST['ticket'], 
                    'type' => $_POST['type'],
                  );

                   // var_dump($data);
                   $addlog = M('play_log')->add($data);//添加用户游戏数据

                }
                // $auname = M('user')->where(array('id'=>$uids[0]))->getField('nickname');
                // $buname = M('user')->where(array('id'=>$uids[1]))->getField('nickname');
                // $game =array(
                //     'auser_id' => $uids[0],
                //     'buser_id' => $uids[1],
                //     'game_id'  => $_POST['game_id'],
                //     'a_uname'  => $auname,
                   
                // );
                //  $addgamelog = M('game_log')->add($game);//添加游戏详细数据
                if($addlog){
                  echo json_encode(array('status'=>1,'msg'=>'新增游戏记录成功'));
                }
            }
          
        }else if ($status == 2) {//结束游戏
       

          // $results = $_POST['result'];
          // var_dump(json_encode($_POST['result']));exit;
          $a = array_combine($_POST['uid'], $_POST['result']);
          // var_dump($a);exit;
          foreach ($a as $k=>$v){
            // var_dump($v);exit;
             $datas = array(
                  'result' => serialize($v),
                  'end_time' => $_POST['end_time'],//游戏开始时间
                  'game_id' => $_POST['game_id'],//每局游戏的唯一标志
                );
             // var_dump($datas);exit;
              $savelog = M('play_log')->where(array('user_id'=>$k,'game_id'=>$_POST['game_id']))->save($datas);

          } 
          // $games =array(
          //         'auser_id' => $uids[0],
          //         'auser_step' => $results[0],
          //         'auser_score' => $_POST['auser_score'],
          //         'buser_id' => $uids[1],
          //         'buser_step' => $results[1],
          //         'buser_score' => $_POST['b_userscore'],
          //         'game_id' => $_POST['game_id'],
          //         'end_time' => $_POST['end_time'],
          // );
          // $gamelog = M('game_log')->where(array('game_id'=>$_POST['game_id']))->save($games);
          // var_dump($gamelog);
          if($savelog){

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
    // public function manyGame(){

    // }

  /**----------------  挑战书部分start    ---------------------**/

  /**
   * [challenge description]
   * @Author   佳民
   * @DateTime 2019-01-22
   * @Function [挑战书详情]
   * @return   [JSON]     [description]
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
    $uid =S($_POST['token'])[2];
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
   * @return   [JSON]     [description]
   */
  public function challenge(){
       $uid =S($_POST['token'])[2];
    
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
     * [createClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [创建俱乐部]
     * @return   [JSON]     [description]
     */
    public function createClub(){
       // $uid =S($_POST['token'])[2];
       $uid = 183;
       $subordinate  =  M('user')->where(array('parent1'=>$uid))->count();   //下级人数
       $userInfo    =  M('user')->where(array('id'=>$uid))->field('nickname,money,active_point')->find();    
       // echo json_encode($userInfo['money']);
       if(IS_POST){
          if($subordinate < 1 || $userInfo['active_point'] <100){
              echo json_encode(array('status'=>-1,'msg'=>'对不起,暂无资格创建俱乐部'));
          }elseif($userInfo['money'] < 5000){
              echo json_encode(array('status'=>-2,'msg'=>'余额不足，请先充值'));
          }else{
              $data = array(
                  'ower_id'     => $uid,
                  'openid'      => S('user_info_'.$uid)[1],
                  'ower_name'   => $userInfo['nickname'],
                  // 'tel'         => $_POST['tel'],      
                  'club_head'   => $_POST['club_head'], //俱乐部图标
                  'club_name'   => $_POST['club_name'], //俱乐部名称
                  'declaration' => $_POST['declaration'], //社团宣言
                  'area'        => $_POST['area'],  //所在地
                  'create_fee'  => 5000, //创建费用
                  'create_number'  => 20,//创建人数
                  'level'  => 1,
              ); 

             
              $club_name = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
              if($club_name){
                 echo json_encode(array('status'=>-2,'msg'=>'该俱乐部已存在'));exit;
              }else{
                $result = M('club_info')->add($data);
                if($result){
                  // $club = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
                  M('user')->where(array('id'=>$uid))->setDec('money',$data['create_fee']);
                  echo json_encode(array('status'=>1,'msg'=>'创建俱乐部成功'));exit;
                }
              } 
          }
       }
    }
    /**
     * [clubs description]
     * @Author   佳民
     * @DateTime 2019-01-23
     * @Function [推荐俱乐部]
     * @return   [JSON]     [description]
     */
    public function clubs(){
        // if(IS_POST){

      
          $club_name = $_POST['club_name'];
          $aWhere['club_name'] = array('like','%'.$club_name.'%');
          $club = M('club_info')->field('id,club_name,club_head,ower_name,create_number')->where($aWhere)->select();
          $data = array_column($club, 'id');
          foreach($data as $k=>$v){
            $id=$v['id'];
            // var_dump($id);exit;
            $user=M("user")->where(array("club_id"=>$id))->count();
            $active=M("user")->where(array("club_id"=>$id))->sum('active_point');
            $club[$k]['active']=$active;
            $club[$k]['create_number']=$user.'/'.$club[$k]['create_number'];
          }
          $active = array_column($club,'active');
          array_multisort($active,SORT_DESC,$club);
          if($club){
              S('club_list',$club,18000);//俱乐部列表存入Redis
          }
        // }
        echo json_encode(array('status'=>1,'msg'=>'返回成功','data'=>$club));
    }

    //图片上传
    public function upload(){
      if(IS_POST){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize  =   3145728 ;// 设置附件上传大小
        $upload->exts   =   array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath =   './Uploads/'; // 设置附件上传根目录
        // $upload->savePath =   ''; // 设置附件上传（子）目录
        // 上传文件 
        $info  =  $upload->upload();
        if(!$info) {// 上传错误提示错误信息
          $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
          echo json_encode(array('status'=>1,'msg'=>'上传成功'));
        }
      }
    }
    /**
     * [clubInfo description]
     * @Author   佳民
     * @DateTime 2019-03-04
     * @Function [俱乐部详情]
     * @return   [JSON]     [description]
     */
    public function clubInfo(){
      // var_dump(S('clubinfo_5'));exit;
       // $uid =S($_POST['token'])[2];
       // $club_id = S('user_info_'.$uid)[2];
       $club_id = 5;
       $uid = 182;
       if(IS_POST){
          if(S('clubinfo_'.$club_id)){
            // echo "213";exit;
               $info = S('clubinfo_'.$club_id);
          }else{
              $clubInfo = M('club_info')->where(array('id'=>$club_id))->find();
              $usernum  = M('user')->where(array('club_id'=>$club_id))->count();
              $active_point  = M('user')->where(array('club_id'=>$club_id))->sum('active_point');
              $userInfo =  M('user')->where(array('id'=>$clubInfo['ower_id']))->field('club_role,headimg,is_club_owner')->find();

              $info = array(
                'club_id'   => $clubInfo['club_id'], //俱乐部ID
                'club_name' => $clubInfo['club_name'],//俱乐部名称
                'club_head' => $clubInfo['club_head'],//俱乐部图标
                'club_role' => $userInfo['is_club_owner'],   //俱乐部身份
                'headimg' => $userInfo['headimg'],//创建人头像
                'declaration' => $clubInfo['declaration'],//宣言
                'area' => $clubInfo['area'],//地区
                'ower_name' => $clubInfo['ower_name'],//创建人
                'club_notice' => $clubInfo['club_notice'],//俱乐部公告
                'club_number' => $usernum.'/'. $clubInfo['create_number'],//俱乐部现有人数/俱乐部创建人数
                'active_point' => $active_point, //活跃度
                'create_time' => date('Y-m-d',strtotime($clubInfo['create_time'])), //创建时间
              );

          }
          S('clubInfo_'.$club_id,$info,18000);
          echo json_encode(array('status'=>1,'msg'=>'俱乐部信息返回成功','data'=>$info));exit;
       }else{
          echo json_encode(array('status'=>-1,'msg'=>'系统错误'));
       } 

    }
    /**
     * [clubMembers description]
     * @Author   佳民
     * @DateTime 2019-01-25
     * @Function [俱乐部成员列表]
     * @return   [JSON]     [description]
     */
    public function  clubMembers(){
      // $club_id = S('user_info_'.$uid)[2];
      if(IS_POST){
        $club_id = 5;
        if(S('clubMembers_'.$club_id)){
          // var_dump(S('clubMembers_'.$club_id));exit;
          echo json_encode(S('clubMembers_'.$club_id));
        }else{
          $members = M('user')->where(array('club_id'=>5))->field('id,nickname,club_role,headimg,active_point,win_amount,last_login_time')->select();
          $data = array_column($members, 'id');
          foreach($data as $k=>$v){
            $id=$v['id'];
            $members[$k]['grade']  = $this->grade($members[$k]['win_amount']); //等级
            $members[$k]['level']  = $this->level($members[$k]['active_point']); //段位
            // $members[$k]['active'] = $this->level($members[$k]['active_point']); //周活跃
            // $user=M("user")->where(array("club_id"=>$id))->count();
            // $active=M("user")->where(array("club_id"=>$id))->sum('active_point');
            // $members[$k]['active']=$active;
            
          }
          S('clubMembers_'.$club_id,$members);
          echo json_encode($members);
        }
      
        
      }  
        
    }
    public function clubSet(){
      $club_id = $_POST['club_id'];
      if(IS_POST){
        $result = M('club_info')->where(array('id'=>$club_id))->save($_POST);
        if($result){
          S('clubInfo_'.$club_id,null);
          echo json_encode(array('status'=>1,'msg'=>'编辑成功'));
        }else{
          echo json_encode(array('status'=>-1,'msg'=>'编辑失败'));
        }
      }
    }
    /**
     * [clubSet description]
     * @Author   佳民
     * @DateTime 2019-03-12
     * @Function [俱乐部成员操作]
     * @return   [JSON]     [description]
     */
    public function memberSet(){

      if(IS_POST){
        if($_POST['type'] == 1){ //将成踢出俱乐部
           $result = M('user')->where(array('id'=>$_POST['uid']))->setField('club_id',0);
           if($result){
            S('user_info_'.$uid,null);
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
           }
        }elseif($_POST['type'] == 2){ //转让部长
          $nickname = M('user')->where(array('id'=>$_POST['uid']))->getField('nickname');
          $result = M('club_info')->where(array('id'=>$_POST['club_id']))->setField(array('ower_id',$_POST['uid']),array('ower_name',$nickname));
          if($result){
            S('clubInfo_'.$club_id,null);
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
           }
        }else{
          echo json_encode(array('status'=>-1,'msg'=>'系统错误'));
        }
      }
    }


    /**
     * [joinClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [加入俱乐部]
     * @return   [JSON]     [description]
     */
    public function joinClub(){
        $uid = 185;
        $club_id =7;
        if($club_id == null ){
          return false;
        }
        // if(IS_POST){
          $user = M('user')->where(array('id'=>$uid))->field('club_id,nickname')->find();
          $club = M('club_info')->where(array('id'=>$club_id))->field('create_number,club_name,ower_id')->find();
          $club_num = M('user')->where(array('club_id'=>$club_id))->count();
          if($club['create_number'] == $club_num){
            echo json_encode(['status'=>-1,'msg'=>'人数已达上限']);exit;

          }else{

            if($user['club_id'] != 0){
              echo json_encode(['status'=>-2,'msg'=>'你当前已有俱乐部']);exit;
            }else{
              $data = array(
                'user_id' => $uid,
                'club_id' => $club_id,
                'status'  => 3,
                'type'    => 1,
                'remark' => 玩家.$user['nickname'].申请加入.$club['club_name'].是否同意？,
               
              );
              $result = M('club_infomation')->add($data);
            
              if($result){
                $email = array(
                'user_id' => $club['ower_id'],
                'club_id' => $club_id,
                'email_id'  => $result,
                'status'    => 3,
                 );
                M('email_read')->add($email);
                echo json_encode(['status'=>1,'msg'=>'申请成功']);exit;
              }
            }

          // }
        } 
    }

        /**
     * [joinClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [处理邮件]
     * @return   [JSON]     [description]
     */
    public function handleEmail(){
        $email_id = $_POST['email_id'];//邮件id
        if(IS_POST){
          // $info = M('club_infomation')->where(array('id'=>$apply_id))->find();
          if($_POST['type'] == 1){//处理俱乐部申请
           
              $result = M('club_infomation')->where(array('id'=>$email_id))->setField('status',$_POST['status']);
              if($result){
              	$data = ['email_id' => $email_id,'user_id'  => $uid,'status'   => 2];
        	    $result = M('email_read')->add($data);
                echo json_encode(array('status'=>1,'msg'=>'处理成功'));
              }
          }elseif($_POST['type'] == 2){//删除邮件
          		foreach ($email_id as $k => $v) {
          			$data = ['email_id'=>$v,'user_id'=>182,'status'=>1];
             		$del = M('email_read')->add($data);
          		}
          	 
             if($del){
                echo json_encode(array('status'=>1,'msg'=>'处理成功'));exit;
             }
             
          }else{ //俱乐部详情
            $info = M('club_infomation')->where(array('id'=>$email_id))->find();
            if($info){
            	$data = ['email_id'=>$email_id,'user_id'=>$uid,'status'=>2];
        	    $result = M('email_read')->add($data);

            }
            echo json_encode(array('status'=>1,'msg'=>'处理成功','data'=>$info));
          }
        }
    }
    /**
     * [clubRecords description]
     * @Author   佳民
     * @DateTime 2019-03-06
     * @Function [俱乐部邮件]
     * @return   [JSON]     [description]
     */
    public function clubRecords(){
    	// $uid =S($_POST['token'])[2];
     $user =  M('user')->where(array('id'=>183))->field('is_club_owner,club_id')->find();
     // var_dump($user);exit;
      if($user['is_club_owner'] == 1){
     	$infomation=M('club_infomation')->alias('c')
			                        ->join("dd_email_read e on c.id=e.email_id") //附表连主表
			                        ->field("c.id,c.status,c.type,c.remark,c.club_id,c.time")
			                        ->where(array('e.user_id'=>183,'e.status'=>2,'c.club_id'=>$user['club_id']))//需要显示的字段
			                        ->select();
         // $infomation=M('club_infomation')->where(array('club_id'=>$user['club_id']))->select();

      }else{
      	$infomation=M('club_infomation')->alias('c')
			                        ->join("dd_email_read e on c.id=e.email_id") //附表连主表
			                        ->field("c.id,c.status,c.type,c.remark,c.club_id,c.time")
			                        ->where(array('e.user_id'=>183,'e.status'=>array('neq',1),'club_id'=>$user['club_id'],'c.type'=>array('neq',1)))
			                        ->select();
         // $infomation=M('club_infomation')->where(array('club_id'=>$user['club_id'],'type'=>array('neq',1)))->select();

      }
      // $infomation=M('club_infomation')->where(array('status'=>1,'type'=>array('')))->select();
      // var_dump($user);exit;
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$infomation]);
      
    }



  /**----------------  俱乐部部分end    ---------------------**/

// public function active($uid){

// }
 /**
  * [duan description]
  * @Author   佳民
  * @DateTime 2019-02-22
  * @Function [等级]
  * @param    [type]      [description]
  * @return   [type]      [description]
  */
    public function level($active){
        if(0 <= $active&$active <= 50){
            return '青铜';
        }elseif (51 <= $active&$active <= 200){
            return '白银';
        }elseif (201 <= $active&$active <= 400){
            return '黄金';
        }elseif (401 <= $active&$active <= 600){
            return '铂金';
        }elseif (601 <= $active&$active <= 800){
            return '钻石';
        }elseif (801 <= $active&$active <= 1000){
            return '大师';
        }else{
            return '王者';
        }
    }


 

 /**
  * [duan description]
  * @Author   佳民
  * @DateTime 2019-02-22
  * @Function [等级]
  * @param    [type]      [description]
  * @return   [type]      [description]
  */
    public function grade($win){
        if(0 <= $win&$win <= 5){
            return 'a';
        }elseif (6 <= $win&$win <= 10){
            return 'b';
        }elseif (11 <= $win&$win <= 20){
            return 'c';
        }elseif (21 <= $win&$win <= 30){
            return 'd';
        }elseif (31 <= $win&$win <= 40){
            return 'e';
        }else{
            return 'f';
        }
    }
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
     * @return   [JSON]     [description]
     */
    public function applyList(){
        $uid =S($_POST['token'])[2];
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
     * @return   [JSON]     [description]
     */

    public function agree(){
        $uid =S($_POST['token'])[2];
        if(IS_POST){
            
             $addFriend =  M('apply')->where(array('id'=>$_POST['aid'],'uid'=>$uid))->setField('status',2);
             if($addFriend){
                echo json_encode(array('status'=>1,'msg'=>'添加成功'));
             }
        }
    }


    /********      好友部分结束  end   ********/







     /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [查询领取记录]
     * @return   [JSON]     [description]
     */
     public function receive(){
      $uid =S($_POST['token'])[2];
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
     * @return   [JSON]     [description]
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
     * @return   [JSON]     [description]
     */
    public function signs(){
   
       $uid =S($_POST['token'])[2];
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
  


}