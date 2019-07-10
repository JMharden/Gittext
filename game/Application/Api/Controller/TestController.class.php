<?php

namespace Api\Controller;
use Think\Controller;
use Think\Db;

/**
 *
 * @package
 */
class TestController extends ApiController
{


  
    public function test(){
       $Model = new \Think\Model();
// select  count(b.id)/count(a.id)  from dd_user_base  a left join dd_user_base  b on a.id =b.id  and  DATE_FORMAT(a.join_time,'%Y-%m-%d') = '2019-07-01'  and b.last_login_time >'2019-07-04' where  DATE_FORMAT(a.join_time,'%Y-%m-%d') ='2019-07-01';
// select  count(*) from dd_user_base  where  DATE_FORMAT(last_time,'%Y-%m-%d') ='2019-07-04';
        $a =  $Model->query("SELECT  count(*) from dd_user_base  where  DATE_FORMAT(join_time,'%Y-%m-%d') ='2019-07-05';");
      $b =  $Model->query("SELECT  count(*) from dd_user_base  where  DATE_FORMAT(last_login_time,'%Y-%m-%d') ='2019-07-06';");
      $time = "2019-06-01";

      
//       $c = $Model->query("SELECT  
// ((SELECT  count(*) from dd_user_base  where  DATE_FORMAT(last_login_time,'%Y-%m-%d') ='2019-06-02')/
// (SELECT  count(*) from dd_user_base  where  DATE_FORMAT(join_time,'%Y-%m-%d') ='2019-06-01'))*100 as three;");


      $c = $Model->query(" SELECT count( b.user_id ) / count( a.id ) FROM dd_user_base a LEFT JOIN dd_login_log b ON a.id = b.user_id  AND DATE_FORMAT( a.join_time, '%Y-%m-%d' ) = '2019-06-30'  AND FROM_UNIXTIME( b.login_time, '%Y-%m-%d' ) = '2019-07-01' WHERE DATE_FORMAT( a.join_time, '%Y-%m-%d' ) = '2019-06-30';");
      
      var_dump($a);
      echo '<br/>';
      var_dump($b);
      echo' <br/>';
      var_dump($c);
     
      exit;


    }
      public function shareType(){
    // if(IS_POST){
      // $type = $_POST['type'];
         $time = date('Y-m-d H:i:s',time());
         var_dump($time);exit;
      $result = M('share')->limit(1)->order('rand()')->find();
    
      return $result;
      // echo json_encode(['status'=>1,'msg'=>'分享成功','data'=>$data]);
    // }
    
   }
   public function share(){
      $user_id =2142;
      $share = M('user')->where(array('user_id'=>$user_id))->getField('share');
      $nowUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
      // if(IS_POST){
        $type = $_POST['type'];
        $results = $this->shareType();
        if($type == 2){
          if($share < 1){
            echo json_encode(['status'=>-1,'msg'=>'分享次数不足']);exit;
          }
        }
        $data= array(
          'user_id' => $user_id,
          'action'  => $nowUrl,
          'type'    => $type,
          'create_time' => date('Y-m-d H:i:s')
        );
        $result = M('action_log')->add($data);
        if($type == 2 && $result){ //娱乐赛分享 
            M('user')->where(array('user_id'=>$user_id))->setDec('share',1);
            M('user')->where(array('user_id'=>$user_id))->setInc('stamina',3);
            
        }
        echo json_encode(['status'=>1,'msg'=>'分享成功','data'=>$results]);exit;
      // }
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
public function haha(){
  $a = 1234567890;
  echo number_format($a);
}
    function bubble_sort(){
          $array = ['1','2','3','4','5','6'];
          $count = count($array);

          if ($count <= 0) return false;

          for($i=0; $i<$count; $i++){

               for($j=$count-1; $j>$i; $j–){

                   if ($array[$j] < $array[$j-1]){

                       $tmp = $array[$j];

                       $array[$j] = $array[$j-1];

                       $array[$j-1] = $tmp;

                   }

               }

          }

        echo $array;

      }

      function quick_sort($array) {
// $array = ['1','2','3','4','5','6'];
        if (count($array) <= 1) return $array;

        $key = $array[0];

        $left_arr = array();

        $right_arr = array();

        for ($i=1; $i;$i++){    

            if ($array[$i] <= $key){

                $left_arr[] = $array[$i];

            }else{  

                $right_arr[] = $array[$i];

            }

        }

        $left_arr = quick_sort($left_arr);

        $right_arr = quick_sort($right_arr);

        return array_merge($left_arr, array($key), $right_arr);

        }
          

}