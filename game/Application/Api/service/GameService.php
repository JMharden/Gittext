<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhuhangan
 * Date: 2019/3/7
 * Time: 17:40
 */

namespace Api\Service;


use function Sodium\add;

class GameService
{
    /**
     * 创建对局
     */
    function createMatch($playUser, $gameType)
    {
        if ($playUser || $gameType || sizeof($playUser) < 2) {
            throw new Exception('参数错误。', 1001);
        }
        $ticketFee = $GLOBALS['_CFG']['site']['lirun'];
        //输赢大小 是否由前端传入？（暂定由后端配置）
        $battleAmount = $GLOBALS['_CFG']['site']['battleAmount'];
        //处理门票相关逻辑
        $userInfos = dealTicketFee($playUser,$ticketFee,$battleAmount);
        //创建比赛
        $matchId = generateRandomString();
        $data = ['match_id' => $matchId,
            'ticket_fee' => $ticketFee,
            'player_num' => sizeof($playUser),
            'players' => $playUser,
            'battle_amount' => $battleAmount,
            'create_time' => NOW_TIME
        ];
        M('play_match_info')->add($data);
        //处理佣金相关逻辑
        dealDraw($userInfos, $matchId);
        return $matchId;
    }

    /**游戏结算
     *  //参数 { 'matchId':'12avas123'，'winner':'1232','data':[ { userId:'' , result:'', } ] }
     * @param $matchId
     * @param $result
     * @param $winner
     * @return  返回游戏结果用于前端展示
     */
    function  gameSettle($matchId,$result,$winner,$winnerId){
        //判断游戏是否存在, 参数是否正常（玩家id能对应上）
       $gameLog = M('play_match_info')->where(array("match_id"=>$matchId))->select();
       if(!$gameLog){
           throw new Exception('未查找到对应的游戏对局', 1001);
       }
       if($gameLog[status]=='1'){
           throw new Exception('该对局已结算', 1001);
       }
       M('play_match_info')->where(array("match_id"=>$matchId))->save(array("status"=>1));
       $winBonus =$gameLog['battle_amount']*$gameLog['player_num'];
      //游戏结算
        M('user')->where(array('id' => $winner))->setInc('money',$winBonus )->setInc('win_amount',1);
       //记录收入支出
        flog($winner, 2, $winBonus, "游戏奖励");
        //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
        foreach ($result ->data as $v){
            $uid = $v->userId;
            $datas[] = array(
                'data' => $v,
                'userId'=>$uid,
                'score'=>$v->sorce,
                'start_time'=>$gameLog['create_time'],
                'end_time' => $_POST['end_time'],//游戏开始时间
                'game_id' => $gameLog['game_id'],//每局游戏的唯一标志
                'winner'=>$winner,
                '$winner_id'=> $winnerId
            );
            // var_dump($datas);exit;
             M('play_log')->addAll($datas);

        }
        return $result;

    }

    /** 佣金计算
     * @param $userInfos
     * @param $matchId
     * @param $ticketFee
     */
    function dealDraw($userInfos, $matchId, $ticketFee)
    {
        $clubFee = $GLOBALS['_CFG']['site']['clubRatio'] * $ticketFee;
        $parent1Fee = $GLOBALS['_CFG']['site']['$firstRatio'] * $ticketFee;
        $parent2Fee = $GLOBALS['_CFG']['site']['$secondRatio'] * $ticketFee;
        $parent3Fee = $GLOBALS['_CFG']['site']['$thirdRatio'] * $ticketFee;
        $systemFee = ($ticketFee - $parent1Fee - $parent2Fee - $parent3Fee - $clubFee) * sizeof($userInfos);
        $time = time();
        if (sizeof($userInfos) > 0) {
            foreach ($userInfos as $userInfo) {
                if ($userInfo['parent1']) {
                    $data[] = ['user_id' => $userInfo['parent1'],
                        'buyer_id' => $userInfo['id'],
                        'money' => $ticketFee,
                        'divided_money' => $parent1Fee,
                        'level' => 1,
                        'create_time' => $time,
                        'type' => 1,
                        'match_id' => $matchId,
                        'status' => 0];
                }
                if ($userInfo['parent2']) {
                    $data[] = ['user_id' => $userInfo['parent2'],
                        'buyer_id' => $userInfo['id'],
                        'money' => $ticketFee,
                        'divided_money' => $parent2Fee,
                        'level' => 2,
                        'create_time' => $time,
                        'type' => 1,
                        'match_id' => $matchId,
                        'status' => 0
                    ];
                }
                if ($userInfo['parent3']) {
                    $data[] = ['user_id' => $userInfo['parent3'],
                        'buyer_id' => $userInfo['id'],
                        'money' => $ticketFee,
                        'divided_money' => $parent3Fee,
                        'level' => 3,
                        'create_time' => $time,
                        'type' => 1,
                        'match_id' => $matchId,
                        'status' => 0];
                }
                if ($userInfo['club_id']) {
                    //level=0表示俱乐部
                    $ower_id = M('club_info')->where(array('id' => $userInfo['club_id']))->getField('ower_id');
                    $data[] = ['user_id' => $ower_id,
                        'buyer_id' => $userInfo['id'],
                        'money' => $ticketFee,
                        'divided_money' => $clubFee,
                        'level' => 0,
                        'create_time' => $time,
                        'type' => 2,
                        'match_id' => $matchId,
                        'status' => 0];
                }
            }
            //系统回收
            $data[] = ['user_id' => 'system',
                'buyer_id' => $userInfo['club_id'],
                'money' => $ticketFee,
                'divided_money' => $systemFee,
                'level' => -1,
                'create_time' => $time,
                'match_id' => $matchId,
                'type' => 2,
                'status' => 1];
            //除系统回收的佣金外默认都是待领取的，只有用户点击领取完才会更新了status 还有用户信息表里的总佣金expense字段 还有finance_log的收入支出明细，
            M('expense')->addAll($data);
        }

        /**处理门票相关逻辑
         * @param $playUser
         * @param $ticketFee
         * @param $battleAmount
         * @return mixed 返回用户的上级相关信息，用户后面计算分成
         */
 function dealTicketFee($playUser,$ticketFee,$battleAmount)
        {
            // 判断是否所有对战用户都满足条件（ 用户余额>门票费用+对战金额）
            $userInfos = M('user')->where(array('id' => array('IN', $playUser), 'money' => array('>=', $ticketFee + $battleAmount)))->getField('id,parent1,parent2,parent3,club_Id');;
            if (sizeof($playUser) < sizeof($userInfos)) {
                throw new Exception('用户余额不足。', 1001);
            }
            //扣除用户门票以及对战金额费用。（对战金额提前扣除等结算时补回）active_point 加10 ，游戏总对局数加1
            M('user')->where(array('id' => array('IN', $playUser)))->setDec('money', $ticketFee+$battleAmount)->setInc('active_point',10)->setInc('match_amount',1);
            flog($playUser, 1, $ticketFee+$battleAmount, "门票支出+下注金额");
            return $userInfos;
        }


        /** 批量所有用户的收入和支出
         * @param $user_ids
         * @param $type
         * @param $money (正代表收入， 负数代表支出)
         * @param $action
         * @param null $remark
         */
        function flog($user_ids, $type, $money, $action, $remark = null)
        {
            if (CLI === true) {
                $time = time();
            } else {
                $time = NOW_TIME;
            }
            if (sizeof($user_ids) > 0) {
                foreach ($user_ids as $user_id) {
                    $dataList[] = ['user_id' => $user_id,
                        'type' => $type,
                        'money' => $money,
                        'action' => $action,
                        'create_time' => $time,
                        'remark' => $remark];
                }
                M('finance_log')->addAll($dataList);
            }
        }

        function generateRandomString($length = 10)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }
    }
}