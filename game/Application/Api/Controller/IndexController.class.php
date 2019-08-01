<?php
namespace Api\Controller;
use Api\Service\GameService;
use Api\Service\UserService;
use Think\Controller;

/**
 * Class IndexController
 * @package 
 */
class IndexController extends ApiController
{
  
  public function _initialize(){
       parent::_load_config();
       parent::write_log();
       $token = $_POST['token'];

       if($token == null || S($token) == null){
           echo   json_encode(['status' => '403', 'msg' => 'token不能为空']);
           exit;
       }
       $GLOBALS['token'] = S($token);

   }
   /**
    随机AI
   */
    public function randAi(){
        if(IS_POST){
            $num = $_POST['num'];

            $data = M('jiqiren')->select();
            if($num == 1){

              
              $data_rand[]  =  $data[18];
            }
            $datas = array_rand($data, $num);
            foreach($datas as $val){
             
              $data_rand[]=$data[$val];
            }
            echo json_encode(array('status'=>1,'msg'=>'返回成功','data'=>$data_rand));exit;
        }else{
            echo json_encode(array('status'=>-1,'msg'=>'系统错误'));exit;
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
      $user_id = $GLOBALS['token'][2];
      $where = array(
        'parent1'=>$user_id,
        'parent2'=>$user_id,
        'parent3'=>$user_id,
        '_logic' => 'or',
      );
       $subordinate =  M('user_base')->where($where)->count();   //下级人数
       $userInfo  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.nickname,a.openid,a.headimg,u.money")
                        ->where(array('a.id'=> $user_id))//需要显示的字段
                        ->find();     
       if(IS_POST){
          if($userInfo['money'] < 500){
              echo json_encode(array('status'=>-1,'msg'=>'对不起,暂无资格创建俱乐部'));
          }else{

                $data = array(
                    'ower_id'     => $user_id,
                    'openid'      => $userInfo['openid'],
                    'ower_name'   => $userInfo['nickname'],
                    'club_head'   => $userInfo['headimg'], //俱乐部图标
                    'ercode'      => $userInfo['headimg'], //俱乐部图标
                    'club_name'   => $_POST['club_name'], //俱乐部名称
                    'create_fee'  => 500, //创建费用
                    'create_number'  => 30//创建人数
               
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
                      'is_club_owner'=>1
                    );
                    M('user')->where(array('user_id'=>$user_id))->save($datas);
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
  
          $club_name = $_POST['club_name'];
          $aWhere['club_name'] = array('like','%'.$club_name.'%');

          $club = M('club_info')->field('id,club_name,club_head,ower_name,area,create_time,declaration,club_notice,create_number')->where($aWhere)->select();
          $data = array_column($club, 'id');
          foreach($data as $k=>$v){
            $user=M('user')->where(array('club_id'=>$v))->count();
            $active=M('user')->where(array('club_id'=>$v))->sum('active_point');
            $club[$k]['active']=$active;//俱乐部活跃度
            $club[$k]['create_number']=$user.'/'.$club[$k]['create_number'];
            $club[$k]['create_time'] = date('Y-m-d',strtotime($club[$k]['create_time'])); //创建时间
          }
          $active = array_column($club,'active');
          array_multisort($active,SORT_DESC,$club);

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
      
      $user_id =$GLOBALS['token'][2];

      if(IS_POST){ 
  
            $code = $_FILES['file'];//获取小程序传来的图片

            if(is_uploaded_file($_FILES['file']['tmp_name'])) {  
                //把文件转存到你希望的目录（不要使用copy函数）  
                $uploaded_file=$_FILES['file']['tmp_name'];  
              
                //我们给每个用户动态的创建一个文件夹  
                $user_path="./Public/upload/image";  
                //判断该用户文件夹是否已经有这个文件夹  
                if(!file_exists($user_path)) {  
                    mkdir($user_path,0777,true); 
                }  
                $file_true_name=$_FILES['file']['name'];  
                $move_to_file=$user_path."/".time().'_'.$user_id.'_'.'club'.'.png';
                $url = "https://tt.wapwei.com".'/'.$move_to_file;
                if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {   
                      
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
        'title'    => $title,
        'content'  => $content,
        'category' => $category,
        'send_to'  => $send_to,
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
       $user_id =$GLOBALS['token'][2];
        $club_id = $_POST['club_id'];
        if($club_id == null ){
          return false;
        }
        if(IS_POST){
          $user  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.nickname,u.club_id")
                        ->where(array('a.id'=> $user_id))//需要显示的字段
                        ->find();
          $club = M('club_info')->where(array('id'=>$club_id))->field('create_number,club_name,ower_id')->find();
          $club_num = M('user')->where(array('club_id'=>$club_id))->count();

          if($club['create_number'] == $club_num){
            echo json_encode(['status'=>-1,'msg'=>'人数已达上限']);exit;

          }else{

            if($user['club_id'] != 0){
              echo json_encode(['status'=>-2,'msg'=>'你当前已有俱乐部']);exit;
            
            }else{
              if(M('message_info')->where(array('create_user'=>$user_id,'category'=>1,'send_to'=>$club['ower_id']))->find()){
                echo json_encode(['status'=>-3,'msg'=>'请勿重复申请']);exit;
              }else{
                $content = $user['nickname'].申请加入.$club['club_name'].是否同意？;
                $this->addEmail('申请信息',$content,1,$club['ower_id'],$user_id);          
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
      $user_id =$GLOBALS['token'][2];
     
      if(IS_POST){
         $type = $_POST['type'];
         $create_user = M('message_info')->where(array('id'=>$_POST['msgId']))->getField('create_user');//获取申请人ID
         $club = M('club_info')->where(array('ower_id'=>$user_id))->field('id,club_name')->find();
        if($type == 1){//同意加入
          $res = M('user')->where(array('user_id'=>$create_user))->setField('club_id',$club['id']);
          $content = 恭喜您成功加入.$club['club_name'];
          $this->addEmail('成员变动',$content,2,$create_user,$uid);
        }else{
          $content = 俱乐部.$club['club_name'].拒绝了您的请求;
          $this->addEmail(成员变动,$content,2,$create_user,$user_id);
         
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
          $user_id =$GLOBALS['token'][2];
          $user  = M('user_base')->alias('a')
                            ->join("dd_user u on a.id=u.user_id") //附表连主表
                            ->field("a.nickname,u.club_id")
                            ->where(array('a.id'=> $user_id))//需要显示的字段
                            ->find();
          $club = M('club_info')->where(array('id'=>$user['club_id']))->field('create_number,club_name,ower_id')->find();
          $quit = M('user')->where(array('user_id'=>$user_id))->setField('club_id',0);
          if($quit){
            $content = $user['nickname'].退出了.$club['club_name'].俱乐部;
            $this->addEmail('成员变动',$content,2,$club['ower_id'],$user_id);
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
       // $user_id =$GLOBALS['token'][2];
       $user_id =$GLOBALS['token'][2];
       $club_id = M('user')->where(array('user_id'=>$user_id))->getField('club_id');
       if(IS_POST){
          if(S('clubinfo_'.$club_id)){
               $info = S('clubinfo_'.$club_id);
          }else{
             
              $clubInfo = M('club_info')->where(array('id'=>$club_id))->find();
              $usernum  = M('user')->where(array('club_id'=>$club_id))->count();
              $active_point  = M('user')->where(array('club_id'=>$club_id))->sum('active_point');
              $is_club_owner =  M('user')->where(array('user_id'=>$user_id))->getField('is_club_owner');
              $headimg =  M('user_base')->where(array('id'=>$clubInfo['ower_id']))->getField('headimg');
              $info = array(
                          
                'club_id'     => $clubInfo['id'],           //俱乐部ID
                'club_name'   => $clubInfo['club_name'],    //俱乐部名称
                'club_head'   => $clubInfo['club_head'],    //俱乐部头像
                'club_role'   => $is_club_owner,            //俱乐部身份0
                'headimg'     => $headimg,                  //创建人头像
                'declaration' => $clubInfo['declaration'],  //宣言
                'area'        => $clubInfo['area'],         //地区
                'ercode'      => $clubInfo['ercode'],       //俱乐部二维码
                'ower_name'   => $clubInfo['ower_name'],    //创建人
                'club_notice' => $clubInfo['club_notice'],  //俱乐部公告
                'club_number' => $usernum.'/'. $clubInfo['create_number'],//俱乐部现有人数/俱乐部创建人数
                'active_point'=> $active_point,             //活跃度
                'create_time' => date('Y-m-d',strtotime($clubInfo['create_time'])) //创建时间
              );
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

      if(IS_POST){
          $user_id =$GLOBALS['token'][2];
          $club_id =  M('user')->where(array('user_id'=>$user_id))->getField('club_id');
          $members = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.id,a.nickname,a.headimg,u.is_club_owner,a.last_login_time,u.rank,u.active_point,u.fun_amount,u.fun_win_amount,u.match_amount,u.win_amount")
                        ->where(array('u.club_id' => $club_id))
                        ->order('id asc')
                        ->select();
          $data = array_column($members,'id');
         
          // $slime_id = $this->checkSlime();
          foreach($data as $k=>$v){
            $funscore = M('play_log')->where(array('user_id'=>$members[$k]['id']))->field('score')->select();//最高步数
            $playscore = M('fun_play_log')->where(array('user_id'=>$members[$k]['id']))->field('score')->select();//最高步数
            $score =   array_merge($funscore,$playscore);
            $members[$k]['score'] = max($score)['score'];
            $members[$k]['active_point'] = $members[$k]['active_point'];//活跃度
            $members[$k]['match_amount'] =$members[$k]['fun_amount']+$members[$k]['match_amount'];
            $members[$k]['win_amount'] =$members[$k]['fun_win_amount']+$members[$k]['win_amount'];
            $members[$k]['probability']  = round($members[$k]['win_amount']/$members[$k]['match_amount']*100,2)."%";
            $members[$k]['level']  = GameService::getDuan($members[$k]['rank'])['level'];  //段位
          }
          echo json_encode($members);
      }  
        
    }
    public function clubSet(){
     $user_id =$GLOBALS['token'][2];
     if(IS_POST){
        $club_id =  M('user')->where(array('user_id'=>$user_id))->getField('club_id');
        $clubinfo = M('club_info')->where(array('id'=>$club_id))->find();
        $data = array(
          'club_head'   => $_POST['club_head'],
          'ercode'      => $_POST['ercode'],
          'declaration' => $_POST['declaration'],
          'club_notice' => $_POST['club_notice']
        );
        if($_POST['club_head'] == null){
           $data['club_head'] = $clubinfo['club_head'];
        }
        if($_POST['ercode'] == null){
          $data['ercode'] = $clubinfo['ercode'];

        }
        $result = M('club_info')->where(array('id'=>$club_id))->save($data);
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
      $user_id =$GLOBALS['token'][2];
      if(IS_POST){
         $result = M('user')->where(array('user_id'=>$_POST['uid']))->setField('club_id',0);
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

        $userId =$GLOBALS['token'][2];
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

       if(IS_POST){
          $userId =$GLOBALS['token'][2];
          $msgId = $_POST['msgId'];
          $data = [
              'msg_id' => $msgId,
              'status' => 2,
              'modify_time' => NOW_TIME
          ];
          $res = M('message_log')->where(array('uid'=>$userId,'msg_id'=>$msgId))->save($data);

          if($res){
            echo json_encode(['status' => '1', 'msg' => '删除成功']);exit;
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

      // $uid =$GLOBALS['token'][2];
      $user_id = $GLOBALS['token'][2];
      $is_club_owner =  M('user')->where(array('user_id'=>$user_id))->getField('is_club_owner');
      $Model = new \Think\Model();
      // if($is_club_owner == 1){
      $infomation =  $Model->query("SELECT  o.id, o.title,o.content,o.create_time,o.category,o.create_user,   g.`status`   FROM   dd_message_info o   LEFT JOIN dd_message_log g ON o.id = g.msg_id    AND g.uid = ".$user_id."  WHERE   ( g.msg_id IS NULL or g.status='1') and  o.send_to  in ('all',".$user_id.") and o.category in(1,2) and o.send_time < CURRENT_TIMESTAMP AND o.expire_time > CURRENT_TIMESTAMP order by id desc;");
          $data = array_column($infomation,'id');
      foreach($data as $k=>$v){
        $id=$v['id'];
        
        $infomation[$k]['create_time']= $this->getTime($infomation[$k]['create_time']);

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
      $user_id= $GLOBALS['token'][2];;
      $slimeId = $_POST['slime_id'];

      $exp = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$slimeId))->getField('exp');
      $level = $this->s_level($exp)['level'];
      echo  $level;

    }


    /**
     * [slime description]
     * @Author   佳民
     * @DateTime 2019-06-18
     * @Function [史莱姆列表]
     * @return   [type]     [description]
     */
    public function slime(){
     
      $user_id = $GLOBALS['token'][2];

      $Model = new \Think\Model();

      $data =  $Model->query("SELECT  g.s_id,g.u_id,g.blue,g.blood,g.exp,o.id,o.name,o.skill,o.skill_introduction,o.slime_introduction FROM   dd_slime  o   LEFT JOIN dd_user_slime g ON o.id = g.s_id  AND g.u_id = ".$user_id."  order by o.id asc ;");
       
      

      $candys = M('user')->where(array('user_id'=>$user_id))->field('candy,candy1,candy2')->find();
      $candy =array($candys['candy'], $candys['candy1'],$candys['candy2']);
   
        // var_dump($candy);exit;
        echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data,'candy'=>$candy]);
    }

    public function addslime(){
       $user_id =$GLOBALS['token'][2];
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
    }
                                                     
    /**
     * [unlockSlime description]
     * @Author   佳民
     * @DateTime 2019-04-29
     * @Function [解锁史莱姆]
     * @return   [type]     [description]
     */
    public function unlockSlime(){
        $user_id =$GLOBALS['token'][2];
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
     * @DateTime 2019-06-18
     * @Function [升级史莱姆]
     * @return   [type]     [description]
     */
    public function upSlime(){
      $user_id = $GLOBALS['token'][2];
      if(IS_POST){
         $sid = $_POST['s_id']+1;
        
         $candy1  = (int)$_POST['candy'];
         $candy2  = (int)$_POST['candy1'];
         $candy3  = (int)$_POST['candy2'];
        $result = $this->hasCandy($user_id,$candy1,$candy2,$candy3,$sid);
        
        if($result){
          echo json_encode(['status'=>1,'msg'=>'升级成功']);
        }

      }
    }
  
     /**
     * [candyNum description]
     * @Author   佳民
     * @DateTime 2019-04-26
     * @Function [检测当前糖果数量]
     * @return   [type]     [description]
     */
  

    public function hasCandy($user_id,$candy,$candy1,$candy2,$sid){
    // $type = 1;
      $candyNum = M('user')->where(array('user_id'=>$user_id))->field('candy,candy1,candy2')->find();
      
      if($candy > (int)$candyNum['candy'] || $candy1 > (int)$candyNum['candy1'] || $candy2 > (int)$candyNum['candy2']){
           echo json_encode(['status'=>-1,'msg'=>'糖果不足']);exit;
      }else{
          $exp = ($candy*100) + ($candy1*200) + ($candy2*400);
        
          $candys = array(
            'candy' => $candyNum['candy'] - $candy,
            'candy1'=> $candyNum['candy1'] - $candy1,
            'candy2'=> $candyNum['candy2'] - $candy2 
          );
          $data = M('user')->where(array('user_id'=>$user_id))->save($candys); //扣除用户糖果数量
          $exps  = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('exp',$exp); //增加经验值  

          if($exps){
            return true;
          }else{
            return false;
          }

      }
   }
  

 /**----------------  俱乐部部分end    ---------------------**/

  /**
    * @param feedback
    * @return mixed
    * @name   用户反馈
  */
  public function feedback()
  { 
    $user_id = $GLOBALS['token'][2];
    $openid  = $GLOBALS['token'][1];
    $start = date('Y-m-d 0:0:0');
    $end   = date('Y-m-d 23:59:59') ;
      if(IS_POST){
        $num = M('feedback')->where(array('user_id'=>$user_id,'add_time'=>array('between',array($start,$end))))->count();
        if($num >= 20){
          echo json_encode(['status'=>-1,'msg'=>'今日反馈次数已达上限!']);exit;
        }
        $nickname = M('user_base')->where(['id'=>$user_id])->getField('nickname');
        $data = ['user_id'=>$user_id,'openid'=>$openid,'nickname'=>$nickname,'content'=>$_POST['content']];
        $result = M('feedback')->add($data);
        if($result){
          echo json_encode(['status'=>1,'msg'=>'感谢您的反馈,我们将尽快处理!']);exit;
        }
      }

  }



  /**
    * @param $gameNum
    * @return json
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
     * @param  $shenglv
     * @return json
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
    /**
     * @param  $score
     * @return json
     * @name   游戏综合评分
     */
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
    public function userInfo(){
      $user_id = $GLOBALS['token'][2];;
      if($user_id == null){
        echo "请先登录！";exit;
      }
      $zhScore = $this->zhScore($user_id);
      $playHistory = $this->playHistory($user_id);
      $funHistory = $this->funHistory($user_id);

      echo json_encode(['status'=>1,'msg'=>'返回成功','zhScore'=>$zhScore,'playHistory'=>$playHistory,'funHistory'=>$funHistory]);

    }
    //综合评分
    public function zhScore($user_id){
    
      $user = M('user')->where(array('user_id'=>$user_id))->field('match_amount,win_amount,fun_amount,fun_win_amount,club_id,rank')->find();
      $club_name = M('club_info')->where(array('id'=>$user['club_id']))->getField('club_name');
      $gameNum = $this->gameNum($user['match_amount'] + $user['fun_amount'])['level'];  //场次评分
      $game = $user['match_amount'] + $user['fun_amount']; //总场次
      $win = $user['win_amount'] + $user['fun_win_amount'];//胜场
      $sl = ($win/$game)*100;
      $probability = round(($win/$game)*100).'%';
      $intsl = floor($sl);
      $shenglv = $this->shenglv($intsl)['level']; //胜率评分
      $match = M('play_log')->where(array('user_id'=>$user_id))->sum('score');
      $fun   = M('fun_play_log')->where(array('user_id'=>$user_id))->sum('score');
      $steps = round(($match+$fun)/$game);
      $funscore  = M('play_log')->where(array('user_id'=>$user_id))->field('score')->select();//最高步数
      $playscore = M('fun_play_log')->where(array('user_id'=>$user_id))->field('score')->select();//最高步数
      $score    =  array_merge($funscore,$playscore);
      $allScore = $gameNum+$intsl+$user['rank'];
      $zhScore  = $this->score($allScore)['level'];
        $result = array(
          'club_name'   => $club_name,  //俱乐部名称
          'gameCount'   => $game,       //总场次
          'probability' => $probability,//胜率 
          'shenglv'     => $shenglv,    //胜率评分  
          'gameNum'     => $gameNum,    //场次评分 
          'score'       => max($score)['score'],      //最高步数
          'zhScore'     => $zhScore,     //综合评分
          'zhScore1'    => $allScore,     //综合评分数字
          // 'gameCount1'  => $game,  
          'aveSteps'    => $steps
        );

        return $result;
    }

   // 竞技赛历史战绩
   public function  playHistory($user_id){

      $play = M('play_log')->where(array('user_id'=>$user_id,'status'=>2))->field('rank,score,bonu,ranks,end_time,user_id')->order('end_time desc')->limit(20)->select();
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
        $datas[]  = $data;
      }
     return $datas;
   }
   //娱乐赛历史战绩
  public function funHistory($user_id){

      $play = M('fun_play_log')->where(array('user_id'=>$user_id,'status'=>2))->field('rank,score,end_time,user_id')->order('end_time desc')->limit(20)->select();  

      foreach ($play as $k => $v) {
        $userRank = M('user')->where(array('user_id'=>$v['user_id']))->getField('rank');
    
        $data = array(
          'end_time'  => date('m-d H:i',$v['end_time']),
          'score'     => $v['score'],
          'rank'      => $v['rank'],
        );
        $datas[]  = $data;
      }
      return $datas;
  }
   

   //用户收益记录
   public  function  userFlog(){
   
      $user_id =$GLOBALS['token'][2];
      $where = array(
        'parent1'=>$user_id,
        'parent2'=>$user_id,
        'parent3'=>$user_id,
        'id'     =>$user_id,
        '_logic' => 'or',
      );
   
      $users =  M('user_base')->where($where)->field('id')->select();   //下级人数
      $id = array_column($users, 'id');

      $today = strtotime(date("Y-m-d"),time()); //当天零点
      $todayEnd = $today+60*60*24;
      $weekStime = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
      $weekendtime = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
      $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
      $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));

      //当天新增
      $todaybonu = M('finance_log')->where(array('user_id'=>$user_id,'create_time'=>array('between',array($today,$todayEnd))))->sum('money');
      //当周
      $weekbonu  = M('finance_log')->where(array('user_id'=>$user_id,'create_time'=>array('between',array($weekStime,$weekendtime))))->sum('money');
      //当月
      $monthbonu = M('finance_log')->where(array('user_id'=>$user_id,'create_time'=>array('between',array($beginThismonth,$endThismonth))))->sum('money');

      $allbonu =  M('finance_log')->where(array('user_id'=>$user_id))->sum('money');
      $bouns = array(
        'todaybonu' => $todaybonu,
        'weekbonu'  => $weekbonu,
        'monthbonu' => $monthbonu,
        'allbonu'   => $allbonu,
      );
      $result = M('finance_log')->where(array('user_id'=>array('IN',array(implode(',',$id)))))->field('user_id,money,create_time')->order('id desc')->select();
      foreach ($result as $v) {
        $username = M('user_base')->where(array('id'=>$v['user_id']))->getField('nickname');
       $data = array(
        'username' => $username,
        'money'    =>ceil($v['money']),
        'content'  => $username.'获得了'.ceil($v['money']).'气泡',
       );
       $datas[] = $data; 
      }
      // array_merge($datas,$bonus);
     echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$datas,'bonus'=>$bouns]);
    
   }

   public function share(){
      $user_id =$GLOBALS['token'][2];
      if($user_id == null){
        echo "请先登录！";exit;
      }
      $share = M('user')->where(array('user_id'=>$user_id))->getField('share');
      $nowUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
      if(IS_POST){
        $type = $_POST['type'];
        $results = $this->shareType();
        
        if($type == 2){
          if($share < 1){
            echo json_encode(['status'=>-1,'msg'=>'分享次数不足']);exit;
          }
        }
        $data= array(
          'user_id' => $user_id,
          'action'  => $nowUrl,
          'type'    => $type,
          'create_time' => date('Y-m-d H:i:s')
        );
        $result = M('action_log')->add($data);
        if($type == 2 && $result){ //娱乐赛分享 
            M('user')->where(array('user_id'=>$user_id))->setDec('share',1);
            M('user')->where(array('user_id'=>$user_id))->setInc('stamina',3);
            
        }
        echo json_encode(['status'=>1,'msg'=>'分享成功','data'=>$results]);exit;
      }
   }

    public function shareType(){
      $result = M('share')->limit(1)->order('rand()')->find();
      
      return $result;
    }

    public function advert(){
    	$user_id = $GLOBALS['token'][2];
    	if($user_id == null){
        	echo "请先登录！";exit;
      	}
    	$nowUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
	   	if(IS_POST){
	   	    $type   = $_POST['type'];
	   	    $status = $_POST['status'];
	   	    $data= array(
	          'user_id' => $user_id,
	          'action'  => $nowUrl,
	          'type'    => $type,
	          'status'  => $status, 
	          'create_time' => date('Y-m-d H:i:s')
	        );
	        $result = M('action_log')->add($data);
	   	 	if($type == 6 && $status == 2){//娱乐赛广告
	   	 		M('user')->where(array('user_id'=>$user_id))->setInc('stamina',6);
	   	 		echo json_encode(['status'=>1,'msg'=>'观看成功']);exit;
	   	 	}else if($type == 7 && $status == 2){
	   	 		M('user')->where(array('user_id'=>$user_id))->setInc('money',5);
	   	 		echo json_encode(['status'=>1,'msg'=>'观看成功']);exit;
	   	 	}else{
	   	 		echo json_encode(['status'=>-1,'msg'=>'未观看完视频']);
	   	 	}
	   	}
    }

//排行榜
    public function rankList(){

      
      	$data = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field('a.nickname,a.headimg,u.rank')
                        ->limit(0,10)
                        ->order('u.rank desc')
                        ->select();
      	foreach ($data as $k => $v) {
	        $data[$k]['rank']   = (int)$v['rank'];
	        $data[$k]['level']   = GameService::getDuan($v['rank'])['level'];  //段位;
      	} 
   
        
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
      
    }
     public function rankLists(){

      if($_POST['type'] == 1){
      	$data = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field('a.nickname,a.headimg,u.money,u.rank')
                        ->limit(0,10)
                        ->order('u.money desc')
                        ->select();
      	foreach ($data as $k => $v) {
	        $data[$k]['money']   = (int)$v['money'];
      	}  
      }else{
      	
      	$data = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field('a.nickname,a.headimg,u.rank')
                        ->limit(0,10)
                        ->order('u.rank desc')
                        ->select();
      	foreach ($data as $k => $v) {
	        $data[$k]['rank']   = (int)$v['rank'];
	        $data[$k]['level']   = GameService::getDuan($v['rank'])['level'];  //段位;
      	}
      }  
        
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
      
    }

    /**商品相关**/
    // public function product(){
    //   $data = M('product')->field('number,type,price')->order('id desc')->select();
    //   echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
    // }



    /**         新增功能开始                **/
        //离线时间
        public function leaveTime(){
            $user_id = $GLOBALS['token'][2];
            $data = M('user_base')->alias('a')
                                ->join("dd_receive u on a.id=u.user_id") //附表连主表
                                ->field('a.last_login_time,u.receive_time')
                                ->where(array('u.user_id'=>$user_id))
                                ->find();
                                
            $start = strtotime($data['receive_time']);
            $end   = strtotime($data['last_login_time']);
            $leaveTime = $end - $start;
            if($leaveTime >= 172800){
              $leaveTime = 172800;
            }
            echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$leaveTime]);exit;          
        }

        //用户金币
        public function currency(){
            $user_id = $GLOBALS['token'][2];
            $data = M('user')->where(array('user_id'=>$user_id))->field('money,stamina,crystal')->find();
            echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);exit;
        }
       //用户收益领取
        public function receive(){
            $user_id = $GLOBALS['token'][2];
            if(IS_POST){
              $money = $_POST['money'];
              $type  = $_POST['type'];
              if($type == 1){
                $rmoney = $money;
              }elseif($type == 2){
                $rmoney = $money*2;
              }else{
                $rmoney = $money*4;
              }
              $data = M('user')->where(array('user_id'=>$user_id))->setInc('money',$rmoney);
              if($data){
                echo json_encode(['status'=>1,'msg'=>'领取成功']);exit;
              }else{
                echo json_encode(['status'=>1,'msg'=>'领取失败']);exit;
              }
            }
            
        }

        //购买商品
        public function buy(){
            if(IS_POST){ 
              $number  = $_POST['number'];
              $type    = $_POST['type'];
              $price   = intval($_POST['price']);
              $user_id = $GLOBALS['token'][2];  
              
              if(empty($_POST['number']) || empty($_POST['type']) || empty($_POST['price'])){
                echo json_encode(['status'=>-2,'msg'=>'参数错误']);exit;
              }
              $product = M('product')->where(array('number'=>$number,'type'=>$type))->find();
              if(!$product){
               echo json_encode(['status'=>-1,'msg'=>'商品不存在']);exit;
              }
              $crystal = M('user')->where(array('user_id'=>$user_id))->getField('crystal');
              if($crystal>=$price){
                if($type == 1){//购买史莱姆
                  M('user_slime')->where(array('s_id'=>$number+1,'u_id'=>$user_id))->setfield('is_lock',1);        
                  
                }elseif($type == 2){//购买饰品
                   $robe = M('robe')->where(array('user_id'=>$user_id))->find();
                    $result = array(
                    'user_id' => $user_id,
                    'hat'     => $robe['hat'].$number.','
                  );
                   if($robe){
                        M('robe')->where(array('user_id'=>$user_id))->save($result);
                   }else{
                        M('robe')->add($result);
                   }
                }else{
                  echo json_encode(['status'=>-2,'msg'=>'参数错误']);exit;
                }
                    $crystals = M('user')->where(array('user_id'=>$user_id))->setDec('crystal',$price);
                    if($crystals){
                      $buyinfo = array(
                        'user_id' => $user_id,
                        'p_num'   => $number,
                        'type'    => $type,
                        'money'   => $price
                      );
                      $buy_log = M('buy_log')->add($buyinfo);
                      if($buy_log){
                        echo json_encode(['status'=>1,'msg'=>'购买成功']);exit;
                      }            
                    }
                 
              }else{
                  echo json_encode(['status'=>-3,'msg'=>'您的钻石不足']);exit;
              }
                   
            }
        }

         /**商品相关**/
          public function product(){
            $data = M('product')->field('number,type,price')->order('id desc')->select();
            echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
          }
          //史莱姆列表
          public function slimeList(){
              $user_id = $GLOBALS['token'][2];
              $slime = M('user_slime')->where(array('u_id'=>$user_id))->field('s_id,is_lock,level,is_check,hat')->select();
              foreach ($slime as $k => $v) {
                $slime[$k]['s_id'] = $slime[$k]['s_id'] - 1 ;
              }
              $slime_robe = M('robe')->where(array('user_id'=>$user_id))->field('user_id,hat')->find();   
              $slime_robe['hat'] = explode(',', $slime_robe['hat']);
              $is_check =  M('user_slime')->where(array('u_id'=>$user_id,'is_check'=>1))->getField('s_id');  
              $data = array('slime_list'=>$slime,'slime_robe'=>$slime_robe,'is_check'=>intval($is_check)-1);
              echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
          }
      //升级史莱姆
          public function upSlimes(){
            $user_id = $GLOBALS['token'][2];
            if(IS_POST){
              $sid = $_POST['s_id']+1;
              $price  = intval($_POST['price']);
              $money = M('user')->where(array('user_id'=>$user_id))->getField('money');
            
              if($price > $money){
                echo json_encode(['status'=>-1,'msg'=>'气泡不足']);exit;
              }else{
                  
                $data = M('user')->where(array('user_id'=>$user_id))->setDec('money',$price); //扣除用户糖果数量
                $up = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('level',1); //增加经验值  

                if($up){
                   echo json_encode(['status'=>1,'msg'=>'升级成功']);exit;

                }else{
                   echo json_encode(['status'=>-2,'msg'=>'升级失败']);exit;
                }
              }
            }
          }
          //选择出战slime
          public function checkSlimes(){
            if(IS_POST){
              $slime_id = $_POST['slime_id']+1;
              $user_id  = $GLOBALS['token'][2];
              $result = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$slime_id))->setfield('is_check',1);
              if($result){
                $is_check = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>array('neq',$slime_id)))->setfield('is_check',0);
                if($is_check){
                  echo json_encode(['status'=>1,'msg'=>'切换成功']);exit;
                }else{
                  echo json_encode(['status'=>1,'msg'=>'切换失败']);exit;
                }
              }
            }
          } 
          //装饰饰品
          public function checkRobe(){
            if(IS_POST){
              $slime_id = $_POST['slime_id']+1;
              $user_id = $GLOBALS['token'][2];
              $data = array(
                'hat'=>$_POST['hat']
              );
              $hat = M('robe')->where(array('user_id'=>$user_id))->getField('hat');
              if(!$hat){
                echo json_encode(['status'=>-1,'msg'=>'暂无该饰品']);exit;
              }else{
                $result = M('user_slime')->where(array('s_id'=>$slime_id,'u_id'=>$user_id))->save($data);
                if($result){
                  echo json_encode(['status'=>1,'msg'=>'装扮成功']);exit;
                }
              }
            }
          }


          public function   chou(){
            $user_id  = $GLOBALS['token'][2];
            if(IS_POST){
              $crystal = intval($_POST['crystal']);
              $money =M('user')->where(array('user_id'=>$user_id,'crystal'=>array('EGT',$crystal)))->setDec('crystal',$crystal);
              // $money = M('user')->where(array('user_id'=>$user_id))->getField('crystal');
       
              if($money==0){
                echo json_encode(['status'=>-1,'msg'=>'水晶不足']);exit;
              }else{
                // M('user')->where(array('user_id'=>$user_id))->setDec('crystal',$crystal);
                $rand = rand(1,100);
                $daytime = strtotime(date("Y-m-d",time()));
               
                $shouru = M('zhuan')->where(array('addtime'=>array('gt',$daytime)))->sum('money'); 
                $zhichu = M('zhuan')->where(array('addtime'=>array('gt',$daytime)))->sum('ying');
           
                $jian = $crystal*2;
             
                $lirun = ($shouru-($zhichu+$jian))/$shouru-((2-rand(1,3))*rand(1,10)/1000); 
                // var_dump($GLOBALS['_CFG']['hongbao']);
                // var_dump($lirun);
         
                if($lirun < $GLOBALS['_CFG']['hongbao']['lirun']*1){
                  $rand = rand(1,100*($GLOBALS['_CFG']['hongbao']['zhong01']+$GLOBALS['_CFG']['hongbao']['zhong05']));
                  // var_dump($rand);
                }
                
                if($rand<=$GLOBALS['_CFG']['hongbao']['zhong01']*100){
                  // var_dump($GLOBALS['_CFG']['hongbao']['zhong01']*100);
                   $zhuan = rand(275,355);
                   $ying = 0.1 * $crystal;
                }
                if( ($rand>$GLOBALS['_CFG']['hongbao']['zhong01']*100) && ($rand <= ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100) )){
                  // var_dump($GLOBALS['_CFG']['hongbao']['zhong01']*100);
                  // var_dump($GLOBALS['_CFG']['hongbao']['zhong05']*100);
                  // exit;
                   $zhuan = rand(185,265);
                   $ying = 0.5 * $crystal;
                }
                if( ($rand> ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100) )  && $rand<= ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100+$GLOBALS['_CFG']['hongbao']['zhong21']*100)  ){
                   $zhuan = rand(95,175);
                   $ying = 2.1 * $crystal;
                }
                if($rand>($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100+$GLOBALS['_CFG']['hongbao']['zhong21']*100)&&$rand<=100){
                   $zhuan = rand(5,85);
                   $ying = 3.6 * $crystal;
                }

            
              }
              $add['uid']  = $user_id;   
              $add['type'] = 1;   
              $add['money']= $crystal;   
              $add['ying'] = $ying;   
              $add['rand'] = $rand;     
              $add['addtime'] = time();  
              $result = M('zhuan')->add($add); 
              if($result){
                
                M('user')->where(array('user_id'=>$user_id))->setInc('crystal',$ying);    
              }
              echo json_encode(['status'=>1,'msg'=>$ying]);exit;
            }

          }
          //抢钻石
      public function isTime($now,$user_id){
        $times =  [
              ['start' => strtotime(date('Y-m-d 12:00:00')),'end'=>strtotime(date('Y-m-d 12:30:00'))],
              ['start' => strtotime(date('Y-m-d 18:00:00')),'end'=>strtotime(date('Y-m-d 18:30:00'))],
        ];
        $now = strtotime(date('Y-m-d H:i:s'));
        $robbone = M('robbing')->where(array('user_id'=>$user_id,'time'=>array('between',array($times[0]['start'],$times[0]['end']))))->find();
        $robbtwo = M('robbing')->where(array('user_id'=>$user_id,'time'=>array('between',array($times[1]['start'],$times[1]['end']))))->find();
        // var_dump($robbone);
        // var_dump($robbtwo);exit;
          if($times[0]['start'] <= $now && $now <= $times[0]['end']){
            if($robbone){
              echo json_encode(['status'=>-2,'msg'=>'您已参与过,请下次再来']);exit;
             
            }else{

              // echo json_encode(['status'=>-2,'msg'=>'啊啊']);exit;
              return ture;        
            }
          }elseif($times[1]['start'] <= $now && $now <= $times[1]['end']){
            if($robbtwo){
              echo json_encode(['status'=>-4,'msg'=>'您已参与过,请下次再来']);exit;
              
            }else{
              // echo json_encode(['status'=>-2,'msg'=>'压抑']);exit;
              return ture;       
            }

          }else{
             echo json_encode(['status'=>-1,'msg'=>'暂未开放']);exit;
              // return false;
          }
      
        
                
      }
      public function robbing(){
        $user_id  = $GLOBALS['token'][2];
        if(IS_POST){
          $time = $this->isTime($now,$user_id);
          $type    = intval($_POST['type']);
          // $crystal = intval($_POST['crystal']);
          $crystal = S('robbing'.$user_id);
          // var_dump($crystal);exit;
          if($time){
              if($type == 1){ //开始
                $crystal = rand(10,100);
                S('robbing'.$user_id,$crystal,1800);
                echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$crystal]);exit;                  
              }else{//结束       
                if($crystal == null){
                  echo json_encode(['status'=>-3,'msg'=>'参数错误']);exit;
                } 
                 $data = array(
                  'user_id' => $user_id,
                  'time'    => time(),
                  'crystal' => $crystal
                );
                M('robbing')->add($data);
                M('user')->where(array('user_id'=>$user_id))->setInc('crystal',$crystal);
                S('robbing'.$user_id,null);
              }
              echo json_encode(['status'=>2,'msg'=>'恭喜获得','data'=>$crystal]);exit;
          }
          // else{
          //    echo json_encode(['status'=>-1,'msg'=>'暂未开放']);exit;
          // }
        }
    }
        



     
          
}
