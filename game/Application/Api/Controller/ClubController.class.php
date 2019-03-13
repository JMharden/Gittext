<?php

namespace Api\Controller;

use Think\Controller;

/**
 * Class ClubController
 * @package 俱乐部功能模块
 */
class ClubController extends ApiController
{
    // public function _initialize()
    // {
    //    // if(S('123123') == null ){
    //    //  echo json_encode(['status' => '403', 'message' => 'request forbidden']);
    //   //       exit; 
    //    // }
    //   $this->_load_config();
    //     $token = $_POST['token'];

    //     if ($token == null || S($token) == null) {
    //         echo json_encode(['status' => '403', 'message' => 'request forbidden']);
    //         exit;
    //     }
    //     // var_dump(S($token));exit;
    //     $uid =S($token)[2];
    //    if(S('user_info_'.$uid)){

    //        // $userInfo = M('user')->where(array('id'=>$uid))->find();
            // $user  = M('user')->where(array('id'=>$uid))->field('id,nickname,money2,headimg,integration,empiric,active_point')->find();
    //         $scene = M('play_log')->where(array('user_id'=>$uid))->count();
    //         $win   = M('play_log')->where(array('user_id'=>$uid,'result'=>'赢'))->count();
    //         $probability =round($win/$scene*100,2)."%";

    //         $grade = $this->grade($win);
    //         $userInfo = array(
    //             'id'       => $user['id'] ,
    //             'openid'    =>$user['openid'],
    //             'club_id'    =>$user['club_id'],
    //             'club_role'    =>$user['club_role '],
    //             'nickname' => $user['nickname'],
    //             'money'   => $user['money'],
    //             'headimg'  => $user['headimg'],
    //             'empiric'  => $user['empiric'],//经验值
    //             'active' => $user['active_point'],//活跃度
    //             'inter'    => $user['integration'],//积分
    //             'grade'    => $grade,//段位
    //             'probability' =>$probability,//胜率
                
    //         );
       
    //        if(!$userInfo){
    //            echo json_encode(['status' => '403', 'msg' => 'userInfo not find']);
    //            exit;
    //        }
    //        //后面有接口要取用户信息（推荐关系啥的）直接从缓存里拿就行
    //        S('user_info_'.$uid, $userInfo,18000);//用户信息存入Redis
    //    }
    // }

 
    

  /**----------------  俱乐部部分start    ---------------------**/
      /**
     * [createClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [创建俱乐部]
     * @return   [type]     [description]
     */
    public function createClub(){
       // $uid =S($_POST['token'])[2];
       $uid = 183;
       $subordinate  =  M('user')->where(array('parent1'=>$uid))->count();   //下级人数
       $userInfo =  M('user')->where(array('id'=>$uid))->getField('active_point,nickname,money'); //活跃点
       $username     =  M('user')->where(array('id'=>$uid))->getField('nickname');    
       // var_dump($userInfo);exit;
       echo json_encode($userInfo['money']);
       if(IS_POST){
          if($subordinate < 1 || $userInfo['active_point'] <100){
              echo json_encode(array('status'=>-1,'msg'=>'对不起,暂无资格创建俱乐部'));
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
                  $club = M('club_info')->where(array('club_name'=>$_POST['club_name']))->find();
                  M('user')->where(array('id'=>$uid))->save($data);
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
     * @return   [type]     [description]
     */
    public function clubs(){
        // if(IS_POST){

      
          $club_name = $_POST['club_name'];
          $aWhere['club_name'] = array('like','%'.$club_name.'%');
          // if($club_name == null ){
            // $club = M('club_info')->field('id,club_name,club_head,ower_name,create_number')->select();
          // }else{
            $club = M('club_info')->field('id,club_name,club_head,ower_name,create_number')->where($aWhere)->select();
          // }
          
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
     * @return   [type]     [description]
     */
    public function clubInfo(){
      // var_dump(S('clubinfo_5'));exit;
       // $uid =S($_POST['token'])[2];
       // $club_id = S('user_info_'.$uid)[2];
       $club_id = $_POST['club_id'];
       if(IS_POST){
          if(S('clubinfo_'.$club_id)){
            // echo "213";exit;
               $info = S('clubinfo_'.$club_id);
          }else{
              $clubInfo = M('club_info')->where(array('id'=>$club_id))->find();
              $usernum  = M('user')->where(array('club_id'=>$club_id))->count();
              $active_point  = M('user')->where(array('club_id'=>$club_id))->sum('active_point');
              $userInfo =  M('user')->where(array('id'=>$clubInfo['ower_id']))->field('club_role,headimg')->find();
              // var_dump($userInfo);exit;
              $info = array(
                'club_name' => $clubInfo['club_name'],//俱乐部名称
                'club_head' => $clubInfo['club_head'],//俱乐部图标
                'club_role' => $userInfo['club_role'],   //俱乐部身份
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
       } 
        S('clubInfo_'.$club_id,$info,18000);
        echo json_encode(array('status'=>1,'msg'=>'俱乐部信息返回成功','data'=>$info));
    }

    /**
     * [fingClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [查找俱乐部]
     * @return   [type]     [description]
     */
    // public function findClub(){
    //     // if(IS_POST){
    //   $club_name = "l";
    //         $aWhere['name'] = array('like','%'.club_name.'%');
    //         $data =  M('club_info')->where($aWhere)->getField('id,club_name,ower_name,club_head');
    //         var_dump($data);exit;
    //         $usernum  = M('user')->where(array('club_id'=>$data['id']))->count();
    //         $active_point  = M('user')->where(array('club_id'=>$data['id']))->sum('active_point');
    //         $res = array(
    //             'club_id' => $data['id'],
    //             'club_head' => $data['club_head'],
    //             'club_name' => $data['club_name'],
    //             'ower_name' => $data['ower_name'],
    //             'club_number' => $usernum.'/'. $data['create_number'],
    //             'active_point' => $active_point,
    //         );
    //         if($result){
    //             echo json_encode(array('status'=>1,'msg'=>'搜索成功','data'=>$res));
    //         }
    //     // }
    // }


    /**
     * [joinClub description]
     * @Author   佳民
     * @DateTime 2019-02-13
     * @Function [加入俱乐部]
     * @return   [type]     [description]
     */
    public function joinClub(){
        $uid = 183;
        if(IS_POST){
          $number = M('club_info')->where(array('id'=>$_POST['club_id']))->getField('create_number');
          $club_num = M('user')->where(array('club_id'=>$_POST['club_id']))->count();
          if($number == $club_num){
            echo json_encode(['status'=>-1,'msg'=>'人数已达上限']);exit;

          }else{

            $club_id = M('user')->where(array('id'=>$uid))->getField('club_id');
            if($club_id != null || $club_id != 0){
              echo json_encode(['status'=>-2,'msg'=>'你当前已有俱乐部']);exit;
            }else{
              $data = array(
                'user_id' => $uid,
                'club_id' => $_POST['club_id'],
                'status'  => 1,
                'type'    => 1,
              );
              $result = M('club_infomation')->add($data);
              if($result){
                echo json_encode(['status'=>1,'msg'=>'申请成功']);exit;
              }
            }

          }
        } 
    }
    /**
     * [clubRecords description]
     * @Author   佳民
     * @DateTime 2019-03-06
     * @Function [俱乐部记录]
     * @return   [type]     [description]
     */
    public function clubRecords(){
      $infomation=M('club_infomation')->alias('c')
                ->join("dd_user u on c.user_id=u.id") //附表连主表
                ->field("u.nickname,c.status,c.user_id,c.time,c.type")
                ->where(array('status'=>1))//需要显示的字段
                ->select();
      
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$infomation]);
      
    }
    /**
     * [clubMembers description]
     * @Author   佳民
     * @DateTime 2019-01-25
     * @Function [俱乐部成员列表]
     * @return   [type]     [description]
     */
    public function  clubMembers(){
      // $uid =S($_POST['token'])[2];
        $uid = 182;
        $club_id = M('user')->where(array('id'=>$uid))->getField('club_id');

        $clubMember = M('user')->where(array('club_id'=>$club_id))->select();
        // $number = M('user')->where(array('club_id'=>$club_id))->count();
        // array_push($clubMember, $number);
        
        echo json_encode($clubMember);
       
    }


  /**----------------  俱乐部部分end    ---------------------**/



 

 
    
  


}