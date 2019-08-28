<?php

namespace Api\Controller;
use Api\Service\GameService;
use Think\Exception;

/**
 *
 * @package
 */
class GameController extends ApiController
{
  
   public function _initialize(){
       parent::_load_config();
       parent::write_log();
       $rand = $_GET['rand'];
       if($rand&&S('request_rand_'.$rand)){
           echo   json_encode(['status' => '-2', 'msg' => '重复提交']);
          exit;
       }
       if($rand){
           S('request_rand_'.$rand,$rand,300);
       }

   }

   public function randAi(){
        if(IS_POST){
            $num = $_POST['num'];

            $data = M('jiqiren')->select();
            if($num == 1){

              
              $data_rand[]  =  $data[18];
            }
            $datas = array_rand($data, $num);
            foreach($datas as $val){
             
              $data_rand[]=$data[$val];
            }
            echo json_encode(array('status'=>1,'msg'=>'返回成功','data'=>$data_rand));exit;
        }else{
            echo json_encode(array('status'=>-1,'msg'=>'系统错误'));exit;
        }
     
    }
    public function recovery(){

            $userId =  $_GET['userId'];
            $gameService =  new GameService();
            $data = $gameService->recovery($userId);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);

    }
     public function isMatchTime(){
        $times =  [
              ['start' => strtotime(date('Y-m-d 12:00:00')),'end'=>strtotime(date('Y-m-d 14:00:00'))],
              ['start' => strtotime(date('Y-m-d 14:00:00')),'end'=>strtotime(date('Y-m-d 21:00:00'))],
        ];

        $now = strtotime(date('Y-m-d H:i:s'));

        if($times[0]['start'] <= $now && $now <= $times[0]['end']){
     
             echo json_encode(['status'=>1,'msg'=>'已开放']);exit;
        
        }elseif($times[1]['start'] <= $now && $now <= $times[1]['end']){
         
             echo json_encode(['status'=>1,'msg'=>'已开放']);exit;
          
        }else{
            if($now  < $times[0]['start'] || $now  > $times[1]['end']){
                $msg = '下次开放时间为12点';
            
            }else{
                $msg = '下次开放时间为19点';
            }
             echo json_encode(['status'=>-1,'msg'=>$msg]);exit;
  
        }    
    }

    //创建竞技赛
    public function createMatch(){
       try{
           $gameType = $_POST['gameType'];
           $battleAmount= $_POST['battleAmount'];
           $playUser =  explode(',',$_POST['playUser']);
           // $slime_id =  explode(',',$_POST['slime_id']);
           // $datas = array_combine($playUser,$slime_id);
           foreach ($playUser as $k => $v) {
             $slime = M('user_slime')->where(array('u_id'=>$v,'is_check'=>1,'is_lock'=>1))->field('u_id,s_id,level,hat')->find();
             // $level = M('user_slime')->where(array('s_id' => $v,'u_id' => $k))->field('exp')->find();
             $levles[] = $slime;
           }
           $gameService =  new GameService();
           $data = $gameService->createMatch($playUser,$gameType,$battleAmount);
           $data['slime_level'] = $levles;
           // var_dump($data);exit;
           echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
       }catch (Exception  $e){
           echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
       }
    }
    //竞技赛结算
    public function gameSettle(){
        try{
      
            $matchId =  $_POST['matchId'];
            $user_id =  $_POST['user_id'];
            $rank    =  $_POST['rank'];
            $score   =  $_POST['score'];
            $is_finish   =  $_POST['is_finish'];
            $gameService =  new GameService();
            $data = $gameService->gameSettle($matchId,$user_id,$rank,$score,$is_finish);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
    
    //创建娱乐赛
    public function createFunMatch(){
       try{
           
            $playUser =  explode(',', $_POST['playUser']);
            // $slime_id =  explode(',', $_POST['slime_id']);
            // $datas = array_combine($playUser,$slime_id);
             foreach ($playUser as $k => $v) {
               $slime = M('user_slime')->where(array('u_id'=>$v,'is_check'=>1,'is_lock'=>1))->field('u_id,s_id,level,hat')->find();
               // $level = M('user_slime')->where(array('s_id' => $v,'u_id' => $k))->field('exp')->find();
               $levles[] = $slime;
             }
            $gameService =  new GameService();
            $data = $gameService->createFunMatch($playUser);
            $data['slime_level'] = $levles;
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
    //娱乐赛结算
    public function funGameSettle(){
        try{
            $matchId =  $_POST['matchId'];
            $score =  $_POST['score'];
            $user_id =  $_POST['user_id'];
            $rank =   $_POST['rank'];
            $is_finish   =  $_POST['is_finish'];
            $gameService =  new GameService();
            $data = $gameService->funGameSettle($matchId,$user_id,$rank,$score,$is_finish);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }


}
