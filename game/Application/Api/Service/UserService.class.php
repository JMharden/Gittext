<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhuhangan
 * Date: 2019/3/7
 * Time: 17:40
 */

namespace Api\Service;


use Think\Exception;

class UserService
{
    /**
     * @param $user
     * @return mixed|null
     */
    function  addUser($user,$openid){
        if(!$openid){
            return null;
        }
          $userId =  M('user_base')->add($user);
          $userExtr =[
            'user_id'=>$userId,
            'create_time'=>time(),
            'openid'    =>$openid
          ];
           M('user')->add($userExtr);
            //清除缓存
           S('user_info_base' . $userId,null);
           S('user_info_full' . $userId,null);
          // $user = UserService::getUserFullInfo($userId);           
          $user['id']=$userId;
          return $user;
    }


    /**
     * 获取用户基础信息
     * @return  按用户分组聚合
     */
    function getUserBaseInfo($userId)
    {
        if ($userId) {
            $userInfo = S('user_info_base' . $userId);
            if ($userInfo) {
                return $userInfo;
            } else {
                $user = M('user_base')->where(array('id' => $userId))->find();
                if($user){
                  $openid = $user['openid'];
                    S('user_info_base' .$openid , $userInfo, 18000);//用户信息存入Redis
                }
                S('user_info_base' . $userId, $userInfo, 18000);//用户信息存入Redis
                return $user;
            }
        }
        return null;
    }
    function getUserBaseInfoByOpen($openId)
    {
        if ($openId) {
            $userInfo = S('user_info_base' . $openId);
            if ($userInfo) {
                return $userInfo;
            } else {
                $user = M('user_base')->where(array('openid' => $openId))->find();
                if($user){
                    $userId = $user['userId'];
                    S('user_info_base' . $userId, $userInfo, 18000);//用户信息存入Redis
                }
                S('user_info_base' .$openId , $userInfo, 18000);//用户信息存入Redis
                return $user;
            }
        }
        return null;
    }
    function getUserFullInfoByOpen($openId)
    {
        if ($openId) {

            $userBase = $this->getUserBaseInfoByOpen($openId);
            
            if ($userBase) {

                $openid = $userBase['openid'];
                $userExtr = M('user')->where(array('openid' => $openid))->find();

                $rank = GameService::getDuan($userExtr['rank']);
                $userInfo = [
                    'is_club_owner' => $userExtr['is_club_owner'],
                    'money' => floor($userExtr['money']),
                    'slimeIndex' => 0,
                    'club_id' => $userExtr['club_id'],
                    'advert'  => $userExtr['advert'],
                    'stamina' => $userExtr['stamina'],
                    'area'    => $userExtr['area'],
                    'share'   => $userExtr['share'],
                    'rank'    => $rank['level'],
                    'ranks'   => $rank['max'] - $rank['min'],
                    'rankNum' => $userExtr['rank'] -$rank['min'],
                    'probability' => round(($userExtr['win_amount']/$userExtr['match_amount'])*100)."%",

                ];

                if($userExtr){
                    return array_merge($userBase,$userInfo);
                }else{
                    return $userBase;
                }
            }
        }
        return null;

    }
    function getUserFullInfo($userId)
    {
        if ($userId) {
            $userInfo = S('user_info_full' . $userId);
            if($userInfo){
                return $userInfo;
            }
            $userBase = $this->getUserBaseInfo($userId);
            if ($userBase) {
                $userExtr = M('user')->where(array('user_id' => $userId))->find();
                $rank = GameService::getDuan($userExtr['rank']);
                $userInfo = [
                    'is_club_owner' => $userExtr['is_club_owner'],
                    'money' => $userExtr['money'],
                    'slimeIndex' => 0,
                    'club_id' => $userExtr['club_id'],
                    'advert'  => $userExtr['advert'],
                    'area'    => $userExtr['area'],
                    'stamina' => $userExtr['stamina'],
                    'rank'    => $rank['level'],
                    'ranks'   => $rank['max'] - $rank['min'],
                    'rankNum'   => $userExtr['rank'] -$rank['min']

                ];
                if($userExtr){
                    $userFull =  array_merge($userBase,$userInfo);
                }else{
                    $userFull = $userBase;
                }
                S('user_info_full' . $userId, $userFull, 18000);//用户信息存入Redis
            }
        }
        return null;

    }

  function addSlime($openid,$user_id){
    if($openid == null || $user_id == null){
        echo "参数错误";exit;
    }
          $data = M('slime')->select();
        foreach ($data as $k => $v) {
          $datas = array(
            's_id' => $v['id'],
            'name' => $v['name'],
            'skill'=> $v['skill'],
            'blood'=> $v['blood'],
            'blue' => $v['blue'],
            'exp' =>  50,
            'u_id' => $user_id,
            'openid'=>$openid
          );
          $result[] = $datas;
      }
      
        M('user_slime')->addAll($result);
    
   
   }
 /*  史莱姆等级 */
     function s_level($level){
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

      $result = search($level, $filter);

      return  current($result);
     }
   function slimeLevel($openid){
     $slime = M('user_slime')->where(array('openid'=>$openid))->field('exp')->select();
     foreach ($slime as  $v) {
        $level = $this->s_level($v['exp']);
        $data = $level['level'];
         # code...
        $datas[] = $data; 
     }
     return $datas;
   }


}
