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
                $maxAccDay = $data[0]['accu_login_days'];
            }else {
                if($data){
                    $maxAccDay = $data[0]['accu_login_days']+1;
                }
                $reward =  $this->getLoginRewardList($maxAccDay);
                $addData =[
                    "user_id"=>$userId,
                    'accu_login_days'=>$maxAccDay,
                    'reward_num'=>$reward['num'],
                    'reward_type'=>$reward['type'],
                    "create_date"=>date("Y-m-d"),
                    'expire_date'=>$end,
                    'is_draw'=>'N'
                ];
                $last = M('login_reward')->add($addData);
                $addData['id']=$last;
                $data = array_merge($addData,$data);
            }
            //就剩余活动天数的数据返回
            while($maxAccDay<7){
                $maxAccDay=$maxAccDay+1;
                $dayreward = $this->getLoginRewardList($maxAccDay);
                $list[]=[
                    'accu_login_days'=>$maxAccDay,
                    'reward_num'=>$dayreward['num'],
                    'reward_type'=>$dayreward['type'],
                    'status'=>'N'
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
            1=>['num'=>5,'type'=>'candy'],
            2=>['num'=>10,'type'=>'candy'],
            3=>['num'=>20,'type'=>'candy'],
            4=>['num'=>5,'type'=>'candy1'],
            5=>['num'=>10,'type'=>'candy1'],
            6=>['num'=>15,'type'=>'candy1'],
            7=>['num'=>10,'type'=>'candy2']
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
        $data = M('login_reward')->where(array("user_id"=>$userId,id=>$activityId,'is_draw'=>'N',"expire_date"=>array("EGT", date("Y-m-d"))))->find();
        if($data){
            //更新领取记录
            M('login_reward')->where(array(id=>$activityId))->save(array("is_draw"=>"Y","draw_time"=>date("Y-m-d")));
            //更新用户奖励
            $num = $data['reward_num'];
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
