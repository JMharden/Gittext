<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class JobController extends Controller {
    public function jobs(){
        $now = time();
        $start_time = strtotime(date('Y-m-d H:i')) - 60;
        $end_time = $now;
        

        $list = M('kailog')->where(array('endtime'=>array('lt',time())))->select();
        //print_r(array('starttime'=>$start_time,'endtime'=>$end_time));
        foreach ($list as $key => $kai_data) {
        if(is_array($kai_data) && $kai_data['status'] == 1){
            if( $kai_data['kid1']<=$kai_data['kid2'] && $kai_data['kid1']<=$kai_data['kid3'] ){
                $kaiid = 1;
            }
            if( $kai_data['kid2']<=$kai_data['kid1'] && $kai_data['kid2']<=$kai_data['kid3'] ){
                $kaiid = 2;
            }
            if( $kai_data['kid3']<=$kai_data['kid1'] && $kai_data['kid3']<=$kai_data['kid2'] ){
                $kaiid = 3;
            }
            if($kai_data['kid3']==$kai_data['kid2']&&$kai_data['kid3']==$kai_data['kid1']){
                $kaiid = rand(1,3);
            }
            $count = M('buylog')->where(array('kid'=>$kai_data['id']))->count();
            if($count <= 4){
                $kaiid = rand(1,3);
            }
            if($kai_data['kongid']!=0&&$kai_data['kongid']<=3){
                $kaiid = $kai_data['kongid'];
            }
            if($kaiid == 1){
                $name = '香蕉';
            }
            else if($kaiid == 2){
                $name = '西瓜';
            }
            else if($kaiid == 3){
                $name = '苹果';
            }
            //print_r($kai_data);
            M('kailog')->where(array('id'=>$kai_data['id']))->save(array('status'=>2,'isid'=>$kaiid,'name'=>$name));
            $list = M('buylog')->where(array('status'=>1,'kid'=>$kai_data['id']))->select();
             foreach ($list as $key => $val) {
                if($val['buyid']==$kaiid){
                    $list1 = M('buylog')->where(array('id'=>$val['id']))->find();
                    if($list1['status']==1) {
                        //$yingmoney = $val['money'] * 2.6;
                        $yingmoney = $val['money'] * 2.5;
                        M('buylog')->where(array('id' => $val['id']))->save(array('yingmoney' => $yingmoney, 'status' => 2, 'isid' => $kaiid, 'isname' => $name));
                    $userinfo = M('user')->where(array('id'=>$val['uid']))->find();
                    if($userinfo){
                        $msg = "恭喜您在".$val['kid']."期中奖，获到奖金".$yingmoney."元！\n时间:".date('Y-m-d H:i:s');
                        //sendwen($userinfo['openid'],$msg);
                        M('user')->where(array('id'=>$userinfo['id']))->save(array('money'=>$userinfo['money']+$yingmoney,'yingkui'=>$userinfo['yingkui']+$yingmoney));
                    }
                    }
                }else{
                    M('buylog')->where(array('id'=>$val['id']))->save(array('status'=>2,'isid'=>$kaiid,'isname'=>$name));
                }
            }  
         }
        }


        $kai_next_data = M('kailog')->where(array('status'=>1))->find();
        dump($kai_next_data);
        if(!is_array($kai_next_data)){
            // $num = M('all_num')->where(array('id'=>1))->find();
            // $all_mun = $num['all_num'] + 1;
            // $num['all_num'] = $all_mun;
            // M('all_num')->save($num);
            $kai_next_data['starttime'] = $end_time;
            $kai_next_data['endtime'] = $end_time + 63;
            //共计金额
            $kai_next_data['allmoney'] = 0;
            //下单量
            $kai_next_data['allnum'] = 0;
            //1进行，2结束
            $kai_next_data['status'] = 1;
            //香蕉金额
            $kai_next_data['kid1'] = 0;
            //西瓜金额
            $kai_next_data['kid2'] = 0;
            //苹果金额
            $kai_next_data['kid3'] = 0;
            //开奖结果文字
            $kai_next_data['isid'] = 0;
            //开奖结果文字
            $kai_next_data['name'] = '';
            //控制开奖
            $kai_next_data['kongid'] = 0;
            M('kailog')->add($kai_next_data);
        }
        
        
    }
    function  kaiqq(){
        $data = get_qqnum();
        $data2 = json_decode($data,true);
        $qqinfo = M('qqnum')->where(array('id'=>1))->find();
        if(($data2[0]['onlinetime']!=$qqinfo['time']&&is_array($data2))){
            M('qqnum')->where(array('id'=>1))->save(array('num1'=>$data,'num2'=>$qqinfo['num1'],'time'=>$data2[0]['onlinetime'],'uptime'=>time()));
            $id1 = substr($data2[0]['onlinenumber'],-1,1); 
            $id2 = substr($data2[0]['onlinenumber'],-2,1); 
            $id3 = substr($data2[0]['onlinenumber'],-3,1);

            //处理开奖结果
           $list = M('kailog')->order('id desc')->find();
           if($list['status']==1){
            //是否豹子
            $baozi = $id1.$id2.$id3;
            if( $baozi=='000' || $baozi=='111' ||$baozi=='222' ||$baozi=='333' ||$baozi=='444' ||$baozi=='555' ||$baozi=='666' ||$baozi=='777' ||$baozi=='888' ||$baozi=='999' ){
                $kaidata['isid2'] = 1;
            }else{
                $kaidata['isid2'] = 0;
            }
            //是否顺子
            $baozi = $id1.$id2.$id3;
            if( $baozi=='012' || $baozi=='123' ||$baozi=='234' ||$baozi=='345' ||$baozi=='456' ||$baozi=='567' ||$baozi=='678' ||$baozi=='789' ||$baozi=='987' ||$baozi=='876' ||$baozi=='765'||$baozi=='654'||$baozi=='543'||$baozi=='432'||$baozi=='321'||$baozi=='210'){
                $kaidata['isid7'] = 1;
            }else{
                $kaidata['isid7'] = 0;
            }
            //是否大
            $baozi = $id1+$id2+$id3;
            if( $baozi=='14' || $baozi=='15' ||$baozi=='16' ||$baozi=='17' ||$baozi=='18' ||$baozi=='19' ||$baozi=='20' ||$baozi=='21' ||$baozi=='22' ||$baozi=='23' ||$baozi=='24'||$baozi=='25'||$baozi=='26'||$baozi=='27'){
                $kaidata['isid10'] = 1;
            }else{
                $kaidata['isid10'] = 0;
            }
            //是否小
            $baozi = $id1+$id2+$id3;
            if( $baozi=='0' || $baozi=='1' ||$baozi=='2' ||$baozi=='3' ||$baozi=='4' ||$baozi=='5' ||$baozi=='6' ||$baozi=='7' ||$baozi=='8' ||$baozi=='9' ||$baozi=='10'||$baozi=='11'||$baozi=='12'||$baozi=='13'){
                $kaidata['isid4'] = 1;
            }else{
                $kaidata['isid4'] = 0;
            }
            //是否单
            $baozi = $id1+$id2+$id3;
            if( $baozi=='1' || $baozi=='3' ||$baozi=='5' ||$baozi=='7' ||$baozi=='9' ||$baozi=='11' ||$baozi=='13' ||$baozi=='15' ||$baozi=='17' ||$baozi=='19' ||$baozi=='21'||$baozi=='23'||$baozi=='25'||$baozi=='27'){
                $kaidata['isid5'] = 1;
            }else{
                $kaidata['isid5'] = 0;
            }
            //是否双
            $baozi = $id1+$id2+$id3;
            if( $baozi=='2' || $baozi=='4' ||$baozi=='6' ||$baozi=='8' ||$baozi=='10' ||$baozi=='12' ||$baozi=='14' ||$baozi=='16' ||$baozi=='18' ||$baozi=='20' ||$baozi=='22'||$baozi=='24'||$baozi=='26'||$baozi=='0'){
                $kaidata['isid9'] = 1;
            }else{
                $kaidata['isid9'] = 0;
            }
            //是否大单
            $baozi = $id1+$id2+$id3;
            if( $baozi=='15' || $baozi=='17' ||$baozi=='19' ||$baozi=='21' ||$baozi=='23' ||$baozi=='25' ||$baozi=='27' ){
                $kaidata['isid1'] = 1;
            }else{
                $kaidata['isid1'] = 0;
            }
            //是否大双
            $baozi = $id1+$id2+$id3;
            if( $baozi=='14' || $baozi=='16' ||$baozi=='18' ||$baozi=='20' ||$baozi=='22' ||$baozi=='24' ||$baozi=='26' ){
                $kaidata['isid8'] = 1;
            }else{
                $kaidata['isid8'] = 0;
            }
            //是否小单
            $baozi = $id1+$id2+$id3;
            if( $baozi=='1' || $baozi=='3' ||$baozi=='5' ||$baozi=='7' ||$baozi=='9' ||$baozi=='11' ||$baozi=='13' ){
                $kaidata['isid3'] = 1;
            }else{
                $kaidata['isid3'] = 0;
            }
            //是否小双
            $baozi = $id1+$id2+$id3;
            if( $baozi=='0' || $baozi=='2' ||$baozi=='4' ||$baozi=='6' ||$baozi=='8' ||$baozi=='10' ||$baozi=='12' ){
                $kaidata['isid6'] = 1;
            }else{
                $kaidata['isid6'] = 0;
            }
            M('kailog')->where(array('id'=>$list['id']))->save(array('status'=>2,'starttime'=>$data2[0]['onlinetime'],'number'=>$data2[0]['onlinenumber'],'isid'=>$id1+$id2+$id3,'idnum'=>$id1.$id2.$id3,'isid1'=>$kaidata['isid1'],'isid2'=>$kaidata['isid2'],'isid3'=>$kaidata['isid3'],'isid4'=>$kaidata['isid4'],'isid5'=>$kaidata['isid5'],'isid6'=>$kaidata['isid6'],'isid7'=>$kaidata['isid7'],'isid8'=>$kaidata['isid8'],'isid9'=>$kaidata['isid9'],'isid10'=>$kaidata['isid10']));
            $buylist = M('buylog')->where(array('status'=>1,'kid'=>$list['id']))->select();
            foreach ($buylist as $key => $vo) {
            $yingmoney = 0;
            if($vo['money']>0){
                if($vo['kid1']>0 && $kaidata['isid1']==1){
                    $yingmoney = $vo['kid1'] * 4 + $yingmoney;
                   
                }
                if($vo['kid2']>0 && $kaidata['isid2']==1){
                    $yingmoney = $vo['kid2'] * 60 + $yingmoney;
                   
                }
                if($vo['kid3']>0 && $kaidata['isid3']==1){
                    $yingmoney = $vo['kid1'] * 3 + $yingmoney;
                   
                }
                if($vo['kid4']>0 && $kaidata['isid4']==1){
                    $yingmoney = $vo['kid1'] * 2 + $yingmoney;
                   
                }
                if($vo['kid5']>0 && $kaidata['isid5']==1){
                    $yingmoney = $vo['kid5'] * 2 + $yingmoney;
                   
                }
                if($vo['kid6']>0 && $kaidata['isid6']==1){
                    $yingmoney = $vo['kid6'] * 4 + $yingmoney;
                   
                }
                if($vo['kid7']>0 && $kaidata['isid7']==1){
                    $yingmoney = $vo['kid7'] * 30 + $yingmoney;
                   
                }
                if($vo['kid8']>0 && $kaidata['isid8']==1){
                    $yingmoney = $vo['kid8'] * 3 + $yingmoney;
                   
                }
                if($vo['kid9']>0 && $kaidata['isid9']==1){
                    $yingmoney = $vo['kid9'] * 2 + $yingmoney;
                   
                }
                if($vo['kid10']>0 && $kaidata['isid10']==1){
                    $yingmoney = $vo['kid10'] * 2 + $yingmoney;
                   
                }

               
                $userinfo = M('user')->where(array('id'=>$vo['uid']))->find();
                if($userinfo){
                    if($yingmoney>0 && $vo['true']==1){
                        M('user')->where(array('id'=>$userinfo['id']))->save(array('money'=>$userinfo['money']+$yingmoney,'yingkui'=>$userinfo['yingkui']+$yingmoney));
                     }
                     if($yingmoney>0 && $vo['true']==0){
                        M('user')->where(array('id'=>$userinfo['id']))->save(array('money2'=>$userinfo['money2']+$yingmoney));
                     }    
               }
            }
             M('buylog')->where(array('id' => $vo['id']))->save(array('yingmoney' => $yingmoney, 'status' => 2, 'isid' => $id1+$id2+$id3));
        }
           }

             //创建新的
           $kai_next_data = M('kailog')->order('id desc')->find();
           
           if($kai_next_data['status']!=1){
            $kai_next_data['id'] = $list['id']+1;
            $kai_next_data['starttime'] = 0;
            //共计金额
            $kai_next_data['allmoney'] = 0;
            //下单量
            $kai_next_data['allnum'] = 0;
            //1进行，2结束
            $kai_next_data['status'] = 1;
            //香蕉金额
            $kai_next_data['kid1'] = 0;
            //西瓜金额
            $kai_next_data['kid2'] = 0;
            //苹果金额
            $kai_next_data['kid3'] = 0;
            //开奖结果文字
            $kai_next_data['isid'] = 0;
            $kai_next_data['idnum'] = 0;
            //开奖结果文字
            $kai_next_data['name'] = '';
            //控制开奖
            $kai_next_data['kongid'] = 0;
            M('kailog')->add($kai_next_data);
        }
        

    }
  
  }
}