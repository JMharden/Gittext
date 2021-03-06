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
    function accuLogin($openid,$user_id){
        if ($openid) {
            $ween = date('w');
            if($ween==0){
                $ween=7;
            }
            $start = date("Y-m-d 00:00:00",strtotime("-".($ween-1)."day"));
            $end = date("Y-m-d 23:59:59",strtotime("+".(7- $ween)."day"));
            // var_dump($start);var_dump($end);
            $nowstart = date("Y-m-d 00:00:00");
            $nowend = date("Y-m-d 23:59:59");
          	// var_dump($nowstart);var_dump($nowend);
            $maxAccDay=1;

            $data= M('login_reward')->where(array("openid" => $openid,'user_id'=>$user_id,"create_date"=>array('between',array($start,$end))))->order('create_date asc')->select();

            $allLogin= M('login_reward')->where(array("openid" => $openid,'user_id'=>$user_id,"create_date"=>array('between',array($start,$end))))->count();//统计当周一登陆几天
         
            $datas= M('login_reward')->where(array("openid" => $openid,'user_id'=>$user_id,"create_date"=>array('between',array($nowstart,$nowend))))->find();
            // var_dump($allLogin);exit;
            //判断当天是否已经登陆过
            if($datas){
                $maxAccDay = $datas['accu_login_days'];
            }else {
                // if($datas){
                    $maxAccDay = $allLogin+1;
                // }
                $reward =  $this->getLoginRewardList($maxAccDay);
                $addData =[
                    'accu_login_days'=>$maxAccDay,
                    'create_date'=>date("Y-m-d H:i:s"),
                    'draw_time'=> null,
                    "expire_date"=>$end,
                    'is_draw'=>'N',
                    'openid'=>$openid,
                    'reward_num'=>$reward['num'],
                    'reward_type'=>$reward['type'],
                    'user_id'=>$user_id
                ];
                $last = M('login_reward')->add($addData);
                $addData['id']=$last;
                $data[] = $addData;
                // var_dump($data);exit;
            }
            //就剩余活动天数的数据返回
            while($maxAccDay<7){
                $maxAccDay=$maxAccDay+1;
                $dayreward = $this->getLoginRewardList($maxAccDay);
                $list[]=[
                    'accu_login_days'=>$maxAccDay,
                    'reward_num'=>$dayreward['num'],
                    'reward_type'=>$dayreward['type'],
                    'is_draw'=>'X'
                ]; 
            }


             return  array_merge($data,$list);
             
        }
        return null;
    }

    /**
     * 获取累计登陆天数对应的奖励
     * @param $loginDays
     * 第一天获取5个candy1  第二天获取10个candy1  第三天获取20个candy1  第四天获取  5个candy2   第五天获取 10个  candy2   第六天获取  15个candy2   第七天获取 10个candy3
     * @return  返回累计登陆天数对应的奖励
     */
    function getLoginRewardList($loginDays){
        $list =[
            1=>['num'=>15,'type'=>'crystal'],
            2=>['num'=>25,'type'=>'crystal'],
            3=>['num'=>35,'type'=>'crystal'],
            4=>['num'=>50,'type'=>'crystal'],
            5=>['num'=>60,'type'=>'crystal'],
            6=>['num'=>60,'type'=>'crystal'],
            7=>['num'=>75,'type'=>'crystal']
        ];
        if($loginDays>0&&$loginDays<8){
            return $list[$loginDays];
        }
        return [];

    }


    /**
     * 累计登陆领取逻辑
     * @param $userId
     * @param $activityId    对应login_reward表中的主键id
     * @return array|null
     */
    function fetchLoginReward($userId,$activityId)
    {
        //判断领取的奖励是否存在，是否过期
      $data = M('login_reward')->where(array("user_id"=>$userId,'id'=>$activityId,'is_draw'=>'N',"expire_date"=>array("EGT", date("Y-m-d"))))->find();
        if($data){
            //更新领取记录
            M('login_reward')->where(array('id'=>$activityId))->save(array("is_draw"=>"Y","draw_time"=>date("Y-m-d H:i:s")));
            //更新用户奖励
            $num  = $data['reward_num'];
            $type = $data['reward_type'];
            if($num&&$type){
                $result = M('user')->where(array("user_id"=>$userId))->setInc($type,$num);
                //todo 需要记录糖果领取日志，暂时不做
                if($result){
                    return ["rewardType"=>$type,'reward_num'=>$num];
                }
            }

        }else{
            return null;
        }

    }


}
