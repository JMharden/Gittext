<?php

namespace Api\Controller;
use Api\Service\GameService;
use Api\Service\UserService;
use Think\Controller;

/**
 * Class IndexController
 * @package Api\Controller把需要接口权限校验的都单独放到一个controller 和login分开
 */
class IndexController extends ApiController
{
  
  public function _initialize(){
       // parent::_initialize();
       parent::_load_config();
       // parent::write_log();
       $rand = $_GET['rand'];
       if($rand&&S('request_rand_'.$rand)){
           echo   json_encode(['status' => '-2', 'msg' => '重复提交']);
           exit;
       }
       if($rand){
           S('request_rand_'.$rand,$rand,300);
       } 
   }



  /**----------------  俱乐部部分start    ---------------------**/
      /**
     * [createClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [创建俱乐部]
     * @return   [JSON]     [description]
     */
    public function createClub(){
      // var_dump($_POST);exit;
       // $uid =S($_POST['token'])[2];
       $uid = 182;
      $where = array(
        'parent1'=>$uid,
        'parent2'=>$uid,
        'parent3'=>$uid,
        '_logic' => 'or',
      );
   
       $subordinate =  M('user')->where($where)->count();   //下级人数
       $userInfo  = M('user')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.nickname,a.openid,a.headimg,u.money")
                        ->where(array('a.id'=> $uid))//需要显示的字段
                        ->find();
       // echo json_encode($userInfo['money']);
       if(IS_POST){
          if($subordinate < 3 || $userInfo['money'] < 600){
              echo json_encode(array('status'=>-1,'msg'=>'对不起,暂无资格创建俱乐部'));
          }else{

              $data = array(
                  'ower_id'     => $uid,
                  'openid'      => $userInfo['openid'],
                  'ower_name'   => $userInfo['nickname'],
                  'club_head'   => $userInfo['headimg'], //俱乐部图标
                  'club_name'   => $_POST['club_name'], //俱乐部名称
                  'create_fee'  => 500, //创建费用
                  'create_number'  => 20//创建人数
             
              ); 
              $club_name = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
              if($club_name){
                 echo json_encode(array('status'=>-2,'msg'=>'该俱乐部已存在'));exit;
              }else{
                $result = M('club_info')->add($data);
                if($result){
                  $datas = array(
                    'money' => $userInfo['money']-$data['create_fee'],
                    'club_id'=>$result,
                    'is_club_ower'=>1
                  );
                  M('user')->where(array('id'=>$uid))->save($datas);
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
          $club = M('club_info')->field('id,club_name,club_head,ower_name,area,create_time,declaration,club_notice,create_number')->where($aWhere)->select();
          $data = array_column($club, 'id');
          // var_dump($data);exit;
          foreach($data as $k=>$v){
            $user=M('user')->where(array('club_id'=>$v))->count();
            $active=M('user')->where(array('club_id'=>$v))->sum('active_point');
            $club[$k]['active']=$active;//俱乐部活跃度
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
/**
 * [upload description]
 * @Author   佳民
 * @DateTime 2019-04-17
 * @Function [function]
 * @return   [type]     [description]
 */
public function upload(){
  $user_id = 182;
    if(IS_POST){
         $type = $_POST['type']; //1为头像  2为二维码 ； 
         $code = $_FILES['file'];//获取小程序传来的图片
          if(is_uploaded_file($_FILES['file']['tmp_name'])) {  
                //把文件转存到你希望的目录（不要使用copy函数）  
                $uploaded_file=$_FILES['file']['tmp_name'];  
              
                //我们给每个用户动态的创建一个文件夹  
                $user_path="./Public/upload/image";  
                //判断该用户文件夹是否已经有这个文件夹  
                if(!file_exists($user_path)) {  
                    //mkdir($user_path); 
                    mkdir($user_path,0777,true); 
                }  
             
                //$move_to_file=$user_path."/".$_FILES['file']['name'];  
                $file_true_name=$_FILES['file']['name'];  
                $move_to_file=$user_path."/".date('Y-m-d').'_'.$user_id.'_'.$type.'_'.'club';//strrops($file_true,".")查找“.”在字符串中最后一次出现的位置  
                $url = "http://tt.wapwei.com".'/'.$move_to_file;
                //echo "$uploaded_file   $move_to_file";  
                if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) { 
                      if($type == 1){
                        M('club_info')->where(array('ower_id'=>$user_id))->setField('club_head',$url);
                      }else{
                        M('club_info')->where(array('ower_id'=>$user_id))->setField('ercode',$url);
                      }
                     echo json_encode(['status'=>1,'msg'=>'上传成功','data'=>$url]);exit;
             
                } else {  
                    echo json_encode(['status'=>-1,'msg'=>'上传失败']);exit;
             
                }  
            } else {  
                echo json_encode(['status'=>-1,'msg'=>'上传失败']);exit; 
            }

    }else{
      echo json_encode(['status'=>0,'msg'=>'系统错误']);exit;
    }

    
    
}
    public function addEmail($title,$content,$category,$send_to,$create_user){
      $data = array(
        'title' => $title,
        'content' => $content,
        'category' => $category,
        'send_to' => $send_to,
        'create_user' => $create_user,
        'create_time' => date('Y-m-d H:i:s',NOW_TIME),
        'expire_time' => date("Y-m-d 23:59:59",strtotime("+7 day"))

      );
      $res = M('message_info')->add($data);
     
    }
    
    /**
     * [joinClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [加入俱乐部]
     * @return   [JSON]     [description]
     */
    public function joinClub(){
        $uid = $GLOBALS['current_uid'];
        $club_id = $_POST['club_id'];
        if($club_id == null ){
          return false;
        }
        if(IS_POST){
          $user = M('user')->where(array('id'=>$uid))->field('club_id,nickname')->find();
          $club = M('club_info')->where(array('id'=>$club_id))->field('create_number,club_name,ower_id')->find();
          $club_num = M('user')->where(array('club_id'=>$club_id))->count();

          if($club['create_number'] == $club_num){
            echo json_encode(['status'=>-1,'msg'=>'人数已达上限']);exit;

          }else{

            if($user['club_id'] != 0){
              echo json_encode(['status'=>-2,'msg'=>'你当前已有俱乐部']);exit;
            
            }else{
              if(M('message_info')->where(array('create_user'=>$uid,'category'=>1,'send_to'=>$club['ower_id']))->find()){
                echo json_encode(['status'=>-3,'msg'=>'请勿重复申请']);exit;
              }else{
                $content = $user['nickname'].申请加入.$club['club_name'].是否同意？;
                $this->addEmail('申请信息',$content,1,$club['ower_id'],$uid);          
                echo json_encode(['status'=>1,'msg'=>'申请成功']);exit;
                
              }
            }
          }
        } 
    }

    
    /**
     * [handle description]
     * @Author   佳民
     * @DateTime 2019-04-24
     * @Function [部长处理申请信息]
     * @return   [type]     [description]
     */
    public function handle(){
      $uid = $GLOBALS['current_uid'];
     
      if(IS_POST){
         $type = $_POST['type'];
           $create_user = M('message_info')->where(array('id'=>$_POST['msgId']))->getField('create_user');//获取申请人ID
         $club = M('club_info')->where(array('ower_id'=>$uid))->field('id,club_name')->find();
        if($type == 1){//同意加入
         
          $res = M('user')->where(array('id'=>$create_user))->setField('club_id',$club['id']);
          // var_dump($res);exit;
  
          $content = 恭喜您成功加入.$club['club_name'];
          
            // var_dump(123);exit;
        $this->addEmail('成员变动',$content,2,$create_user,$uid);
            

        }else{
          $content = 俱乐部.$club['club_name'].拒绝了您的请求;
          $this->addEmail(成员变动,$content,2,$create_user,$uid);
          // echo json_encode(['status'=>1,'msg'=>'成功']);
        }
       M('message_info')->where(array('id'=>$_POST['msgId']))->delete();//获取申请人ID
      }
    }

/**
 * [quitClub description]
 * @Author   佳民
 * @DateTime 2019-04-17
 * @Function [退出俱乐部]
 * @return   [type]     [description]
 */
public function quitClub(){
  if(IS_POST){
      $uid = $GLOBALS['current_uid'];
      
      $user = M('user')->where(array('id'=>$uid))->field('club_id,nickname')->find();
      $club = M('club_info')->where(array('id'=>$user['club_id']))->field('create_number,club_name,ower_id')->find();
      $quit = M('user')->where(array('id'=>$uid))->setField('club_id',0);
      if($quit){
        $content = $user['nickname'].退出了.$club['club_name'].俱乐部;
        $this->addEmail('成员变动',$content,2,$club['ower_id'],$uid);
        echo json_encode(['status'=>1,'msg'=>'退出俱乐部成功']);exit;
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

       
       $club_id = 3;  
       if(IS_POST){
           $uid = 182;
           $club =  M('user')->where(array('id'=>$uid))->getField('club_id');
           // if($club == 0){
           //    echo json_encode(array('status'=>-2,'msg'=>'暂未加入俱乐部'));exit;
           // }else{
              if(S('clubinfo_'.$club_id)){
                   $info = S('clubinfo_'.$club_id);
              }else{
                 
                  $clubInfo = M('club_info')->where(array('id'=>$club_id))->find();
                  $usernum  = M('user')->where(array('club_id'=>$club_id))->count();
                  $active_point  = M('user')->where(array('club_id'=>$club_id))->sum('active_point');
                  $is_club_owner =  M('user')->where(array('id'=>$uid))->getField('is_club_owner');
                  $userInfo =  M('user')->where(array('id'=>$clubInfo['ower_id']))->getField('headimg');
                  // $has_club = M('user')->where(array('id'=>$uid))->getField('club_id');
                  $info = array(
                              
                    'club_id'     => $clubInfo['id'],           //俱乐部ID
                    'club_name'   => $clubInfo['club_name'],    //俱乐部名称
                    'club_head'   => $clubInfo['club_head'],    //俱乐部头像
                    'club_role'   => $is_club_owner,            //俱乐部身份0
                    'headimg'     => $userInfo['headimg'],      //创建人头像
                    'declaration' => $clubInfo['declaration'],  //宣言
                    'area'        => $clubInfo['area'],         //地区
                    'ercode'      => $clubInfo['ercode'],       //俱乐部二维码
                    'ower_name'   => $clubInfo['ower_name'],    //创建人
                    'club_notice' => $clubInfo['club_notice'],  //俱乐部公告
                    'club_number' => $usernum.'/'. $clubInfo['create_number'],//俱乐部现有人数/俱乐部创建人数
                    'active_point'=> $active_point,             //活跃度
                    'create_time' => date('Y-m-d',strtotime($clubInfo['create_time'])) //创建时间
                  );
                  // var_dump($info);exit;
              }
              S('clubInfo_'.$club_id,$info,1800);
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
      // if(IS_POST){
        $club_id = 3;
        // if(S('clubMembers_'.$club_id)){
        //   // var_dump(S('clubMembers_'.$club_id));exit;
        //   echo json_encode(S('clubMembers_'.$club_id));
        // }else{
          $members = M('user')->where(array('club_id'=>$club_id))->field('id,nickname,is_club_owner,headimg,rank,last_login_time,active_point,slime,match_amount,win_amount')->select();
          $data = array_column($members,'id');
         
          $slime_id = $this->checkSlime();
          foreach($data as $k=>$v){
            $id=$v['id'];

            $members[$k]['probability']  = round($members[$k]['win_amount']/$members[$k]['match_amount']*100,2)."%";
            // $members[$k]['sc'] = M('play_match_log')->where(array('id'=>array('in',)))->count();
            $slime = json_decode($members[$k]['slime'],true);
            $members[$k]['s_level']  = $slime[$slime_id]['exp'];  //史莱姆等级
            $members[$k]['slime']    = $slime[$slime_id]['sid'];  //史莱姆ID
    
            $members[$k]['level']  = GameService::getDuan($members[$k]['rank']);  //段位
          }
          // S('clubMembers_'.$club_id,$members);
          echo json_encode($members);
        // }
      
      // }  
        
    }
    public function clubSet(){
    	$uid = $GLOBALS['current_uid'];
        $club_id =  M('user')->where(array('id'=>$uid))->getField('club_id');
      // $club_id = $_POST['club_id'];
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
      $user_id = $GLOBALS['current_uid'];  //用户ID
      if(IS_POST){
        // if($_POST['type'] == 1){ //将成员踢出俱乐部
           $result = M('user')->where(array('id'=>$_POST['uid']))->setField('club_id',0);
           if($result){
              $content = 您被.$club['club_name'].俱乐部部长踢出了俱乐部;
              $this->addEmail('成员变动',$content,2,$_POST['uid'],$user_id);
              echo json_encode(array('status'=>1,'msg'=>'成功踢出该成员'));
           }
      }
    } 

   
/**
 * 消息已读操作
 */
    public function readEmail(){
      if(IS_POST){

        $userId = $GLOBALS['current_uid'];
        $msgId =  $_POST['msgId'];
        $has_message =M('message_log')->where(array('uid'=>$userId,'msg_id'=>$msgId))->find();
        if($has_message){
          echo json_encode(['status' => '1', 'msg' => '改邮件已读']);exit;
        }else{
            $data = ['msg_id' => $msgId,
            'uid' => $userId,
            'status' => 1,
            'create_time' => NOW_TIME
          ];
          $res =M('message_log')->add($data);
          echo json_encode(['status' => '1', 'msg' => '返回成功']);
        }
            
      }
    }

    /**
     * 消息删除操作
     */
    public function delEmail(){

      //  $userId =  $GLOBALS['current_uid'];
       if(IS_POST){
	        $userId = $GLOBALS['current_uid'];
	        $mId =  $_POST['msgId'];
	        $msgId = explode(",", $mId);
	        
         foreach ($msgId as $k => $v) {
              $data = [
                  'msg_id' => $v,
                  'status' => 2,
                  'modify_time' => NOW_TIME
              ];
              
              $res = M('message_log')->where(array('uid'=>$userId,'msg_id'=>$v))->save($data);
           
          
            if($res){
              echo json_encode(['status' => '1', 'msg' => '删除成功']);exit;
            } 
          }
       }else{
          echo json_encode(['status' => '-1', 'msg' => '系统错误']);
       }
    }


    /**
     * [getTime description]
     * @Author   佳民
     * @DateTime 2019-04-24
     * @Function [获取时间]
     * @param    [type]     $time [description]
     * @return   [type]           [description]
     */
 public function getTime($time){
   $now_time = strtotime(date("Y-m-d H:i:s", time()));
   $show_time = strtotime($time);
   $dur = $now_time - $show_time;

      if ($dur < 60) {
          return '刚刚';
      } else {
          if ($dur < 3600) {
              return floor($dur / 60) . '分钟前';
          } else {
              if ($dur < 86400) {
                  return floor($dur / 3600) . '小时前';
              } else {
                  if ($dur < 604800) {//7天内
                      return floor($dur / 86400) . '天前';
                  } else {
                       return '7天前';
                  }
              }
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
      $uid = 182;
      $is_club_owner =  M('user')->where(array('user_id'=>$uid))->getField('is_club_owner');
      $Model = new \Think\Model();
      // if($is_club_owner == 1){
      $infomation =  $Model->query("SELECT  o.id, o.title,o.content,o.create_time,o.category,o.create_user,   g.`status`   FROM   dd_message_info o   LEFT JOIN dd_message_log g ON o.id = g.msg_id    AND g.uid = ".$uid."  WHERE   ( g.msg_id IS NULL or g.status='1') and  o.send_to  in ('all',".$uid.") and o.category in(1,2) and o.send_time < CURRENT_TIMESTAMP AND o.expire_time > CURRENT_TIMESTAMP order by id desc;");
          $data = array_column($infomation,'id');
      foreach($data as $k=>$v){
        $id=$v['id'];
        
        $infomation[$k]['create_time']= $this->getTime($infomation[$v]['create_time']);

      }
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$infomation]);
      
    }



  /**----------------  俱乐部部分end    ---------------------**/


 /**----------------  史莱姆馆    ---------------------**/

     /**二分查找*/
    public function search($score, $filter)

    {   

        $half = floor(count($filter) / 2); // 取出中間数

        // 判断积分在哪个区间

        if ($score <= $filter[$half-1]['max']) {

            $filter = array_slice($filter, 0 , $half);

        } else {

            $filter = array_slice($filter, $half , count($filter));

        }
        // 继续递归直到只剩一个元素

        if (count($filter) != 1) {

            $filter = self::search($score, $filter);

        }
        return $filter;

    }

    /*  史莱姆等级 */
     public function s_level($level){
      $filter = [
        ['level' => 1,  'min' => 0,     'max' => 499],
        ['level' => 2,  'min' => 500,   'max' => 999],
        ['level' => 3,  'min' => 1000,  'max' => 1499],
        ['level' => 4,  'min' => 1500,  'max' => 1999],
        ['level' => 5,  'min' => 2000,  'max' => 2499],
        ['level' => 6,  'min' => 2500,  'max' => 2999],
        ['level' => 7,  'min' => 3000,  'max' => 3499],
        ['level' => 8,  'min' => 3500,  'max' => 3999],
        ['level' => 9,  'min' => 4000,  'max' => 4499],
        ['level' => 10, 'min' => 4500,  'max' => 4999],
        ['level' => 11, 'min' => 5000,  'max' => 5699],
        ['level' => 12, 'min' => 5700,  'max' => 6399],
        ['level' => 13, 'min' => 6400,  'max' => 7099],
        ['level' => 14, 'min' => 7100,  'max' => 7799],
        ['level' => 15, 'min' => 7800,  'max' => 8499],
        ['level' => 16, 'min' => 8500,  'max' => 9099],
        ['level' => 17, 'min' => 9100,  'max' => 9799],
        ['level' => 18, 'min' => 9800,  'max' => 10499],
        ['level' => 19, 'min' => 10500, 'max' => 11199],
        ['level' => 20, 'min' => 11200, 'max' => 11899],
        ['level' => 21, 'min' => 11900, 'max' => 12899],
        ['level' => 22, 'min' => 12900, 'max' => 13899],
        ['level' => 23, 'min' => 13900, 'max' => 14899],
        ['level' => 24, 'min' => 14900, 'max' => 15899],
        ['level' => 25, 'min' => 15900, 'max' => 16899],
        ['level' => 26, 'min' => 16900, 'max' => 17899],
        ['level' => 27, 'min' => 17900, 'max' => 18899],
        ['level' => 28, 'min' => 18900, 'max' => 19899],
        ['level' => 29, 'min' => 19900, 'max' => 20899],
        ['level' => 30, 'min' => 20900, 'max' => 22000],

      ];

      $result = $this->search($level, $filter);

      return  current($result);
     }

  /**
     * [checkSlime description]
     * @Author   佳民
     * @DateTime 2019-04-26
     * @Function [检测当前史莱姆]
     * @return   [type]     [description]
     */
    public function checkSlime(){
      $GLOBALS['slime'] = 1;
      return $GLOBALS['slime'];
    }


    /**
     * [slime description]
     * @Author   佳民
     * @DateTime 2019-04-29
     * @Function [史莱姆列表]
     * @return   [type]     [description]
     */
    public function slime(){

      $user_id = 182;
      $Model = new \Think\Model();

        $data =  $Model->query("SELECT  g.s_id,g.u_id,g.blue,g.blood,g.exp,o.id,o.name,o.skill,o.skill_introduction,o.slime_introduction FROM   dd_slime  o   LEFT JOIN dd_user_slime g ON o.id = g.s_id  AND g.u_id = ".$user_id."  order by o.id asc ;");
        $result = array_column($data, 'id');
      
        foreach($result as $k=>$v){
           $level = $this->s_level($data[$k]['exp']);
      	   $data[$k]['max_exp']=  $level['max']-$level['min'];
      	   $data[$k]['exp']=  $data[$k]['exp']-$level['min'];
           $data[$k]['level']  = $level['level'];

  
        } 

        $candy = M('user')->where(array('user_id'=>$user_id))->field('candy,candy1,candy2')->find();
        
        // var_dump($candy);exit;
        echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data,'candy'=>$candy]);
    }





    public function addslime(){
      $user_id = 183;
      $result = M('slime')->where(array('id'=>3))->find();
      // var_dump($result);exit;
      $data  = array(
        's_id' => $result['id'],
        'u_id' => $user_id,
        'name' => $result['name'],
        'blue' => $result['blue'],
        'blood'=> $result['blood'],
        'skill' => $result['skill'],  
      );
      M('user_slime')->add($data);
      // M()
    }


                                                            
    /**
     * [unlockSlime description]
     * @Author   佳民
     * @DateTime 2019-04-29
     * @Function [解锁史莱姆]
     * @return   [type]     [description]
     */
    public function unlockSlime(){
       $user_id = 182;
      if(IS_POST){
        $candy = $this->candyNum($user_id,$_POST['candy']);
        if($candy == false){
          echo json_encode(['status'=>-1,'msg'=>'糖果不足']);exit;
        }
         $result = M('slime')->where(array('id'=>$_POST['s_id']))->find();

         if($save){
            echo json_encode(['status'=>1,'msg'=>'解锁成功']);
         }
      }
     
          
    }

    /**
     * [upSlime description]
     * @Author   佳民
     * @DateTime 2019-04-30
     * @Function [升级史莱姆]
     * @return   [type]     [description]
     */
    public function upSlime(){
      $user_id = 182;
      if(IS_POST){
         $sid = $_POST['s_id'];
         // var_dump($sid);exit;
        
         $type = $_POST['type'];
		     $candyNum = $_POST['candy'];
      	 $exp = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->getField('exp');
     	   $nowLevel = $this->s_level($exp)['level'];
       // if
     	// var_dump($nowLevel['level']);exit;
     	     S('slime_'.$sid,$nowLevel);
     	
      	   $this->candy($user_id,$type,$candyNum,$sid);

           $nowSlime = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->field('exp,blood,blue')->find();
           $level = $this->s_level($nowSlime['exp']);
           // var_dump($level);exit;
           $nowSlime['exp']     = $nowSlime['exp']- $level['min'];
           $nowSlime['min_exp'] = $level['min'];
           $nowSlime['max_exp'] = $level['max']-$level['min'];
           $nowSlime['level']   = $level['level'];
           $num = $nowSlime['level']-S('slime_'.$sid);
           // var_dump($num);exit;
           if($num>0){
               	S('slime_'.$sid,null);
             		$blood  = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('blood',1); //增加蓝量，血量 
             		$blue   = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('blue',1); //增加经验值 
 		      }
 		      $shuxing = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->field('blood,blue')->find();
	      	$candy = M('user')->where(array('user_id'=>$user_id))->field('candy,candy1,candy2')->find();
 		      $nowSlime['blood']  = $shuxing['blood'];
          $nowSlime['blue']   = $shuxing['blue'];
         
           echo json_encode(['status'=>1,'msg'=>'升级成功','data'=>$nowSlime,'candy'=>$candy]);
            // }
         // }
      }
    }
  
    /**
     * [candyNum description]
     * @Author   佳民
     * @DateTime 2019-04-26
     * @Function [检测当前糖果数量]
     * @return   [type]     [description]
     */



   public function candy($user_id,$type,$candyNum,$sid){
   	// $type = 1;
	   	$candy = M('user')->where(array('user_id'=>$user_id))->field('candy,candy1,candy2')->find();
	   	if($type == 1){//小糖果
	   		if($candy['candy']>=$candyNum && $candyNum != 0){
	   			$data = M('user')->where(array('user_id'=>$user_id))->setDec('candy',$candyNum); //扣除用户糖果数量
	            $exps  = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('exp',$candyNum*100); //增加经验值    
	           
	   		}else{
	   			echo json_encode(['status'=>-1,'msg'=>'糖果不足']);exit;
	   		}
	   	}else if($type == 2){
	   		if($candy['candy1']>=$candyNum && $candyNum != 0){
	   			$data = M('user')->where(array('user_id'=>$user_id))->setDec('candy1',$candyNum); //扣除用户糖果数量
	            $exps  = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('exp',$candyNum*200); //增加经验值    
	            
	   		}else{
	   			echo json_encode(['status'=>-1,'msg'=>'糖果不足']);exit;
	   		}
	   	}else{
	   		if($candy['candy2']>=$candyNum && $candyNum != 0){
	   			$data = M('user')->where(array('user_id'=>$user_id))->setDec('candy2',$candyNum); //扣除用户糖果数量
	            $exps  = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('exp',$candyNum*500); //增加经验值    
	            
	   		}else{
	   			echo json_encode(['status'=>-1,'msg'=>'糖果不足']);exit;
	   		}
	   	}
   }

   // public function slimeLevel(){

   // }

 /**----------------  俱乐部部分end    ---------------------**/


  public function feedback()
  {
    $start = date('Y-m-d 0:0:0');
    $end   = date('Y-m-d 23:59:59') ;
      if(IS_POST){
        $num = M('feedback')->where(array('user_id'=>182,'add_time'=>array('between',array($start,$end))))->count();
        if($num >= 20){
          echo json_encode(['status'=>-1,'msg'=>'今日反馈次数已达上限!']);exit;
        }
        $data = ['user_id'=>182,'content'=>$_POST['content']];
        $result = M('feedback')->add($data);
        if($result){
          echo json_encode(['status'=>1,'msg'=>'感谢您的反馈,我们将尽快处理!']);exit;
        }
      }

  }



  /**
 * @param $gameNum
 * @return mixed
 * @name   游戏场次评分
 */
    public function gameNum($gameNum){
        $filter = [
            ['level' => 4,  'min' => 0,     'max' => 50],
            ['level' => 3,  'min' => 51,   'max' => 150],
            ['level' => 2,  'min' => 151,  'max' => 250],
            ['level' => 1,  'min' => 251,  'max' => 400],
            ['level' => 0,  'min' => 401,  'max' => 9999],
        ];

        $result = $this->search($gameNum, $filter);

        return  current($result);
    }
    /**
     * @param $gameNum
     * @return mixed
     * @name   游戏胜率评分
     */
    public function shenglv($shenglv){
        $filter = [
            ['level' => 4,  'min' => 0,     'max' => 20],
            ['level' => 3,  'min' => 21,   'max' => 40],
            ['level' => 2,  'min' => 41,  'max' => 60],
            ['level' => 1,  'min' => 61,  'max' => 85],
            ['level' => 0,  'min' => 86,  'max' => 100],
        ];

        $result = $this->search($shenglv, $filter);

        return  current($result);
    }
    public function score($score){
    	$filter = [
            ['level' => 4,  'min' => 0,     'max' => 1350],
            ['level' => 3,  'min' => 1351,  'max' => 1500],
            ['level' => 2,  'min' => 1501,  'max' => 2000],
            ['level' => 1,  'min' => 2001,  'max' => 2500],
            ['level' => 0,  'min' => 2501,  'max' => 3000],

        ];
        $result = $this->search($score, $filter);

        return  current($result);
    }
    public function zhScore(){
    	$user_id  =  182;
    	if($user_id == null){
    		echo "参数错误！！！";
    	}
    	$user = M('user')->where(array('user_id'=>$user_id))->field('match_amount,win_amount,fun_amount,fun_win_amount,club_id,rank')->find();
    	$club_name = M('club_info')->where(array('id'=>$user['club_id']))->getField('club_name');

    	$gameNum = $this->gameNum($user['match_amount'] + $user['fun_amount'])['level'];  //场次评分
    	$game = $user['match_amount'] + $user['fun_amount']; //总场次
    	$win = $user['win_amount'] + $user['fun_win_amount'];//胜场
    	$sl = ($win/$game)*100;
    	$probability = round(($win/$game)*100).'%';
    	$intsl =floor($sl);
    	$shenglv = $this->shenglv($intsl)['level']; //胜率评分
    	$score = M('play_log')->where(array('user_id'=>182))->max('score');//最高步数

    	$allScore = $gameNum+$intsl+$user['rank'];
    	$zhScore  = $this->score('$allScore')['level'];
        $result = array(
        	'club_name'   => $club_name,  //俱乐部名称
        	'gameCount'   => $game,       //总场次
        	'probability' => $probability,//胜率 
        	'shenglv'     => $shenglv, //胜率评分  
        	'gameNum'     => $gameNum, //场次评分 
        	'score'       => $score,   //最高步数
        	'zhScore'     => $zhScore//综合评分
        );
    	echo json_encode(['statu'=>1,'msg'=>'返回成功','data'=>$result]);
    }


   public function  playHistory(){
   	$user_id = 182;
   
   	 if(IS_POST){
     	 	$type = 1;
     	 	$play = M('play_log')->where(array('user_id'=>$user_id,'type'=>$type,'statu'=>2))->field('rank,score,bonu,ranks,end_time,user_id')->select();
     	 	foreach ($play as $k => $v) {
     	 	  $userRank = M('user')->where(array('user_id'=>$v['user_id']))->getField('rank');
     	
     	 		$data = array(
     	 			'end_time'  => date('m-d H:i',$v['end_time']),
     	 			'score'     => $v['score'],
     	 			'rank'      => $v['rank'],
     	 			'ranks'     => $v['ranks'],
     	 			'bonu'      => floor($v['bonu']),
     	 			'userRank'  => $userRank
     	 		);
     	 		// $data[] = date('m-d H:i',$v['end_time']); 
     	 		// $data['score']    = $v['score'];
     	 		$datas[]  = $data;
	 	  }
   	 	
   	 	echo json_encode(['statu'=>1,'msg'=>'返回成功','data'=>$datas]);
   	 	// }
   	 }else{
   	 	echo json_encode(['statu'=>-1,'msg'=>'系统错误']);
   	 }	
   }
  public function funHistory(){
    $user_id = 182;
   
     if(IS_POST){
        $type = 2;
        // if($type == 1){
        $play = M('play_log')->where(array('user_id'=>$user_id,'type'=>$type,'statu'=>2))->field('rank,score,end_time,user_id')->select();
        // var_dump($play);exit;/

        foreach ($play as $k => $v) {
          $userRank = M('user')->where(array('user_id'=>$v['user_id']))->getField('rank');
      
          $data = array(
            'end_time'  => date('m-d H:i',$v['end_time']),
            'score'     => $v['score'],
            'rank'      => $v['rank'],
          );
          // $data[] = date('m-d H:i',$v['end_time']); 
          // $data['score']    = $v['score'];
          $datas[]  = $data;
       }
      
        echo json_encode(['statu'=>1,'msg'=>'返回成功','data'=>$datas]);
      // }
     }else{
        echo json_encode(['statu'=>-1,'msg'=>'系统错误']);
     }  
   }
   


}
