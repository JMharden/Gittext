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
         $config = getGameConfig($gameType);
        //处理门票相关逻辑
        $userInfos = dealTicketFee($playUser, $config);
        //创建比赛
        $matchId = generateRandomString();
        $data = ['match_id' => $matchId,
            'ticket_fee' => $config['ticketFee'],
            'player_num' => sizeof($playUser),
            'players' => $playUser,
            'battle_amount' => $config['battleAmount'],
            'create_time' => NOW_TIME
        ];
        M('play_match_info')->add($data);
        //处理佣金相关逻辑
        CommissionService::dealDraw($userInfos, $matchId);
        return $matchId;
    }

    /**游戏结算
     *  //参数 { 'matchId':'12avas123'，'winner':'1232','data':[ { userId:'' , result:'', } ] }
     * @param $matchId
     * @param $result
     * @param $winner
     * @return  返回游戏结果用于前端展示
     */
    function gameSettle($matchId, $result, $winner, $winnerId)
    {
        //判断游戏是否存在, 参数是否正常（玩家id能对应上）
        $gameLog = M('play_match_info')->where(array("match_id" => $matchId))->select();
        if (!$gameLog) {
            throw new Exception('未查找到对应的游戏对局', 1001);
        }
        if ($gameLog[status] == '1') {
            throw new Exception('该对局已结算', 1001);
        }
        M('play_match_info')->where(array("match_id" => $matchId))->save(array("status" => 1));
        $winBonus = $gameLog['battle_amount'] * $gameLog['player_num'];
        //游戏结算
        M('user')->where(array('id' => $winner))->setInc('money', $winBonus)->setInc('win_amount', 1);
        //记录收入支出
        flog($winner, 2, $winBonus, "游戏奖励");
        //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
        foreach ($result->data as $v) {
            $uid = $v->userId;
            $datas[] = array(
                'data' => $v,
                'userId' => $uid,
                'score' => $v->sorce,
                'start_time' => $gameLog['create_time'],
                'end_time' => $_POST['end_time'],//游戏开始时间
                'game_id' => $gameLog['game_id'],//每局游戏的唯一标志
                'winner' => $winner,
                '$winner_id' => $winnerId
            );
            // var_dump($datas);exit;
            M('play_log')->addAll($datas);
            //rank分计算
            //todo  若用户升级段位了前端需要提示 ，此操作最好从缓存中取值判断，不要在查一遍sql
            M('user')->where(array('id' => $uid))->setInc('rank', dealRank($gameLog['type'],$uid,$winnerId,$v->sorce));
        }
        return $result;

    }

    /**处理游戏rank分
     * +20 -15        初级场
     * +30 -15  （额外+10） 中级场
     * +35 -15  （额外+15） 高级场
     * $score  若评分很高 则有额外加分 （暂定若评分为S  则额外+5分）
     * @param $gameType
     * @param $result
     */
        function dealRank($gameType,$userId,$winnerId,$score){
            $plu =0;
            if($score =='S'){
                $plu= 5;
            }
            //如果输 直接扣掉15分
            if($userId!=$winnerId){
                return -15+$plu;
            } else if($gameType==1){
                        return 20+$plu;
            } else if($gameType==2){
                return 25+$plu;
            } else if($gameType==3){
                return 30+$plu;
            }
        }

    /**rank分转换为段位
     *
     * 1240 下 青铜三  1240 - 1280 青铜2   1280 -1320 青铜1  -》青铜      +40分一个段位          （所有青铜）
     * 1320 - 1380 白银3   1380-1440 白银2  1440-1500 白银1 ——》白银（匹配用）  +60分一个段位  （所有白银为一个段位区间）
     * 1500-1560  黄金5   1560-1620 黄金4  1620- 1680   黄金3   1680-1740 黄金2  1740-1800黄金1 ——》黄金   +60分一个段位  （黄金5到黄金3 为一个段位区间用于匹配）
     * （1800-1860  V  1860-1920 iV   1920-1980 iiV ）-》铂金2    1980-2040  2040-2100 铂金1   +60分一个段位
     *  (2100- 2200  220-2300  2300-2400 )->钻石2    （2400-2500  2500-2600）  =》钻石1   +100分一个段位
     *  大师  +100分一个段位
     * 王者
     *
     * @param $rank
     *
     */
        function getDuan($rank){
            if(0 <= $rank&$rank <=1320){
                // if(0 <= $rank&$rank <=1240){
                //     return '青铜III';
                // }elseif (1241 <= $rank&$rank <= 1280){
                //     return '青铜II';
                // }else{
                //     return '青铜I';
                // }
                return '青铜'；
               
            }elseif (1321 <= $rank&$rank <= 1500){
                // if(1321 <= $rank&$rank <=1380){
                //     return '白银III';
                // }elseif (1381 <= $rank&$rank <= 1440){
                //     return '白银II';
                // }else{
                //     return '白银I';
                // }
                return '白银';
            }elseif (1501 <= $rank&$rank <= 1800){
                if(1501 <= $rank&$rank  <= 1680){
                    return '黄金III';
                }elseif (1681 <= $rank&$rank <= 1740){
                    return '黄金II';
                }else{
                    return '黄金I';
                }
                // if(1501 <= $rank&$rank <=1560 || 1561 <= $rank&$rank <= 1620 || 1621 <= $rank&$rank <= 1680){
                //     return '黄金V';
                // }elseif (1561 <= $rank&$rank <= 1620){
                //     return '黄金IV';
                // }elseif (1621 <= $rank&$rank <= 1680){
                //     return '黄金III';
                // }elseif (1681 <= $rank&$rank <= 1740){
                //     return '黄金II';
                // }else{
                //     return '黄金I';
                // }
             
            }elseif (1801 <= $rank&$rank <= 1980){
                if(1801 <= $rank&$rank <= 1980){
                    return '铂金II';
                }else{
                    return '铂金I';
                }
                
            }elseif (2101 <= $rank&$rank <= 2600){
                if(2101 <= $rank&$rank <= 2400){
                    return '钻石II';
                }else{
                    return '钻石I';
                }
               
            }elseif (2601 <= $rank&$rank <= 2800){
                return '大师';
            }else{
                return '王者';
            }

        }

    /**
     * @param $gameType 1:初级场 ，2 ：中级场  3 ：高级场 （不同类型对应的门票费用不同）
     *
     */
    function getGameConfig($gameType)
    {
        $ticketFee = $GLOBALS['_CFG']['site']['lirun' . $gameType];
        //输赢大小 是否由前端传入？（暂定由后端配置）
        $battleAmount = $GLOBALS['_CFG']['site']['battleAmount1' . $gameType];
        if ($ticketFee && $battleAmount) {
            return ['ticketFee' => $ticketFee,
                    'battleAmount' => $battleAmount
            ];
        } else {
            throw new Exception('门票相关配置错误。', 1002);
        }

    }

    /**处理门票相关逻辑
     * @param $playUser
     * @param $ticketFee
     * @param $battleAmount
     * @return mixed 返回用户的上级相关信息，用户后面计算分成
     */
    function dealTicketFee($playUser, $config)
    {
        $ticketFee = $config['ticketFee'];
        $battleAmount = $config['battleAmount'];
        // 判断是否所有对战用户都满足条件（ 用户余额>门票费用+对战金额）
        $userInfos = M('user')->where(array('id' => array('IN', $playUser), 'money' => array('>=', $ticketFee + $battleAmount)))->getField('id,parent1,parent2,parent3,club_Id');;
        if (sizeof($playUser) < sizeof($userInfos)) {
            throw new Exception('用户余额不足。', 1001);
        }
        //扣除用户门票以及对战金额费用。（对战金额提前扣除等结算时补回）active_point 加10 ，游戏总对局数加1
        M('user')->where(array('id' => array('IN', $playUser)))->setDec('money', $ticketFee + $battleAmount)->setInc('active_point', 10)->setInc('match_amount', 1);
        flog($playUser, 1, $ticketFee + $battleAmount, "门票支出+下注金额");
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