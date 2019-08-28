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
    function createMatch($playUser, $gameType,$battleAmount)
    {
     
        if (!($playUser || $gameType || sizeof($playUser) >1)) {
            throw new Exception('参数错误。', 1001);
        }
        // $is_time = $this->isTime();
               
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
        $playUsers = sizeof($playUser);
        // if($playUsers <5){
        //     $playUsers = 5;
        // }
        $data = ['match_id' => $matchId,
            'ticket_fee' => $ticketFee,
            'player_num' => $playUsers,
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
    function gameSettle($matchId, $user_id, $rank,$score,$is_finish)
    {
        //$resultJson = json_decode($result,true);
        //判断游戏是否存在, 参数是否正常（玩家id能对应上）
        $gameLog = M('play_match_info')->where(array("match_id" => $matchId))->find();

        // var_dump($gameLog);exit;
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
       
         $bonusRatio  = $this -> dealBonus($playNum, $gameLog['battle_amount']);
         
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
        $userUpdateInfo["crystal"] = $bonus;
        $userRank = M('user')->where(array('user_id'=>$user_id))->getfield('rank');
        $rankNow = $this->getDuan($userRank);

        $res=array(
            'user_id' => $user_id,
            'score'=>$score,
            'rank' =>$rank,//排名
            // 'ranks' =>$ranks,//段位分
            'level'   => $rankNow['level'],
            'rankHigh' => $rankNow['max'] - $rankNow['min'],
            'rankLow' => $userRank -$rankNow['min'],
            'crystal'=>$bonus
        );
         $datas= array(
            'user_id' => $user_id,
            'score'   => $score,
            'is_finish'=>$is_finish,
            'rank'    =>  $rank ,
            'bonu'    =>'+'.$bonus,
            'start_time' => $gameLog['create_time'],
            'end_time'=> NOW_TIME,//游戏开始时间
            'type'=>$gameLog['type'],
            'status'  =>2,
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
    function createFunMatch($playUser){
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
function funGameSettle($matchId,$user_id,$rank,$score,$is_finish)
{
    // $resultJson = json_decode($result,true);
    //判断游戏是否存在, 参数是否正常（玩家id能对应上）
    $gameLog = M('fun_match_info')->where(array("match_id" => $matchId))->find();
    $userRank = M('user')->where(array('user_id'=>$user_id))->getfield('rank');
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
          // var_dump($candyNum);exit;
          $rankNum  = $this -> dealRankByNum($playNum,$rank);
          
          if($rank<=count($candyNum)){
                // $crystal = $candyNum[$rank-1];
                $ranks    = $rankNum[$rank-1];
                if($userRank < abs($ranks)){
                    $rankss  = '-'.$userRank;
                }else{
                    $rankss  = $ranks;
                }
                // var_dump($ranks);exit;
                if($is_finish==1){

                    $crystal = $candyNum[$rank-1]+2;
                    
                }else{

                    $crystal = $candyNum[$rank-1];
                    
                }
                //排名第一增加胜局数
                if($rank==1){
                   M('user')->where(array('user_id' => $user_id))->setInc('fun_win_amount',1);
                }
                M('user')->where(array('user_id' => $user_id))->setInc('crystal',$crystal);//修改水晶数
                M('user')->where(array('user_id' => $user_id))->setInc('rank',$rankss);//修改段位分
           }
         
        //记录游戏数据 (个人数据 放单独字段，玩家所有对局记录存 data里)
        $datas= array(
            'user_id' => $user_id,
            'score' => $score,
            'is_finish'=>$is_finish,
            'rank' => $rank,
            'ranks' => $ranks,
            'crystal' => '+'.$crystal,
            'start_time' => $gameLog['create_time'],
            'end_time' => NOW_TIME,//游戏开始时间
            'type'=>$gameLog['type'],
            'status'=>2,
            'match_id'=>$matchId
        );
        
        $rankNow = $this->getDuan($userRank);

        //结果计算
        $res=array(
            'user_id' => $user_id,
            'score'=>$score,
            'rank' =>$rank,//排名
            'ranks' =>$ranks,//段位分
            'level'   => $rankNow['level'],
            'rankHigh' => $rankNow['max'] - $rankNow['min'],
            'rankLow' => $userRank -$rankNow['min'],
            'crystal'=>$crystal//加水晶
        );

        M('fun_play_log')->add($datas);
        return $res;


}

    /**
     * 竞技赛水晶分配
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
        if($battleAmount == 10){
            if($playerNum==2){
                $data = array(20,0);
        
            }else if ($playerNum==3){
                $data = array(20,10,0);
        
            }else if ($playerNum==4){
                $data = array(24,16,0,0);
        
            }else if ($playerNum==5){
                $data = array(24,16,10,0,0);
        
            }
            else if ($playerNum==6){
                $data = array(26,18,16,0,0,0);
        
            }else if ($playerNum==7){
                $data = array(26,18,16,10,0,0,0);

            }else if ($playerNum==8){
                $data = array(26,20,18,16,0,0,0,0);

            }else if ($playerNum==9){
                $data = array(26,20,18,16,10,0,0,0,0);

            }else{
                $data = array(27,21,19,17,16,0,0,0,0,0);
          
            }
        }else if ($battleAmount == 50) {
            if($playerNum==2){
                $data = array(100,0);
   
            }else if ($playerNum==3){
                $data = array(100,50,0);
        
            }else if ($playerNum==4){
                $data = array(120,80,0,0);
        
            }else if ($playerNum==5){
                $data = array(120,80,50,0,0);
        
            }else if ($playerNum==6){
                $data = array(130,100,80,0,0,0);
        
            }else if ($playerNum==7){
                $data = array(130,100,80,50,0,0,0);

            }else if ($playerNum==8){
                $data = array(130,100,90,80,0,0,0,0);

            }else if ($playerNum==9){
                $data = array(130,100,90,80,50,0,0,0,0);

            }else{
                $data = array(135,105,95,85,80,0,0,0,0,0);
          
            }
        }else if ($battleAmount == 100){
            if($playerNum==2){
                $data = array(1000,0);
   
            }else if ($playerNum==3){
                $data = array(1000,100,0);
        
            }else if ($playerNum==4){
                $data = array(1200,160,0,0);
        
            }else if ($playerNum==5){
                $data = array(1200,160,100,0,0);
        
            }else if ($playerNum==6){
                $data = array(1300,180,160,0,0,0);
        
            }else if ($playerNum==7){
                $data = array(1300,180,160,100,0,0,0);

            }else if ($playerNum==8){
                $data = array(1300,200,180,160,0,0,0,0);

            }else if ($playerNum==9){
                $data = array(1300,200,180,160,100,0,0,0,0);

            }else{
                $data = array(1350,210,190,170,160,0,0,0,0,0);
          
            }
        }else{
           echo "下注水晶不存在";
        }
        return $data; 
        

        
    }
    /**
     * 排位赛结算水晶
     */

    function dealCandyByNum($playerNum){

        if($playerNum<6){
            $data = array(6,5,4,3,2);
   
        }else if ($playerNum==6){
            $data = array(7,6,5,4,3,2);
    
        }else if ($playerNum==7){
            $data = array(8,7,6,5,4,3,2);

        }else if ($playerNum==8){
            $data = array(9,8,7,6,5,4,3,2);

        }else if ($playerNum==9){
            $data = array(10,9,8,7,6,5,4,3,2);

        }else{
            $data = array(11,10,98,7,6,5,4,3,2);
      
        }
        return $data;
    }
    /**
     * [结算段位分 description]
     * @Author   duke
     * @DateTime 2019-08-23T10:22:45+0800
     * @param    [type]                   $playerNum [description]
     * @param    [type]                   $rank      [description]
     * @return   [type]                              [description]
     */
    function dealRankByNum($playerNum,$rank){
        if($playerNum<6){
            $data = array('+40','+30','+0','-35','-40' );
        }else if ($playerNum==6){
            $data = array('+40','+35','+30','-30','-35','-40');
        }else if ($playerNum==7){
            $data = array('+40','+35','+30','+0','-30','-35','-40');
        }else if ($playerNum==8){
            $data = array('+45','+40','+35','+30','-30','-35','-40','-45');
        }else if ($playerNum==9){
            $data = array('+45','+40','+35','+30','+0','-30','-35','-40','-45');
        }else{
            $data = array('+50','+45','+40','+35','+30','-30','-35','-40','-45','50');
        }
        return $data;
    }
 
     /**rank分转换为段位
     * @param $rank
     *
     */
      function getDuan($level){
          $filter = [
            ['level' => '青铜',  'min' => 0,  'max' => 99],
            ['level' => '白银',  'min' => 100,  'max' => 239],
            ['level' => '黄金',  'min' => 240,  'max' => 509],
            ['level' => '铂金',  'min' => 510,  'max' => 899],
            ['level' => '钻石',  'min' => 900,  'max' => 1529],
            // ['level' => '大师',  'min' => 1530,  'max' => 2129],
            ['level' => '王者',  'min' => 1530,  'max' => 5000],
          ];

          $result = search($level, $filter);

          return  current($result);
     }
      function getDuans($level){
          $filter = [
            ['level' => '青铜III','min' => 0,    'max' => 29],
            ['level' => '青铜II', 'min' => 30,   'max' => 59],
            ['level' => '青铜I',  'min' => 60,   'max' => 99],

            ['level' => '白银III','min' => 100,  'max' => 139],
            ['level' => '白银II', 'min' => 140,  'max' => 179],
            ['level' => '白银I',  'min' => 180,  'max' => 239],

            ['level' => '黄金IV', 'min' => 240,  'max' => 299],
            ['level' => '黄金III','min' => 300,  'max' => 359],
            ['level' => '黄金II', 'min' => 360,  'max' => 419],
            ['level' => '黄金I',  'min' => 420,  'max' => 509],

            ['level' => '铂金IV',   'min' => 510,  'max' => 599],
            ['level' => '铂金III',  'min' => 600,  'max' => 689],
            ['level' => '铂金II',   'min' => 690,  'max' => 779],
            ['level' => '铂金I',    'min' => 780,  'max' => 899],

            ['level' => '钻石V',   'min' => 900,  'max' => 1019],
            ['level' => '钻石IV',  'min' => 1020,  'max' => 1139],
            ['level' => '钻石III', 'min' => 1140,  'max' => 1259],
            ['level' => '钻石II',  'min' => 1260,  'max' => 1379],
            ['level' => '钻石I',   'min' => 1380,  'max' => 1529],

            // ['level' => '大师V',   'min' => 1530, 'max' => 1679],
            // ['level' => '大师IV',  'min' => 1680, 'max' => 1829],
            // ['level' => '大师III', 'min' => 1830, 'max' => 1979],
            // ['level' => '大师II',  'min' => 1980, 'max' => 2129],
            // ['level' => '大师I',   'min' => 2130, 'max' => 2309],

            ['level' => '王者',   'min' => 1530, 'max' => 5000],
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
        // 判断是否所有对战用户都满足条件（ 用户余额>门票费用+对战金额）,体力>1
          $userInfos  = M('user_base')->alias('a')
                        ->join("dd_user u on a.id=u.user_id") //附表连主表
                        ->field("a.parent1,a.parent2,a.parent3,u.club_id,u.id")//需要显示的字段
                        ->where(array('a.id' => array('IN', $playUser),'u.stamina'=>array('GT',0),'u.crystal' => array('EGT', $ticketFee + $battleAmount)))
                        ->select();

                        
        if (sizeof($playUser) > sizeof($userInfos)) {
            throw new Exception('用户水晶不足。', 1001);
        }
        //扣除用户门票以及对战金额费用。（对战金额提前扣除等结算时补回）active_point 加10 ，游戏总对局数加1,体力-1
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        $Model->execute("update dd_user set crystal=crystal-".($ticketFee + $battleAmount).", active_point=active_point+10,match_amount =match_amount +1,stamina = stamina -1 where user_id in (".implode(",",$playUser).")");
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


