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
          // $user = UserService::getUserFullInfo($userId);           // $user['id']=$userId;
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
            // var_dump($userBase);exit;
            if ($userBase) {
                $openid = $userBase['openid'];
                $userExtr = M('user')->where(array('openid' => $openid))->find();
                $rank = GameService::getDuan($userExtr['rank']);
                $userInfo = [
                    'is_club_owner' => $userExtr['is_club_owner'],
                    'money' => $userExtr['money'],
                    'slimeIndex' => 0,
                    'club_id' => $userExtr['club_id'],
                    'advert' => $userExtr['advert'],
                    'stamina' => $userExtr['stamina'],
                    'rank'    => $rank['level'],
                    'ranks'   => $rank['max'] - $rank['min'],
                    'rankNum'   => $userExtr['rank'] -$rank['min']
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
                    'advert' => $userExtr['advert'],
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


}
