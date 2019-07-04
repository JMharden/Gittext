<?php
namespace Api\Controller;
use Think\Controller;
class CodeController extends Controller {

   
    // public function __construct($appid = '', $secret = '', $source = 8, $width = '430')
    // {   
    //     $this->_load_config();
       
    //     $appid = $this->_bei_mp['appid']  ;
    //     $secret = $this->_bei_mp['appsecret'];
    //     $source  = $this->source  ;
    //     $width = $this->width ;
    // }
    
     protected function _load_config()
    
    {
        $_CFG = S('sys_config');
        if (empty($_CFG) || APP_DEBUG) {
            $config = M('config')->select();
            if (!is_array($config)) {
                die('请先在后台设置好各参数');
            }
            foreach ($config as $v) {
                $_CFG[$v['name']] = unserialize($v['value']);
            }
            unset($config);
            S('sys_config', $_CFG);
        }
        // 循环将配置写道成员变量
        foreach ($_CFG as $k => $v) {
            $key = '_' . $k;
            $this->$key = $v;
        }
        $this->assign('_CFG', $_CFG); // 指配到模板
        $GLOBALS['_CFG'] = $_CFG;        // 保存到全局变量
    } 
       
    //获取access_token
    public function get_access_token()

    {
        $this->_load_config();
       
        $appid = $this->_bei_mp['appid']  ;
        $secret = $this->_bei_mp['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $data = $this->curl_get($url);
        $result = json_decode($data,true);

       
        return $result;
    }

    public function curl_get($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $data;
    }
    //获得二维码
    public function get_qrcode() {
        //header('content-type:image/gif');
        //header('content-type:image/png');格式自选，不同格式貌似加载速度略有不同，想加载更快可选择jpg
        header('content-type:image/jpg');
        // $id = $this->id;
        $data = array();
        // $source = 8;
        $data['page'] = "";  //参数跳转到product/show，产品详情
        $data['scene'] = 8;
        $data['width'] = 280;
        $data = json_encode($data);
      
        $access = $this->get_access_token();
        $access_token= $access['access_token'];

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access['access_token'];
        $da = $this->get_http_array($url,$data);
        
    }
    public function get_http_array($url,$post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //没有这个会自动输出，不用print_r();也会在后面多个1
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $out = json_decode($output);
        return $out;
    }
        //把请求发送到微信服务器换取二维码
  public function httpRequest($url, $data='', $method='GET'){
    $curl = curl_init();  
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
    if($method=='POST')
    {
        curl_setopt($curl, CURLOPT_POST, 1); 
        if ($data != '')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }
    }

    curl_setopt($curl, CURLOPT_TIMEOUT, 30);  
    curl_setopt($curl, CURLOPT_HEADER, 0);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    $result = curl_exec($curl);  
    curl_close($curl);  
    return $result;
  } 

}