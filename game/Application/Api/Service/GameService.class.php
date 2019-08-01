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
     * 创建对局
     * @param $playUser
     * @param $gameType
     * @return
     * @throws Exception
     */
    function createMatch($playUser, $gameType,$battleAmount,$slime_id)
    {
     
         if (!($playUser || $gameType || sizeof($playUser) >1)) {
            throw new Exception('参数错误。', 1001);
        }
        // $config = $this->getGameConfig($gameType,$battleAmount);
        
        //处理门票相关逻辑
        // $userInfos = $this->dealTicketFee($playUser,$config);
        if($battleAmount<100){
            $ticketFee = $battleAmount*0.2;
        }else{
            $ticketFee = $battleAmount*0.1;
        }
      
        $userInfos = $this->dealTicketFee($playUser,$ticketFee,$battleAmount);
        //创建比赛
        $matchId = $this->generateRandomString();

        $data = ['match_id' => $matchId,
            'ticket_fee' => $ticketFee,
            'player_num' => sizeof($playUser),
            'players'  => implode(",",$playUser),
            'battle_amount' => $battleAmount,
            'create_time' => NOW_TIME,
            'expaire_time'=>time()+1*60*60,
            'type'=>$gameType//初中高级场
        ];
       
        M('play_match_info')->add($data);
        //处理佣金相关逻辑
         CommissionService::dealDraw($userInfos, $matchId,$ticketFee);

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
    function gameSettle($matchId, $user_id, $rank,$score,$slime_id)
    {
        //$resultJson = json_decode($result,true);
        //判断游戏是否存在, 参数是否正常（玩家id能对应上）
        $gameLog = M('play_match_info')->where(array("match_id" => $matchId))->find();
        if (!$gameLog) {
            throw new Exception('未查找到对应的游戏对局', 1001);
        }
        $players = explode(",", $gameLog['players']);
        //判断用户id是否正确
        if(!in_array($user_id, $players)){
            throw new Exception('参数错误', 1003);
        }
        if(time()>$gameLog['expaire_time']){
            M('play_match_info')->where(array("match_id" => $matchId))->save(array("status" => 1));
        } 
        if ($gameLog['status'] == '1' || time()>$gameLog['expaire_time']) {
           throw new Exception('该对局已结算', 1002);
        }
        
        $dealedPlayers = explode(",", $gameLog['dealed_players']);
       //判断是否已经结算过
        if(in_array($user_id, $dealedPlayers)){
            throw new Exception('该用户已结算', 1003);
        }else{
            M('play_match_info')->where(array("match_id" => $matchId))->save(array("dealed_players" => $gameLog['dealed_players'].$user_id.','));

        }
        $playNum = $gameLog['player_num'];
      //  $winBonus = $gameLog['battle_amount'] * $gameLog['player_num'];
        $bonusRatio  = $this -> dealBonus($playNum, $gameLog['battle_amount']-$gameLog['ticket_fee']);

        $ranks    = $this -> dealRankByNum($playNum,$rank); //段位分计算
        

         
            //判断当前排名是否有奖励
            // $bonus =0;
            // $rank =0;
            $userUpdateInfo = array();
            if($rank<=count($bonusRatio)){
                $bonus= $bonusRatio[$rank-1];
                // var_dump($bonus);exit;
                $finLogs= array(
                    'user_id' => $user_id,
                    'type' => 2,
                    'money' =>$bonus,
                    'create_time' => NOW_TIME,
                    'remark' => '游戏对局',
                   );
                //排名第一增加胜局数
                if($rank==1){
                    if($gameLog['type']==4){
                        $userUpdateInfo["invite_win_amount"] = 1;
                    }else{

                        $userUpdateInfo["win_amount"] = 1;
                    }
                }
            }
            $userUpdateInfo["rank"] = $ranks;
            $userUpdateInfo["money"] = $bonus;
           
       //      $userUpdateInfo["lastest_slime"] = $slime_id;
       // //     M('user')->where(array('user_id' => $user_id))->setInc('rank',$ranks);
            $res=array(
                'user_id' => $user_id,
                'score'=>$score,
                'rank' =>$rank,//排名
                'ranks'=>$ranks,//排位分计算
                'bonus'=>$bonus
            );
             $datas= array(
                'user_id' => $user_id,
                'score' => $score,
                'rank' =>  $rank ,
                'ranks'=>$ranks,//排位分计算
                'bonu'=>$bonus,
                'start_time' => $gameLog['create_time'],
                'end_time' => NOW_TIME,//游戏开始时间
                'type'=>$gameLog['type'],
                'status'=>2,
                'match_id'=>$matchId
            );
              //处理掉线玩家逻辑
            $offline =  array_diff($players,$dealedPlayers);
            foreach($offline as $ind=> $id){
                if($offline!=$user_id){
                    $datas[]= array(
                        'user_id' => $user_id,
                        'score' => $score,
                        'rank' =>  $rank ,
                        'ranks'=>'-15',//排位分计算
                        'bonu'=>0,
                        'start_time' => $gameLog['create_time'],
                        'end_time' => NOW_TIME,//游戏开始时间
                        'type'=>$gameLog['type'],
                        'status'=>3,//掉线
                        'match_id'=>$matchId
                    );
                }
           }
            
        M('finance_log')->add($finLogs);
      //  M('user')->where(array('user_id' => $user_id))->save($userUpdateInfo);
      // 批量更新用户信息
       $sql = "update dd_user set ";
        foreach($userUpdateInfo as $key=>$value)
        {
            $sql=$sql.$key.'='.$key.'+'.$value.',';
           
       }
       $sqls  =substr($sql, 0, -1).' '.'where user_id='.$user_id;
     // var_dump($sqls);exit;
        $Model = new \Think\Model();
       $Model->execute($sqls);
        // $Model->execute($sql);
        
        M('play_log')->add($datas);
        return $res;

    }
    /**
 * 创建娱乐赛
 * @param $playUser
 * @throws Exception
 */


function createFunMatch($playUser,$slime_id){
    if (!($playUser && sizeof($playUser) >0)) {
       throw new Exception('参数错误。', 1001);
    }
    
    //判断体力是否充足
    $userInfos = M('user')->where(array('user_id' => array('IN', $playUser), 'stamina'=>array('GT',0)))->select();
    // var_dump(sizeof($userInfos));exit;
    if (sizeof($playUser) > sizeof($userInfos)) {
        throw new Exception('用户体力不足。', 1001);
    }
    //active_point 加5 ，游戏总对局数加1,体力-1
    $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
    $Model->execute("update dd_user set  active_point=active_point+5,fun_amount =fun_amount+1,stamina = stamina-1 where user_id in (".implode(",",$playUser).")");
    //创建比赛
    $matchId = $this->generateRandomString();
    $data = ['match_id' => $matchId,
        'player_num' => sizeof($playUser),
        'players' => implode(",",$playUser),
        'create_time' => NOW_TIME,
        'expaire_time'=>time()+1*60*60,
        'type'    =>   1,//娱乐赛

    ];
    M('fun_match_info')->add($data);

    return $data;
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
function funGameSettle($matchId,$user_id,$rank,$score,$slime_id)
{
    // $resultJson = json_decode($result,true);
    //判断游戏是否存在, 参数是否正常（玩家id能对应上）
    $gameLog = M('fun_match_info')->where(array("match_id" => $matchId))->find();
    if (!$gameLog) {
            throw new Exception('未查找到对应的游戏对局', 1001);
        }
        $players = explode(",", $gameLog['players']);
        //判断用户id是否正确
        if(!in_array($user_id, $players)){
            throw new Exception('参数错误', 1003);
        }

        if(time()>$gameLog['expaire_time']){

            M('fun_match_info')->where(array("match_id" => $matchId))->save(array("status" => 1));
        } 
        if ($gameLog['status'] == '1' || time()>$gameLog['expaire_time']) {
           throw new Exception('该对局已结算', 1002);
        }
        
        $dealedPlayers = explode(",", $gameLog['dealed_players']);
       //判断是否已经结算过
        if(in_array($user_id, $dealedPlayers)){
            throw new Exception('该用户已结算', 1003);
        }else{
            M('fun_match_info')->where(array("match_id" => $matchId))->save(array("dealed_players" => $gameLog['dealed_players'].$user_id.','));

        }    

          $playNum = $gameLog['player_num'];
          $candyNum  = $this -> dealCandyByNum($playNum);
          $candy = 1;
          if($rank<=count($candyNum)){
                $candy= $candyNum[$rank-1];
                //排名第一增加胜局数
                if($rank==1){
                   M('user')->where(array('user_id' => $user_id))->setInc('fun_win_amount',1);
                }
                M('user')->where(array('user_id' => $user_id))->setInc('candy',$candy);
           }
        //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
        $datas= array(
            'user_id' => $user_id,
            'score' => $score,
            'rank' => $rank,
            'start_time' => $gameLog['create_time'],
            'end_time' => NOW_TIME,//游戏开始时间
            'type'=>$gameLog['type'],
            'status'=>2,
            'match_id'=>$matchId
        );
        //结果计算
        $res=array(
                'user_id' => $user_id,
                'score'=>$score,
                'rank' =>$rank,//排名
                'candy'=>$candy//加糖果
                
            );
        M('user')->where(array('user_id' => $user_id))->setField('lastest_slime',$slime_id);
        M('fun_play_log')->add($datas);

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
        $first  = 0;
        $second = 0;
        $third  = 0;
        $fourth = 0;
        $five = 0;
        $six=0;
        $seven=0;
        $eight=0;
        $night=0;
        if($playerNum<4){
            $first =$playerNum*$battleAmount;
        }else if ($playerNum<7){
            $first = round($playerNum*$battleAmount*0.6);
            $second = round($playerNum * $battleAmount * 0.4);
        }else if ($playerNum<9){
            $first = round($playerNum*$battleAmount*0.5);
            $second = round($playerNum*$battleAmount*0.3);
            $third  = $playerNum*$battleAmount-$first-$second;
        }else if ($playerNum<12){
            $first = round($playerNum*$battleAmount*0.4);
            $second = round($playerNum*$battleAmount*0.3);
            $third  = round($playerNum*$battleAmount*0.2);;
            $fourth =  $playerNum*$battleAmount-$first-$second-$third;
        }else if ($playerNum<14){
            $first = round($playerNum*$battleAmount*0.3);
            $second = round($playerNum*$battleAmount*0.3);
            $third  = round($playerNum*$battleAmount*0.2);
            $fourth  = round($playerNum*$battleAmount*0.1);
            $five =  $playerNum*$battleAmount-$first-$second-$third-$fourth;
        }else if ($playerNum<17){
            $first = round($playerNum*$battleAmount*0.3);
            $second = round($playerNum*$battleAmount*0.2);
            $third  = round($playerNum*$battleAmount*0.2);
            $fourth  = round($playerNum*$battleAmount*0.1);
            $five =  round($playerNum*$battleAmount*0.1);
            $six =  $playerNum*$battleAmount-$first-$second-$third-$fourth-$five;
        }else if ($playerNum<19){
            $first = round($playerNum*$battleAmount*0.25);
            $second = round($playerNum*$battleAmount*0.2);
            $third  = round($playerNum*$battleAmount*0.1);
            $fourth  = round($playerNum*$battleAmount*0.1);
            $five =  round($playerNum*$battleAmount*0.1);
            $six =  round($playerNum*$battleAmount*0.1);
            $seven =  $playerNum*$battleAmount-$first-$second-$third-$fourth-$five-$six;
        }else if ($playerNum<22){
            $first = round($playerNum*$battleAmount*0.25);
            $second = round($playerNum*$battleAmount*0.15);
            $third  = round($playerNum*$battleAmount*0.15);
            $fourth  = round($playerNum*$battleAmount*0.15);
            $five =  round($playerNum*$battleAmount*0.1);
            $six =  round($playerNum*$battleAmount*0.1);
            $seven =  round($playerNum*$battleAmount*0.05);
            $eight =  $playerNum*$battleAmount-$first-$second-$third-$fourth-$five-$six-$seven;
        }else {
            $first = round($playerNum*$battleAmount*0.25);
            $second = round($playerNum*$battleAmount*0.15);
            $third  = round($playerNum*$battleAmount*0.15);
            $fourth  = round($playerNum*$battleAmount*0.10);
            $five =  round($playerNum*$battleAmount*0.1);
            $six =  round($playerNum*$battleAmount*0.1);
            $seven =  round($playerNum*$battleAmount*0.05);
            $eight =  round($playerNum*$battleAmount*0.05);
            $night =  $playerNum*$battleAmount-$first-$second-$third-$fourth-$five-$six-$seven-$night;
        }
        $data =array($first,$second,$third,$fourth,$five,$six,$seven,$eight,$night);
        return $data;
    }

    function dealCandyByNum($playerNum){
        $first  = 1;
        $second = 1;
        $third  = 1;
        $fourth = 1;
        if($playerNum<3){
            $first = 10;
        }else if ($playerNum<6){
            $first = 15;
            $second= 10;
        }else if ($playerNum<9){
            $first = 20;
            $second= 15;
            $third = 10;
        }else{
            $first  = 25;
            $second = 20;
            $third  = 15;
            $fourth = 10;
        }
        $data =array($first,$second,$third,$fourth);

        return $data;
    }
    function dealRankByNum($playerNum,$rank){
        $first=0;
        $second =0;
        $third =0;
        $fourth =0;
        if($playerNum<3){
            $first =15;
             $data =array($first);
            
        }else if ($playerNum<6){
            $first = 20;
            $second= 15;
             $data =array($first,$second);
            
        }else if ($playerNum<9){
            $first = 25;
            $second= 20;
            $third = 15;
            $data =array($first,$second,$third);
            
        }else{

            $first = 30;
            $second =25;
            $third =20;
            $fourth = 15;
             $data =array($first,$second,$third,$fourth);
            
        }
        
        // $data =array($first,$second,$third,$fourth);
        if(count($data)<$rank){
            return -15;
        }else{
            
            return $data[$rank-1];
        }
        // return $data;
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
        function dealRank($gameType,$rank,$playNum){
            $scorePlu =0;
            $rankAdd =0;
            $gameTypePlu =0;
            //邀请赛不计算排位分
            if($gameType==4){
                return 0;
            }
            if($rank<=count($playNum)){
              $rankAdd = $playNum[$rank];
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
       
        $battleAmount = $GLOBALS['_CFG']['site']['battleAmount' . $gameType];
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
    function dealTicketFee($playUser,$ticketFee,$battleAmount)
    {

        // $ticketFee = $config['ticketFee'];
        // $battleAmount = $config['battleAmount'];
        // var_dump($config);
        // 判断是否所有对战用户都满足条件（ 用户余额>门票费用+对战金额）,体力>1
          $userInfos  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.parent1,a.parent2,a.parent3,u.club_id,u.id")//需要显示的字段
                        ->where(array('a.id' => array('IN', $playUser),'u.stamina'=>array('GT',0),'u.money' => array('EGT', $ticketFee + $battleAmount)))
                        ->select();

                        // var_dump(sizeof($userInfos));exit;
        if (sizeof($playUser) > sizeof($userInfos)) {
            throw new Exception('用户余额不足。', 1001);
        }

        //扣除用户门票以及对战金额费用。（对战金额提前扣除等结算时补回）active_point 加10 ，游戏总对局数加1,体力-1
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        $Model->execute("update dd_user set money=money-".($ticketFee + $battleAmount).", active_point=active_point+10,match_amount =match_amount +1,stamina = stamina -1 where user_id in (".implode(",",$playUser).")");
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


