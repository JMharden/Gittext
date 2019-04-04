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
           $gameType =  $_GET['gameType'];
           $playUser =  $_GET['playUser'];
           $gameService =  new GameService();
           $data = $gameService->createMatch($playUser,$gameType);
           echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
       }catch (Exception  $e){
           echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }

    public function gameSettle(){
        try{
          //  ($matchId, $result, $winner, $winnerId)
            $matchId =  $_POST['matchId'];
            $winner =  $_POST['winner'];
            $winnerId =  $_POST['winnerId'];
            $result =   $_POST['result'];
            $gameService =  new GameService();
            $data = $gameService->gameSettle($matchId, $result, $winner, $winnerId);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }


}