<?php
/**
 * Created by IntelliJ IDEA.
 * User: zhuhangan
 * Date: 2019/3/7
 * Time: 17:40 xxxxxxxxxxxxxxxx
 */

namespace Api\Service;


use Think\Exception;

class GameService
{
    /**
     *
     */
    public function recovery($userId)
    {
        //要不要针对频率进行限制?
        if(S("recovery_task_".$userId)){
            echo "limit exec per 5min";
            exit;
        }
        //体力上限为50
        M('user')->where(array('stamina' => array('LT', 50),'user_id' => $userId))->setInc('stamina', 1);
        S("recovery_task_".$userId,"task",["expire"=>300]);
        echo "success";
        exit;
    }


    /**娱乐赛游戏结算

 *  //参数 { 'matchId':'12avas123'，'winner':'1232','data':[ { userId:'' , result:'', } ] }
 * @param $matchId
 * @param $result
 * @param $winner
 * @param $winnerId
 * @return  返回游戏结果用于前端展示
 * @throws Exception
 */
function funGameSettle($matchId, $result, $winner, $winnerId)
{
    $resultJson = json_decode( $result,true);
    //判断游戏是否存在, 参数是否正常（玩家id能对应上）
    $gameLog = M('fun_match_info')->where(array("match_id" => $matchId))->find();
    if (!$gameLog) {
        throw new Exception('未查找到对应的游戏对局', 1001);
    }
    if ($gameLog[status] == '1') {
        //    throw new Exception('该对局已结算', 1001);
    }
    M('fun_match_info')->where(array("match_id" => $matchId))->save(array("status" => 1));
    $winBonus = $gameLog['battle_amount'] * $gameLog['player_num'];
    //游戏结算
    $Model = new \Think\Model();
    $Model->execute("update dd_user set candy=candy+".$gameLog['player_num'].", fun_win_amount=fun_win_amount+1 where user_id = ".$winner);
    //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
    foreach ($resultJson as $v) {
        $uid = $v['userId'];
        $datas[] = array(
            'user_id' => $uid,
            'game_id' => 0,
            'result' => $result,
            'score' => $v['score'],
            'rank' => $v['rank'],
            'start_time' => $gameLog['create_time'],
            'end_time' => NOW_TIME,//游戏开始时间
            'challenge_id'=> '',
            'type'=>$gameLog['type'],
            'status'=>2,
            'winner' => $winner,
            'winner_id' => $winnerId,
            'match_id'=>$matchId
        );
        //rank分计算
        $rank =$this ->dealRank($gameLog['type'],$uid,$winnerId,$v['score']);
        M('user')->where(array('user_id' => $uid))->setInc('rank',$rank );
    }
    M('play_log')->addAll($datas);
    return $result;


}



/**
 * 创建娱乐赛
 * @param $playUser
 * @throws Exception
 */


function createFunMatch($playUser){
    if (!($playUser && sizeof($playUser) >0)) {
       throw new Exception('参数错误。', 1001);
    }
     $userInfos  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.parent1,a.parent2,a.parent3,u.id")//需要显示的字段
                        ->where(array('a.id' => array('IN', $playUser), 'u.stamina'=>array('GT',0)))
                        ->select();
                        // var_dump($userInfos);exit;
    //判断体力是否充足
    // $userInfos = M('user')->where(array('user_id' => array('IN', $playUser), 'stamina'=>array('GT',0)))->getField('id,parent1,parent2,parent3');
    if (sizeof($playUser) > sizeof($userInfos)) {
        throw new Exception('用户体力不足。', 1001);
    }
    //active_point 加5 ，游戏总对局数加1,体力-1
    $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
    $Model->execute("update dd_user set  active_point=active_point+5,fun_amount =fun_amount +1,stamina = stamina -1 where user_id in (".implode(",",$playUser).")");
    //创建比赛
    $matchId = $this->generateRandomString();
    $data = ['match_id' => $matchId,
        'player_num' => sizeof($playUser),
        'players' => implode(",",$playUser),
        'create_time' => NOW_TIME,
        'type'    =>   2,//娱乐赛

    ];
    M('fun_match_info')->add($data);

    return $data;
}
    /**
     * 创建对局
     * @param $playUser
     * @param $gameType
     * @return
     * @throws Exception
     */
    function createMatch($playUser, $gameType,$battleAmount)
    {
     
         if (!($playUser || $gameType || sizeof($playUser) >1)) {
            throw new Exception('参数错误。', 1001);
        }
        // $battleAmount = $_POST['battleAmount'];
        $config = $this->getGameConfig($gameType,$battleAmount);
         
        
        //处理门票相关逻辑
        $userInfos = $this->dealTicketFee($playUser,$config);
        //创建比赛
        $matchId = $this->generateRandomString();
        $data = ['match_id' => $matchId,
            'ticket_fee' => $config['ticketFee'],
            'player_num' => sizeof($playUser),
            'players' => implode(",",$playUser),
            'battle_amount' => $config['battleAmount'],
            'create_time' => NOW_TIME,
            'type'=>$gameType
        ];
        M('play_match_info')->add($data);
        //处理佣金相关逻辑
        CommissionService::dealDraw($userInfos, $matchId,$config['ticketFee']);

        return $data;
    }

    /**游戏结算
     *  //参数 { 'matchId':'12avas123'，'winner':'1232','data':[ { userId:'' , result:'', } ] }
     * @param $matchId
     * @param $result
     * @param $winner
     * @param $winnerId
     * @return  返回游戏结果用于前端展示
     * @throws Exception
     */
    function gameSettle($matchId, $result, $winner, $winnerId)
    {
        $resultJson = json_decode($result,true);

        //判断游戏是否存在, 参数是否正常（玩家id能对应上）
        $gameLog = M('play_match_info')->where(array("match_id" => $matchId))->find();
        if (!$gameLog) {
            throw new Exception('未查找到对应的游戏对局', 1001);
        }
        if ($gameLog['status'] == '1') {
           throw new Exception('该对局已结算', 1001);
        }
        M('play_match_info')->where(array("match_id" => $matchId))->save(array("status" => 1));
        $playNum = $gameLog['player_num'];

      //  $winBonus = $gameLog['battle_amount'] * $gameLog['player_num'];
        $bonusRatio  = $this -> dealBonus($playNum, $gameLog['battle_amount']);
        $rankData    = $this -> dealRankByNum($playNum);
// var_dump($rankData);exit;
        
        //游戏结算
        //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
        foreach ($resultJson as $v) {
            $uid = $v['userId'];
            //个人排名
            $rank = $v['rank'];
            
            //判断当前排名是否有奖励
            $bonus =0;
            if($rank<=count($bonusRatio)){
                $bonus= $bonusRatio[$rank-1];
                // var_dump($bonus);exit;
                $finLogs[] = array(
                    'user_id' => $uid,
                    'type' => 2,
                    'money' =>$bonus,
                    'create_time' => NOW_TIME,
                    'remark' => '游戏对局',
                    'create_time' => NOW_TIME,
                    'remark' => $v['rank']
                   );
                //排名第一增加胜局数
                if($rank==1){
                    M('user')->where(array('user_id' => $uid))->setInc('win_amount',1 );
                }
                M('user')->where(array('user_id' => $uid))->setInc('money',$bonus);
            }
            //rank分计算
            $ranks =$this ->dealRank($gameLog['type'],$v['score'],$rank,$rankData);
            M('user')->where(array('user_id' => $uid))->setInc('rank',$ranks);
            $res[]=array(
                'user_id' => $uid,
                'winner' => $winner,
                'score'=>$v['score'],
                'rank'=>$rank,//排名
                // 'ranks'=>$ranks,//排位分计算
                'ranks'=>$rankData[$rank-1],//排位分计算
                'bonus'=>$bonus
            );
             $datas[] = array(
                'user_id' => $uid,
                'game_id' => 0,
                'result' => $result,
                'score' => $v['score'],
                'rank' =>  $rank ,
                'ranks'=>$rankData[$rank-1],//排位分计算
                'bonu'=>$bonus,
                'start_time' => $gameLog['create_time'],
                'end_time' => NOW_TIME,//游戏开始时间
                'type'=>$gameLog['type'],
                'status'=>2,
                'winner' => $winner,
                'winner_id' => $winnerId,
                'match_id'=>$matchId
            );
             // var_dump($datas);exit;
            
        }

        M('finance_log')->addAll($finLogs);
        M('play_log')->addAll($datas);
        return $res;

    }

    /**
     * 奖金分配
     * 1-3个人   一个人获奖   第一名获奖 1*n
     * 4-8个人  两个人获奖   第一名: 3+(n-4)*0.5    第二名：1+(n-4)*0.5
     * 9-14个人 三个人获奖   第一名: 5+(n-9)*0.3    第二名：3+(n-9)*0.3  第三名：1+(n-9)*0.4   （四舍五入取整）
     * 15-20个人 四个人获奖   第一名: 6.5+(n-15)*0.25   第二名：4.5+(n-15)*0.25  第三名：3+(n-15)*0.25   第三名：1+(n-15)*0.25
     *
     * @param $playerNum 玩家数量
     * @param $battleAmount  对战金额
     * @return array
     */
    function dealBonus($playerNum,$battleAmount){
        $first=0;
        $second =0;
        $third =0;
        $fourth =0;
        if($playerNum<4){
            $first =$playerNum*$battleAmount;
        }else if ($playerNum<9){
            $first = (($playerNum-4)*0.5 +3)*$battleAmount;
            $second = (($playerNum-4)*0.5 +1)*$battleAmount;
        }else if ($playerNum<15){
            $first  = round((($playerNum-9)*0.3 +5)*$battleAmount,1);
            $second =  round(($playerNum-9)*0.3 +3*$battleAmount,1);
            $third  = $playerNum*$battleAmount-$first-$second;
        }else{
            $first =  round(($playerNum-15)*0.25 +6.5*$battleAmount,1);
            $second = round(($playerNum-15)*0.25 +4.5*$battleAmount,1);
            $third =round( ($playerNum-15)*0.25 +3*$battleAmount,1);
            $fourth =  $playerNum*$battleAmount-$first-$second-$third;
        }
        $data =array($first,$second,$third,$fourth);
        return $data;
    }


    function dealRankByNum($playerNum){
        $first=0;
        $second =0;
        $third =0;
        $fourth =0;
        if($playerNum<4){
            $first =15;
        }else if ($playerNum<9){
            $first = 20;
            $second = 15;
        }else if ($playerNum<15){
            $first = 25;
            $second =  20;
            $third = 15;
        }else{
            $first = 30;
            $second =25;
            $third =20;
            $fourth = 15;
        }
        $data =array($first,$second,$third,$fourth);
        return $data;
    }
    /**处理游戏rank分
     * +20 -15        初级场
     * +30 -15  （额外+10） 中级场
     * +35 -15  （额外+15） 高级场
     * $score  若评分很高 则有额外加分 （暂定若评分为S  则额外+5分）
     * @param $gameType
     * @param $userId
     * @param $winnerId
     * @param $score
     * @param $rank
     * @param $playNum  比赛人数，排名 也决定rank分
     * @return int|void
     */
        function dealRank($gameType,$score,$rank,$playNum){
            $scorePlu =0;
            $rankAdd =0;
            $gameTypePlu =0;
          if($rank<=count($playNum)){
              $rankAdd =$playNum[$rank];
          }
            if($score =='S'){
                $scorePlu= 5;
            }
            //如果输 直接扣掉15分
            if($rank<count($playNum)){
                return -15+$scorePlu;
            }
            if($gameType==1){
                $gameTypePlu =5;
            } else if($gameType==2){
                $gameTypePlu=10;
            } else if($gameType==3){
                $gameTypePlu=15;
            }
            return $rankAdd+$gameTypePlu+$scorePlu;
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
      function getDuan($level){
          $filter = [
            ['level' => '青铜',  'min' => 0,  'max' => 1320],
            ['level' => '白银',  'min' => 1321,  'max' => 1500],
            ['level' => '黄金',  'min' => 1501,  'max' => 1800],
            ['level' => '铂金',  'min' => 1801,  'max' => 2000],
            ['level' => '钻石',  'min' => 2001,  'max' => 2600],
            ['level' => '王者',  'min' => 2601,  'max' => 3000],
          ];

          $result = search($level, $filter);

          return  current($result);
     }


    /**
     * @param $gameType 1:初级场 ，2 ：中级场  3 ：高级场 （不同类型对应的门票费用不同）
     * @return
     * @throws Exception
     */
    function getGameConfig($gameType,$battleAmount)
    {
       
        $ticketFee = $GLOBALS['_CFG']['site']['lirun' . $gameType];

        //输赢大小 是否由前端传入？（暂定由后端配置）
       
        // $battleAmount = $GLOBALS['_CFG']['site']['battleAmount' . $gameType];
        if ($ticketFee && $battleAmount) {
            return ['ticketFee' => $ticketFee,
                    'battleAmount' => $battleAmount
            ];
        } else {
            throw new Exception('门票相关配置错误。', 1002);
        }
        return;
    }
// [{"userId":"182","score":"666","rank":"1"},{"userId":"183","score":"350","rank":"2"}] 
    /**处理门票相关逻辑
     * @param $playUser
     * @param $config
     * @return mixed 返回用户的上级相关信息，用户后面计算分成
     * @throws Exception
     */
    function dealTicketFee($playUser, $config)
    {
        $ticketFee = $config['ticketFee'];
        $battleAmount = $config['battleAmount'];
        // 判断是否所有对战用户都满足条件（ 用户余额>门票费用+对战金额）,体力>1
        // $userInfos = M('user_base')->where(array('id' => array('IN', $playUser), 'stamina'=>array('GT',0),'money' => array('EGT', $ticketFee + $battleAmount)))->getField('id,parent1,parent2,parent3');
          $userInfos  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.parent1,a.parent2,a.parent3,u.club_id,u.id")//需要显示的字段
                        ->where(array('a.id' => array('IN', $playUser), 'u.stamina'=>array('GT',0),'u.money' => array('EGT', $ticketFee + $battleAmount)))
                        ->select();

                        // var_dump(sizeof($userInfos));exit;
        if (sizeof($playUser) > sizeof($userInfos)) {
            throw new Exception('用户余额不足。', 1001);
        }

        //扣除用户门票以及对战金额费用。（对战金额提前扣除等结算时补回）active_point 加10 ，游戏总对局数加1,体力-1
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        $Model->execute("update dd_user set money=money-".($ticketFee + $battleAmount).", active_point=active_point+10,match_amount =match_amount +1,stamina = stamina -1 where user_id in (".implode(",",$playUser).")");
        // $Model->execute("update dd_user set money=money-".($ticketFee + $battleAmount).", active_point=active_point+10,match_amount =match_amount +1,stamina = stamina -1 where user_id in (".implode(",",$playUser).")");


        // var_dump
       // M('user')->where(array('user_id' => array('IN', $playUser)))->setDec('money', $ticketFee + $battleAmount)->setInc('active_point', 10)->setInc('match_amount', 1);
     //   M('user')->where(array('id' => array('IN', $playUser)))->save($data);
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
    function flog($user_ids, $type, $money, $remark)
    {
        if (CLI === true) {
            $time = time();
        } else {
            $time = NOW_TIME;
        }
        if (sizeof($user_ids) > 0) {
            foreach ($user_ids as $user_id) {
                $dataList[] = [
                    'user_id' => $user_id,
                    'type' => $type,
                    'money' => $money,
                    'create_time' => $time,
                    'remark' => $remark
                  ];
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


