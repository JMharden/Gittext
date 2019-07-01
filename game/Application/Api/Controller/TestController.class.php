<?php

namespace Api\Controller;
use Think\Controller;

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

 

      //排行榜
      // public function rankList(){
        
      //   $data = M('user_base')->alias('a')
      //                   ->join("dd_user u on a.id=u.user_id") //附表连主表
      //                   ->field('a.nickname,a.headimg,u.money')
      //                   ->limit(0,10)
      //                   ->order('u.money desc')
      //                   ->select();
      //   $Model = new \Think\Model();
      //   $funscore =  $Model->query("SELECT  a.score,a.user_id   FROM    dd_fun_play_log a left join dd_fun_play_log b on a.user_id = b.user_id  group by a.score having a.score=max(b.score) order by score desc limit 0,10;");

      //   $playscore =  $Model->query("SELECT  a.score,a.user_id   FROM    dd_play_log a inner join dd_play_log b on a.user_id = b.user_id  group by a.score having a.score=max(b.score) order by score desc limit 0,10;");

      //   $score   =  array_merge($funscore,$playscore);

      //  array_flip(arsort($score));
      //  array_keys($score);
      
      //  foreach ($score as $k => $v) {
      //   $user = M('user_base')->where(array('id'=>$v['user_id']))->field('nickname,headimg')->find();
      //   $datas['nickname'] = $user['nickname'];
      //   $datas['headimg']  = $user['headimg'];
      //   $datas['score']    = $v['score'];
      //   $result[]  =  $datas; 
      //    # code...
      //  }
      //  var_dump($result);
      // }
    

}