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
   
    public function _initialize(){
       // parent::_initialize();
       parent::_load_config();
       

   }
      public function isTime($now,$user_id){
        $times =  [
              ['start' => strtotime(date('Y-m-d 12:00:00')),'end'=>strtotime(date('Y-m-d 12:30:00'))],
              ['start' => strtotime(date('Y-m-d 16:00:00')),'end'=>strtotime(date('Y-m-d 18:30:00'))],
        ];
        $now = strtotime(date('Y-m-d H:i:s'));
        $robbone = M('robbing')->where(array('user_id'=>$user_id,'time'=>array('between',array($times[0]['start'],$times[0]['end']))))->find();
        $robbtwo = M('robbing')->where(array('user_id'=>$user_id,'time'=>array('between',array($times[1]['start'],$times[1]['end']))))->find();
   
          if($times[0]['start'] <= $now && $now <= $times[0]['end']){
            if($robbone){
               return false;
              // echo json_encode(['status'=>-2,'msg'=>'您已参与，请明日再来！']);exit;
            }else{
              return ture;        
            }
          }elseif($times[1]['start'] <= $now && $now <= $times[1]['end']){
            if($robbtwo){
               return false;
              // echo json_encode(['status'=>-2,'msg'=>'您已参与，请明日再来！']);exit;
            }else{
              return ture;       
            }

          }else{
              return false;
          }
      
        
                
      }
      public function robbing(){
        $user_id = 231;
        if(IS_POST){
          $time = $this->isTime($now,$user_id);
          $type    = intval($_POST['type']);
          // $crystal = intval($_POST['crystal']);
          $crystal = S('robbing'.$user_id);
          // var_dump($crystal);exit;
          if($time){
              if($type == 1){ //开始
                $crystal = rand(10,100);
                S('robbing'.$user_id,$crystal,1800);
                echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$crystal]);exit;                  
              }else{//结束       
                if($crystal == null){
                  echo json_encode(['status'=>2,'msg'=>'参数错误']);exit;
                } 
                 $data = array(
                  'user_id' => $user_id,
                  'time'    => time(),
                  'crystal' => $crystal
                );
                M('robbing')->add($data);
                M('user')->where(array('user_id'=>$user_id))->setInc('crystal',$crystal);
                S('robbing'.$user_id,null);
              }
              echo json_encode(['status'=>2,'msg'=>'恭喜获得','data'=>$crystal]);exit;
          }else{
             echo json_encode(['status'=>-1,'msg'=>'暂未开放']);exit;
          }
        }
    }
    public function addSlime(){
    
          $data = M('slime')->where(array('id'=>1))->find();
          // var_dump($data);exit;
          $other = M('slime')->where(array('id'=>array('GT',1)))->select();
          $add[] = array(
            's_id' => $data['id'],
            'name' => $data['name'],
            'skill'=> $data['skill'],
            'blood'=> $data['blood'],
            'blue' => $data['blue'],
            'exp' =>  50,
            'u_id' => $user_id,
            'openid'=>$openid,
            'is_lock'=>1,
            'is_check'=>1
          );
        
        foreach ($other as $k => $v) {
          $others = array(
            's_id' => $v['id'],
            'name' => $v['name'],
            'skill'=> $v['skill'],
            'blood'=> $v['blood'],
            'blue' => $v['blue'],
            'exp' =>  50,
            'u_id' => $user_id,
            'openid'=>$openid,
            'is_lock'=>0,
            'is_check'=>0
          );
          $result[] = $others;
        }
        array_splice($result,0,0,$add);

        M('user_slime')->addAll($result);

   }
   public function clubRecords(){

   
      $user_id = 231;
      $is_club_owner =  M('user')->where(array('user_id'=>$user_id))->getField('is_club_owner');
      $Model = new \Think\Model();
      // if($is_club_owner == 1){
      $infomation =  $Model->query("SELECT  o.id, o.title,o.content,o.create_time,o.category,o.create_user,   g.`status`   FROM   dd_message_info o   LEFT JOIN dd_message_log g ON o.id = g.msg_id    AND g.uid = ".$user_id."  WHERE   ( g.msg_id IS NULL or g.status='1') and  o.send_to  in ('all',".$user_id.") and o.category in(1,2) and o.send_time < CURRENT_TIMESTAMP AND o.expire_time > CURRENT_TIMESTAMP order by id desc;");
          $data = array_column($infomation,'id');
      foreach($data as $k=>$v){
        $id=$v['id'];
        
        $infomation[$k]['create_time']= $this->getTime($infomation[$k]['create_time']);

      }
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$infomation]);
      
    }
  public function test(){
     $time = $_GET['time'];
     echo json_encode(['status'=>1,'data'=>$time]);
  }
  public function test1(){
     $time = $_POST['time'];
     echo json_encode(['status'=>1,'data'=>$time]);
  }
   //离线时间
  public function leaveTime(){
      $data = M('user_base')->alias('a')
                          ->join("dd_receive u on a.id=u.user_id") //附表连主表
                          ->field('a.last_login_time,u.receive_time')
                          ->where(array('u.user_id'=>2142))
                          ->find();
                          
      $start = strtotime($data['receive_time']);
      $end   = strtotime($data['last_login_time']);
      $leaveTime = $end - $start;
      if($leaveTime >= 172800){
        $leaveTime = 172800;
      }
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$leaveTime]);exit;          
  }

  //用户金币
  public function currency(){
      $user_id = 2142;
      $data = M('user')->where(array('user_id'=>$user_id))->field('money,stamina,crystal')->find();
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);exit;
  }
 //用户收益领取
  public function receive(){
      $user_id = 2142;
      if(IS_POST){
        $money = intval($_POST['money']);
        
        $data = M('user')->where(array('user_id'=>$user_id))->setInc('money',$money);
        if($data){
          echo json_encode(['status'=>1,'msg'=>'领取成功']);exit;
        }else{
          echo json_encode(['status'=>1,'msg'=>'领取失败']);exit;
        }
      }
      
  }

  //购买商品
  public function buy(){
      if(IS_POST){ 
        $number  = $_POST['number'];
        $type    = $_POST['type'];
        $price   = intval($_POST['price']);
        $user_id = 2142;  
        
        if(empty($_POST['number']) || empty($_POST['type']) || empty($_POST['price'])){
          echo json_encode(['status'=>-2,'msg'=>'参数错误']);exit;
        }
        $product = M('product')->where(array('number'=>$number,'type'=>$type))->find();
        if(!$product){
         echo json_encode(['status'=>-1,'msg'=>'商品不存在']);exit;
        }
        $crystal = M('user')->where(array('user_id'=>$user_id))->getField('crystal');
        if($crystal>=$price){
          if($type == 1){//购买史莱姆
            M('user_slime')->where(array('s_id'=>$number+1,'u_id'=>$user_id))->setfield('is_lock',1);        
            
          }elseif($type == 2){//购买饰品
             $robe = M('robe')->where(array('user_id'=>$user_id))->find();
              $result = array(
              'user_id' => $user_id,
              'hat'     => $robe['hat'].$number.','
            );
             if($robe){
                  M('robe')->where(array('user_id'=>$user_id))->save($result);
             }else{
                  M('robe')->add($result);
             }
          }else{
            echo json_encode(['status'=>-2,'msg'=>'参数错误']);exit;
          }

          $crystals = M('user')->where(array('user_id'=>$user_id))->setDec('crystal',$price);
          if($crystals){
            $buyinfo = array(
              'user_id' => $user_id,
              'p_num'   => $number,
              'type'    => $type,
              'money'   => $price
            );
            $buy_log = M('buy_log')->add($buyinfo);
            if($buy_log){
              echo json_encode(['status'=>1,'msg'=>'购买成功']);exit;
            }            
          }
           
        }else{
            echo json_encode(['status'=>-3,'msg'=>'您的钻石不足']);exit;
        }
             
      }
  }

   /**商品相关**/
    public function product(){
      $data = M('product')->field('number,type,price')->order('id desc')->select();
      echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
    }
    //史莱姆列表
    public function slimeList(){
        $user_id = 2142;
        $slime = M('user_slime')->where(array('u_id'=>$user_id))->field('s_id,is_lock,level,is_check,hat')->select();
        foreach ($slime as $k => $v) {
          $slime[$k]['s_id'] = $slime[$k]['s_id'] - 1 ;
        }
        $slime_robe = M('robe')->where(array('user_id'=>$user_id))->field('user_id,hat')->find();   
        $slime_robe['hat'] = explode(',', $slime_robe['hat']);
        $is_check =  M('user_slime')->where(array('u_id'=>$user_id,'is_check'=>1))->getField('s_id');  
        $data = array('slime_list'=>$slime,'slime_robe'=>$slime_robe,'is_check'=>intval($is_check)-1);
        echo json_encode(['status'=>1,'msg'=>'返回成功','data'=>$data]);
    }
//升级史莱姆
    public function upSlime(){
      $user_id = 2142;
      if(IS_POST){
        $sid = $_POST['s_id']+1;
        $price  = intval($_POST['price']);
        $money = M('user')->where(array('user_id'=>$user_id))->getField('money');
      
        if($price > $money){
          echo json_encode(['status'=>-1,'msg'=>'气泡不足']);exit;
        }else{
            
          $data = M('user')->where(array('user_id'=>$user_id))->setDec('money',$price); //扣除用户糖果数量
          $up = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$sid))->setInc('level',1); //增加经验值  

          if($up){
             echo json_encode(['status'=>1,'msg'=>'升级成功']);exit;

          }else{
             echo json_encode(['status'=>-2,'msg'=>'升级失败']);exit;
          }
        }
      }
    }
    //选择出战slime
    public function checkSlime(){
      if(IS_POST){
        $slime_id = $_POST['slime_id']+1;
        $user_id  = 2142;
        $result = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>$slime_id))->setfield('is_check',1);
        if($result){
          $is_check = M('user_slime')->where(array('u_id'=>$user_id,'s_id'=>array('neq',$slime_id)))->setfield('is_check',0);
          if($is_check){
            echo json_encode(['status'=>1,'msg'=>'切换成功']);exit;
          }else{
            echo json_encode(['status'=>1,'msg'=>'切换失败']);exit;
          }
        }
      }
    } 
    //装饰饰品
    public function checkRobe(){
      if(IS_POST){
        $slime_id = $_POST['slime_id']+1;
        $user_id = 2142;
        $data = array(
          'hat'=>$_POST['hat']
        );
        $hat = M('robe')->where(array('user_id'=>$user_id))->getField('hat');
        if(!$hat){
          echo json_encode(['status'=>-1,'msg'=>'暂无该饰品']);exit;
        }else{
          $result = M('user_slime')->where(array('s_id'=>$slime_id,'u_id'=>$user_id))->save($data);
          if($result){
            echo json_encode(['status'=>1,'msg'=>'装扮成功']);exit;
          }
        }
      }
    }


    public function   chou(){
      $user_id  = 2142;
      // M('zhuan')->where(array('id'=>array('GT',0)))->delete();exit;
      if(IS_POST){
        $crystal = intval($_POST['crystal']);
         // var_dump($GLOBALS['_CFG']['hongbao']);exit;
        $money = M('user')->where(array('user_id'=>$user_id))->getField('crystal');
        // var_dump($money);exit;
        if($crystal>$money){
          echo json_encode(['status'=>-1,'msg'=>'钻石不足']);exit;
        }else{
          // M('user')->where(array('user_id'=>$user_id))->setDec('crystal',$crystal);
          $rand = rand(1,100);
          $daytime = strtotime(date("Y-m-d",time()));
         
          $shouru = M('zhuan')->where(array('addtime'=>array('gt',$daytime)))->sum('money'); 
          $zhichu = M('zhuan')->where(array('addtime'=>array('gt',$daytime)))->sum('ying');
          var_dump($shouru);
          echo '<br/>';
           var_dump($zhichu);
          echo '<br/>';

          $jian = $crystal*2;
       
          $lirun = ($shouru-($zhichu+$jian))/$shouru-((2-rand(1,3))*rand(1,10)/1000); 

          var_dump($lirun);
          echo '<br/>';
           var_dump($GLOBALS['_CFG']['hongbao']['lirun']*1);
          echo '<br/>';
   
          if($lirun < $GLOBALS['_CFG']['hongbao']['lirun']*1){
            $rand = rand(1,100*($GLOBALS['_CFG']['hongbao']['zhong01']+$GLOBALS['_CFG']['hongbao']['zhong05']));
           var_dump($rand);
            echo '<br/>';exit;
          }
          
          if($rand<=$GLOBALS['_CFG']['hongbao']['zhong01']*100){
            // var_dump($GLOBALS['_CFG']['hongbao']['zhong01']*100);
             $zhuan = rand(275,355);
             $ying = 0.1 * $crystal;
          }
          if( ($rand>$GLOBALS['_CFG']['hongbao']['zhong01']*100) && ($rand <= ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100) )){
            // var_dump($GLOBALS['_CFG']['hongbao']['zhong01']*100);
            // var_dump($GLOBALS['_CFG']['hongbao']['zhong05']*100);
            // exit;
             $zhuan = rand(185,265);
             $ying = 0.5 * $crystal;
          }
          if( ($rand> ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100) )  && $rand<= ($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100+$GLOBALS['_CFG']['hongbao']['zhong21']*100)  ){
             $zhuan = rand(95,175);
             $ying = 2.1 * $crystal;
          }
          if($rand>($GLOBALS['_CFG']['hongbao']['zhong01']*100+$GLOBALS['_CFG']['hongbao']['zhong05']*100+$GLOBALS['_CFG']['hongbao']['zhong21']*100)&&$rand<=100){
             $zhuan = rand(5,85);
             $ying = 3.6 * $crystal;
          }

        }
        $add['uid']  = $user_id;   
        $add['type'] = 1;   
        $add['money']= $crystal;   
        $add['ying'] = $ying;   
        $add['rand'] = $rand;     
        $add['addtime'] = time();  
        $result = M('zhuan')->add($add); 
        if($result){
          M('user')->where(array('user_id'=>$user_id))->setDec('crystal',$crystal);
          M('user')->where(array('user_id'=>$user_id))->setInc('crystal',$ying);    
        }
        echo json_encode(['status'=>1,'msg'=>'恭喜获得'.$ying.'红包']);exit;
      }

    }
  
    



     
          

}