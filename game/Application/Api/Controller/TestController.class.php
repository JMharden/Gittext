<?php

namespace Api\Controller;


/**
 *
 * @package
 */
class TestController extends ApiController
{


  
    public function test(){
        // echo "123";exit;
       $val =  $_POST['val'];
       // var_dump($val);exit;
        if($val){
            // $data = M('user')->where(array('user_id'=>232))->find();
           echo json_encode(['status' => '1', 'msg' => 'OK','data'=>121123213213]);
        
        }


      }
    
      public function one(){
         $candy = M('user')->where(array('user_id'=>232))->field('candy,candy1,candy2')->find();
         sort($candy);
         // var_dump($candy);exit;
         echo json_encode(['status'=>1,'msg'=>'返回成功','candy'=>$candy]);
      }


       //娱乐赛历史战绩
  public function funHistory(){
    $user_id =232;
    if($user_id == null){
        echo "参数错误！！！";
      }
     // if(IS_POST){
        
        if(S('funHistory_' . $user_id)){
          $datas = S('funHistory_' . $user_id);
          // var_dump($datas);exit;
          
        }else{
          $play = M('fun_play_log')->where(array('user_id'=>$user_id,'status'=>2))->field('rank,score,end_time,user_id')->order('end_time desc')->select();
                  
          foreach ($play as $k => $v) {
            $userRank = M('user')->where(array('user_id'=>$v['user_id']))->getField('rank');
        
            $data = array(
              'end_time'  => date('m-d H:i',$v['end_time']),
              'score'     => $v['score'],
              'rank'      => $v['rank'],
            );
            $datas[]  = $data;
          }
          S('funHistory_' . $user_id, $datas, 18000);
        }
        
      
        echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$datas]);
     //  // }
     // }else{
     //    echo json_encode(['status'=>-1,'msg'=>'系统错误']);
     // }  
   }


}