<?php

namespace Api\Controller;
use Api\Service\GameService;
use Think\Exception;

/**
 *
 * @package
 */
class NoticeController extends ApiController
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

    /**
     * 消息列表
     */
    public function noticeList(){
       $userId =  $GLOBALS['current_uid'];
        $Model = new \Think\Model();
       $res =  $Model->query("SELECT   o.*,   g.`status`   FROM   dd_notice_info o   LEFT JOIN dd_notice_log g ON o.id = g.msg_id    AND g.uid = ".$userId."  WHERE   o.send_time < CURRENT_TIME AND o.expire_time > CURRENT_TIME    AND g.msg_id IS NULL and  o.send_to  in ('all',".$userId.");");

       echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $res]);

    }


}