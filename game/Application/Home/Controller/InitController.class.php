<?php


namespace Home\Controller;


use Think\Controller;


class InitController extends Controller {


    public function auto_login()
    {
//        echo '系统维护请稍等';die();
        $url = I('get.url');
        $openid = I('get.openid');
        $url = \Think\Crypt::decrypt($url,CashKey);
        $openid = \Think\Crypt::decrypt($openid,CashKey);
        //$url = I('get.url');
        //$url = $_GET['url'];
        if(!empty($openid)){

            $temp_user = M('user')->where(array('openid'=>$openid))->find();
            if(is_array($temp_user)){
                session('user',$temp_user);
                session('openid',$openid);
                if(empty($url)){
                    $url = __ROOT__.'/';
                }
                redirect($url);
                //redirect(str_replace('//','/',urldecode($url)));
            }
        }
    }

}