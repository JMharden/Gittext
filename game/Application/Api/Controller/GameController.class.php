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
       $rand = $_GET['rand'];
       if($rand&&S('request_rand_'.$rand)){
           echo   json_encode(['status' => '-2', 'msg' => '重复提交']);
          exit;
       }
       if($rand){
           S('request_rand_'.$rand,$rand,300);
       }

   }

    public function recovery(){

            $userId =  $_GET['userId'];
            $gameService =  new GameService();
            $data = $gameService->recovery($userId);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);

    }
    public function createMatch(){
       try{
           $gameType =  $_POST['gameType'];
           $playUser =  explode(',', $_POST['playUser']);
           $battleAmount= $_POST['battleAmount'];
           $gameService =  new GameService();
           $data = $gameService->createMatch($playUser,$gameType,$battleAmount);
           echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
       }catch (Exception  $e){
           echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
public function gameSettle(){
        try{
          //  ($matchId, $result, $winner, $winnerId)
            $matchId =  $_POST['matchId'];
            $user_id =  $_POST['user_id'];
            $rank =  $_POST['rank'];
            $score =   $_POST['score'];
            $gameService =  new GameService();
            $data = $gameService->gameSettle($matchId,$user_id,$rank,$score);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
    // public function gameSettle(){
    //     try{
    //       //  ($matchId, $result, $winner, $winnerId)
    //         $matchId =  $_POST['matchId'];
    //         $winner =  $_POST['winner'];
    //         $winnerId =  $_POST['winnerId'];
    //         $result =   $_POST['result'];
    //         $gameService =  new GameService();
    //         $data = $gameService->gameSettle($matchId, $result, $winner, $winnerId);
    //         echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
    //     }catch (Exception  $e){
    //         echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
    //     }
    // }
      public function createFunMatch(){
       try{
            $playUser =  explode(',', $_POST['playUser']);
            $gameService =  new GameService();
            $data = $gameService->createFunMatch($playUser);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
    public function funGameSettle(){
        try{
          //  ($matchId, $result, $winner, $winnerId)
            $matchId =  $_POST['matchId'];
            $score =  $_POST['score'];
            $user_id =  $_POST['user_id'];
            $rank =   $_POST['rank'];
            $gameService =  new GameService();
            $data = $gameService->funGameSettle($matchId,$user_id,$rank,$score);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }


}
