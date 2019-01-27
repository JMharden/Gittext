<?php
namespace Api\Controller;
use Think\Controller;


class ApiController extends Controller {


    

    public function index(){
    	if(IS_POST){
    		$type = $_POST['type'];
    		if($type == 1){
    			$data = array(
    				'name' => '傻逼',
    				'year' => 14
    			);
    			$aa = json_encode(array('status'=>1,'msg'=>'返回成功','data'=>$data));
    			echo  $aa;
    		}
    	}
    }

public function duan($integrl){
    if(0 <= $integrl&$integrl <= 100){
        echo '青铜';
    }elseif (101 <= $integrl&$integrl <= 200){
        echo '白银';
    }else{
        echo '黄金';
    }
}


    public function sign(){
   
        $this->display();
    }
    /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [查询领取记录]
     * @return   [type]     [description]
     */
     public function receive(){
       $wintegration   = M('user')->where(array('id'=>37))->getField('wintegration');               //有无待领取积分
       $pending_amount = M('account_info')->where(array('uid'=>37))->getField('pending_amount');    //有无待领取佣金
       $receive        = array(
            'wintegration'   => $wintegration,
             'pending_amount' => $pending_amount,
       );
       if($wintegration != 0 && $pending_amount != 0){
            echo json_encode(array('status'=>1,'msg'=>'有待领取积分和佣金','data'=>$receive));
       }else if($wintegration != 0 && $pending_amount == 0 ){
            echo json_encode(array('status'=>2,'msg'=>'有待领取积分','data'=>$wintegration));
       }else if($pending_amount != 0 && $wintegration == 0 ){
            echo json_encode(array('status'=>3,'msg'=>'有待领取佣金','data'=>$pending_amount));
       }else{
            echo json_encode(array('status'=>4,'msg'=>'无待领取积分和佣金'));
       }
     }
    /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [执行当天签到]
     * @return   [type]     [description]
     */

    public function signs(){
   
       
        if(IS_POST){
            $type = $_POST['type'];
            // var_dump($type);exit;
            if($type == 1){
                $signYn = $this->signYn(37);//今天是否签到
                if($signYn){
                     echo json_encode(array('status'=>2,'msg'=>'今日已签到'));
                }else{
                    $data=array(
                        'is_sign' => 1,
                        'sign_time'   =>strtotime(date('Y-m-d H:i:s',time())),
                        'uid'     =>37,
                    );
            
                    $sign = M('user_sign')->add($data);
                    if($sign){
                        M('user')->where(array('id'=>37))->setInc('wintegration',3);//修改用户积分

                        echo json_encode(array('status'=>1,'msg'=>'签到成功'));
                    }else{
                        echo json_encode(array('status'=>3,'msg'=>'签到失败'));
                    }
                }
            }else if($type == 2){
                $wintegration = M('user')->where(array('id'=>37))->getField('wintegration');
                if($wintegration == 0){

                    echo json_encode(array('status'=>-1,'msg'=>'无积分可领取'));
                }else{
                    $data = M('user')->where(array('id'=>37))->setInc('integration',$wintegration);
                    if($data){
                        $datas = M('user')->where(array('id'=>37))->setDec('wintegration',$wintegration);
                        echo json_encode(array('status'=>1,'msg'=>'领取成功'));
                    }
                }
            }else if($type == 3){
                $pending_amount = M('account_info')->where(array('uid'=>37))->getField('pending_amount');

                if($pending_amount == 0){

                    echo json_encode(array('status'=>-1,'msg'=>'无佣金可领取'));
                }else{
                    $data = M('account_info')->where(array('uid'=>37))->setInc('received_amount',$pending_amount);
                     // var_dump($data);exit;
                    if($data){
                        $time = date('Y-m-d H:i:s',time());
                        // var_dump($time);exit;
                        M('account_info')->where(array('uid'=>37))->setDec('pending_amount',$pending_amount);
                        M('account_info')->where(array('uid'=>37))->setField('last_receive_time',$time);
                        echo json_encode(array('status'=>1,'msg'=>'领取成功'));
                    }
                }
            }else{
                echo json_encode(array('status'=>0,'msg'=>'系统错误'));
            }
        }


    }
    /**
     * [time description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [签到记录判断]
     * @return   [type]     [description]
     */
    public function signYn($uid){
       $dateStr = date('Y-m-d', time());
       $timestamp0 = strtotime($dateStr); //当日0点的时间
       $timestamp24 = strtotime($dateStr) + 86400;   //当日24点的时间
        return M('user_sign')->where(array('uid'=>$uid,'sign_time'=>array('between',array($timestamp0,$timestamp24))))->find();
         
           
    }
    /**
     * [Receive description]
     * @Author   佳民
     * @DateTime 2019-01-14
     * @Function [领取积分]
     */
    public function integration(){
        $wintegration = M('user')->where(array('id'=>37))->getField('wintegration');
        if($wintegration == 0){

            echo json_encode(array('status'=>-1,'msg'=>'无积分可领取'));
        }else{
            $data = M('user')->where(array('id'=>37))->setInc('integration',$wintegration);
            if($data){
                $datas = M('user')->where(array('id'=>37))->setDec('wintegration',$wintegration);
                echo json_encode(array('status'=>1,'msg'=>'领取成功'));
            }
        }
    }
    /**
     * [Commission description]
     * @Author   佳民
     * @DateTime 2019-01-15
     * @Function [领取佣金]
     */
    public function Commission(){
        $pending_amount = M('account_info')->where(array('uid'=>37))->getField('pending_amount');

        if($pending_amount == 0){

            echo json_encode(array('status'=>-1,'msg'=>'无佣金可领取'));
        }else{
            $data = M('account_info')->where(array('uid'=>37))->setInc('received_amount',$pending_amount);
             // var_dump($data);exit;
            if($data){
                $time = date('Y-m-d H:i:s',time());
                // var_dump($time);exit;
                M('account_info')->where(array('uid'=>37))->setDec('pending_amount',$pending_amount);
                M('account_info')->where(array('uid'=>37))->setField('last_receive_time',$time);
                echo json_encode(array('status'=>1,'msg'=>'领取成功'));
            }
        }
    }


    /********      好友部分开始  start   ********/
      
    public function friend(){
        $this->display();
    }
     /**
      * [addFriend description]
      * @Author   佳民
      * @DateTime 2019-01-16
      * @Function [查找好友]
      */
    public function findFriend(){
        if(IS_POST){
            if(is_numeric($_POST['keyword'])){
                $aWhere['mobile'] = array('like','%'.$_POST['keyword'].'%');
           
            }
            else{
            
                $aWhere['nickname'] = array('like','%'.$_POST['keyword'].'%');
            }

        }
        $data =  M('user')->where($aWhere)->getField('id,nickname,mobile');
       
        echo json_encode(array('data'=>$data));
    }
     /**
      * [addFriend description]
      * @Author   佳民
      * @DateTime 2019-01-16
      * @Function [添加好友]
      */
    public function addFriend(){
        
        if(IS_POST){
             $sname = M('user')->where(array('id'=>$_POST['fid']))->getField('nickname');
             $data['uid'] = $_POST['uid'];//被申请人ID
             $data['sid'] = $_POST['fid'];//申请人ID
             $data['text'] = $_POST['text'];//申请说明
             $data['stime'] = strtotime(date('Y-m-d H:i:s',time()));
             $data['status'] = 1;
             // $data['sname']  = $sname;
             $addFriend = M('apply')->add($data);
             if($addFriend){
                echo json_encode(array('status'=>1,'msg'=>'等待验证'));
             }
        }
    }
    /**
     * [applyList description]
     * @Author   佳民
     * @DateTime 2019-01-16
     * @Function [申请列表]
     * @return   [type]     [description]
     */
    public function applyList(){
       
    $apply=M('apply')->alias('a')
                    ->join("dd_user u on a.sid=u.id") //附表连主表
                    ->field("u.nickname,a.status,a.sid,a.stime")
                    ->where(array('uid'=>39,'status'=>1))//需要显示的字段
                    ->select();

        echo json_encode(array('status'=>1,'msg'=>'获取成功','data'=>$apply)); 
    }

    public function agree(){
        if(IS_POST){
             // $data = M('apply')->where(array('id'=>1))->find();
        
            
             // $data['sname']  = $sname;
             $addFriend =  M('apply')->where(array('id'=>1))->setField('status',2);
             if($addFriend){
                echo json_encode(array('status'=>1,'msg'=>'添加成功'));
             }
        }
    }


    /********      好友部分结束  end   ********/




}