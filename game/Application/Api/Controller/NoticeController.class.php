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
     * 公告
     */
    public function noticeList(){
        $Model = new \Think\Model();
        $res =  $Model->query("SELECT  id, title,content,category,biz_parameters  FROM   dd_notice_info o   WHERE  status='1' and o.send_time < CURRENT_TIMESTAMP AND o.expire_time > CURRENT_TIMESTAMP   ");
        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $res]);
    }



    /**
 * 消息未读列表
 */
    public function msgList(){
       // $userId =  $GLOBALS['current_uid'];
       $userId ='123';
        $Model = new \Think\Model();
        $res =  $Model->query("SELECT  o.id, title,content,category,biz_parameters,   g.`status`   FROM   dd_message_info o   LEFT JOIN dd_message_log g ON o.id = g.msg_id    AND g.uid = ".$userId."  WHERE   o.send_time < CURRENT_TIMESTAMP AND o.expire_time > CURRENT_TIMESTAMP    AND( g.msg_id IS NULL or g.status='1') and  o.send_to  in ('all',".$userId.");");
        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $res]);
    }

/**
 * 消息已读操作
 */
    public function read(){
        $userId =  $GLOBALS['current_uid'];
        $msgId =  $_POST['msgId'];
        $data = ['msg_id' => $msgId,
            'uid' => $userId,
            'status' => 1,
            'create_time' => NOW_TIME
        ];
        $res =M('message_log')->add($data);
        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $res]);
    }

    /**
     * 消息删除操作
     */
    public function del(){
      //  $userId =  $GLOBALS['current_uid'];
        $userId ='123';
        $msgId =  $_POST['msgId'];
        $noticeLog = M("message_log");
        $data['status'] = '2';
        $data['modify_time'] = NOW_TIME;
        $res = $noticeLog->where(array("uid"=>$userId,"msg_id"=>$msgId))->save($data);
        echo json_encode(['status' => '1', 'msg' => '返回成功', 'data' => $res]);
    }
}