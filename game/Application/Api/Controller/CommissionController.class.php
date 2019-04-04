<?php

namespace Api\Controller;
use Api\Service\CommissionService;
use Api\Service\GameService;
use Think\Exception;

/**
 *
 * @package
 */
class CommissionController extends ApiController
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

   public function      unclaimedRecord(){
           $userId =  $_GET['userId'];
           $commissionService =  new CommissionService();
           $data = $commissionService->unclaimedRecord($userId);
           echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);

    }
    public function receive(){
        try{
            $userId =  $_GET['userId'];
            $commissionService =  new CommissionService();
            $data = $commissionService->receive($userId);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }

    public function      withDraw(){
        try{
            $userId =  $_GET['userId'];
            $amount =  $_GET['amount'];
            $commissionService =  new CommissionService();
            $data = $commissionService->withDraw($userId,$amount);
            echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $data]);
        }catch (Exception  $e){
            echo json_encode(['status' => '-1', 'msg' => $e->getMessage()]);
        }
    }
}