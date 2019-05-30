<?php

/**

 * Created by PE.

 * User: PE

 * Date: 2014/12/11

 * Time: 21:05

 */



namespace Common\Model;

use Think\Model;



class DomainModel extends Model {



    var $_cache_name;



    protected $_validate = array(

        array('domain','require','网址必须！',self::MUST_VALIDATE),

    );



    public function _initialize(){

        $this->_cache_name = __NAMESPACE__.'__'.__CLASS__;

    }



    public function getWhiteDomain(){

        $temp_arr = S($this->_cache_name.'_'.__FUNCTION__);

        if (!$temp_arr) {

            $where = array('is_lock'=>0);

            // $id = S($this->_cache_name.'_'.__FUNCTION__.'_id');
            // if ($id) {
                // $where['id'] = array('neq',$id);
            // }

            //$list = $this->where($where)->select();
            //$temp_arr = $this->_getDomain($list);
            //$temp_arr = array_rand($list,1);
            $temp_arr = $this->_getDomainOne();
            S($this->_cache_name.'_'.__FUNCTION__,$temp_arr,15);
            S($this->_cache_name.'_'.__FUNCTION__.'_id',$temp_arr['id'],5);
        }

        return $temp_arr;

    }



    private function _getDomainOne(){

        $where = array('is_lock'=>0,'is_home'=>0,'is_qr_code'=>0);

        $temp_array = $this->where($where)->order('id ASC')->find();

        //暂时直接返回成功

        //return $temp_array;

        if(is_array($temp_array)){

            if($this->_checkDomain($temp_array['domain'])){

                return $temp_array;

            }

            else{

                $this->where(array('id'=>$temp_array['id']))->setField('is_lock',1);

                return $this->_getDomainOne();

            }

        }

        else{

            return false;

        }

    }



    private function _getDomain($domain_list){

        if(count($domain_list) > 0){

            $temp_key = array_rand($domain_list);

            if($this->_checkDomain($domain_list[$temp_key]['domain'])){

                return $domain_list[$temp_key];

            }

            else{

                $this->where(array('id'=>$domain_list[$temp_key]['id']))->setField('is_lock',1);

                unset($domain_list[$temp_key]);

                return $this->_getDomain($domain_list);

            }

        }

        else{

            return false;

        }

    }



    private function _checkDomain($domain){

        if(!empty($domain)){

            $domain_arr = explode(':',$domain);

            return $this->_checkApi($domain_arr[0]);

        }

        return false;

    }



    private function _checkApi($domain){

        //http://api.wxyun.org/ph.do?url=360.com

//        $url = 'http://check.wxshare.cn/checkurl/index.php/Url?url='.$domain;

//        $ch = curl_init($url);

//        //curl_setopt ($ch, CURLOPT_URL, $url);

//        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5 );

//        curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 );

//        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false);

//        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false);

//        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);

//        $html_content = curl_exec($ch);

//        curl_close($ch);

//        //print_r($html_content);

//        if($html_content == 'success'){

//            return true;

//        }

//        return false;



        //Result  0正常  1白名单 2黑名单 3查询失败

        $url = 'http://vip.weixin139.com/weixin/1239241515.php?domain='.$domain;

        $ch = curl_init($url);

        //curl_setopt ($ch, CURLOPT_URL, $url);

        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5 );

        curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 );

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);

        $html_content = curl_exec($ch);

        curl_close($ch);

        //print_r($html_content);

        $html_content = json_decode($html_content,true);

        //$json=json_decode($html_content,true);

        //print_r($json);
        
        if($html_content['status'] != 2){

            return true;

        }

        return false;

    }

} 