<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhuhangan
 * Date: 2019/3/7
 * Time: 17:40
 */

namespace Api\Service;


use Think\Exception;

class CommissionService
{
    /**
     * 待领取佣金
     * @return  按用户分组聚合
     */
    function unclaimedRecord($userId)
    {
        if ($userId) {
            $cache = S("unclaimed" . $userId);
            if ($cache) {
                return $cache;
            }
            //可以通过判断是否有返回记录确定用户是否有待领取的
            $record = M('expense')->where(array("user_id" => $userId, "status" => 0))->field("user_id,buyer_id,sum(divided_money) as total")->group('buyer_id')->select();
            S("unclaimed" . $userId, $record, 300);
            return $record;
        }
        return null;
    }

    /**
     * 佣金领取操作 （将所有待领取佣金都领取了）
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    function receive($userId)
    {
        if ($userId) {
            $record = M('expense')->where(array("user_id" => $userId, "status" => 0))->field("user_id,sum(divided_money) as total")->find();
            if (!$record || $record['total'] == 0) {
                throw new Exception("你没有待领取的佣金");
            }
            M('expense')->where(array("user_id" => $userId, "status" => 0))->save(array('status' => 1, 'modify_time' => NOW_TIME));
            //更新用户总佣金
            M('user')->where(array('id' => array('IN', $userId)))->setInc('expense', $record['total']);//->setInc('active_point', 10)->setInc('match_amount', 1);
            S("unclaimed" . $userId, []);
            return $record;
        }
        return null;
    }

    /**
     * @param $userId
     * @param $amount 提现金额
     * @throws Exception
     */
    function withDraw($userId, $amount)
    {
        $userInfos = M('user')->where(array('user_id' => array('IN', $userId), 'expense_avail' => array('EGT', $amount)))->getField('id,expense_avail');
        if (!$userInfos) {
            throw new Exception("佣金余额不足");
        }
        $data = ['user_id' => $userId,
            'create_time' => NOW_TIME,
            'money' => $amount,
            'type' => 1
        ];
        //佣金提现记录
        M('expense_withdraw')->add($data);
        //todo 提现方式待定:是已现金直接发放还是说 兑换到money
        //更新佣金信息
        M('user')->where(array('user_id' => $userId))->setInc('expense_withdraw', $amount)->setDec('expense_avail', $amount);

    }


    /** 佣金计算
     * @param $userInfos
     * @param $matchId
     * @param $ticketFee
     */
    function dealDraw($userInfos, $matchId, $ticketFee)
    {
        $clubFee = $GLOBALS['_CFG']['site']['clubRatio'] * $ticketFee;
        $parent1Fee = $GLOBALS['_CFG']['site']['firstRatio'] * $ticketFee;
        $parent2Fee = $GLOBALS['_CFG']['site']['secondRatio'] * $ticketFee;
        $parent3Fee = $GLOBALS['_CFG']['site']['thirdRatio'] * $ticketFee;
        $systemFee = ($ticketFee - $parent1Fee - $parent2Fee - $parent3Fee - $clubFee) * sizeof($userInfos);
        $time = time();
        if (sizeof($userInfos) > 0) {
            foreach ($userInfos as $userInfo) {
                if ($userInfo['parent1']) {
                    S("unclaimed" . $userInfo['parent1'], null);
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
                    S("unclaimed" . $userInfo['parent2'], null);
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
                    S("unclaimed" . $userInfo['parent3'], null);
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
                    S("unclaimed" . $ower_id, null);
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
                'type' => 2,
                'match_id' => $matchId,
                'status' => 1];
            //除系统回收的佣金外默认都是待领取的，只有用户点击领取完才会更新了status 还有用户信息表里的总佣金expense字段 还有finance_log的收入支出明细，
            M('expense')->addAll($data);
        }
    }
}