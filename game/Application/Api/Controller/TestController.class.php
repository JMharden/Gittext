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


}