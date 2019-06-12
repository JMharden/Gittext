<?php

namespace Api\Controller;
use Api\Service\ActivityService;
use Api\Service\GameService;
use Think\Exception;

/**
 *
 * @package
 */
class ActivityController extends ApiController
{


    /**
     * 领取累计登陆奖励
     */
    public function fetchReward(){

        $activityService =  new ActivityService();
        $data = $activityService->fetchLoginReward($_POST['user_id'],$_POST['activityId']);
        if($data){
            echo json_encode(['status' => '200', 'msg' => '领取成功', 'data' => $data]);
        }else{
            echo json_encode(['status' => '-1', 'msg' => '领取失败']);
        }

    }
    



}