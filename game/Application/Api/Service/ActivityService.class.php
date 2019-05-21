<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhuhangan
 * Date: 2019/3/7
 * Time: 17:40
 */

namespace Api\Service;


use Think\Exception;

class ActivityService
{
    /**
     * 累计登陆逻辑
     * @return  按用户分组聚合
     */
    function accuLogin($userId){
        if ($userId) {
            $ween = date('w');
            if($ween==0){
                $ween=7;
            }
            $start = date("Y-m-d",strtotime("-".$ween."day"));
            $end = date("Y-m-d",strtotime("+".(7- $ween)."day"));
            date("Y-m-d",strtotime("+".(7- date('w'))."day"));
            date('w');
            date_add() ;
            $maxAccDay=1;
           $data= M('login_reward')->where(array("user_id" => $userId,"create_date"=>array('GT',$start)))->order('create_date desc')->select();
          //判断当天是否已经登陆过
           if($data&& $data[0]['create_date']==date("Y-m-d")){
               return  $data;
           }else if($data){
               $maxAccDay = $data[0]['accu_login_days']+1;
           }
           $addData =[
                "user_id"=>$userId,
                'accu_login_days'=>$maxAccDay,
                'reward'=>$maxAccDay*20,
                "create_date"=>date("Y-m-d"),
                'expire_date'=>$end,
                'is_draw'=>'N'
            ];
            $last = M('login_reward')->add($addData);
            $addData['id']=$last;
            return array_merge($addData,$data);
        }
        return null;
    }
    /**
     * 累计登陆领取逻辑
     * @return  按用户分组聚合
     */
    function fetchLoginReward($userId,$activityId)
    {
        //判断领取的奖励是否存在，是否过期
        $date = M('login_reward')->where(array("user_id"=>$userId,id=>$activityId,'is_draw'=>'N',"expire_date"=>array("EGT", date("Y-m-d"))))->find();
        if($date){
          return   M('login_reward')->where(array(id=>$activityId))->save(array("is_draw"=>"Y","draw_time"=>date("Y-m-d")));
        }else{
            return null;
        }

    }


}