<?php
namespace Home\Controller;

use Think\Controller;

class MpayController extends Controller {
   // protected $pay_config;
   /*
    * @param array $pay_config 配置数组
    */
    // public function __construct($pay_config)
    // {
    //     $this->pay_config=$pay_config;
    // }
    /*
     * 支付提交
     */
    public  function index(){
        $pay_config=array(
            'mchId'=>'000100004003000017', //账号
            'key'=>'02446c6dfbff4689bb3b806dab9828ee',//密钥
            'serverPayUrl'=>'https://mpay.onepaypass.com/aps/cloudplatform/api/trade.html',
            'serverQueryUrl'=>'https://api.onepaypass.com/aps/cloudplatform/api/trad
            e.html',
        //=======================退款服务地址
            'refundUrl'=>'https://api.onepaypass.com/aps/cloudplatform/api/trade.html',
        //=======================callback地址
            'callbackUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/pay.php?a=callback',
        //=======================notify地址
            'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/pay.php?a=notify',
        );
        $money = 0.02;
        switch ($_REQUEST['a']){
            case 'pay':
             pay($money);
             break;
            case 'callback':
             callback();
             break;
            case 'notify':
             notify();
             break;
        }
    }

    /*
     * 支付成功，异步通知
     */

}
