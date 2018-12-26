<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html><html lang="zh-CN";><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="x5-orientation" content="portrait">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<link rel="dns-prefetch" href="https://static.gtimg.com/">
		 <script type="text/javascript" src="/Public/js/jquery-1.7.2-min.js"></script>
		<title>幸福·大转盘</title>
		<link rel="stylesheet" type="text/css" href="/res/zhuan/mystyle.css?54">		
		<style type="text/css">
 			 @media only screen and (min-width:320px) and (orientation:portrait){
    html{font-size:120px;}
              @media only screen and (max-width:320px) and (orientation:portrait){
    html{font-size:95px;｝
    	
		</style>
	<meta name="poweredby" content="besttool.cn" />
</head>
	<body>

		<header class="l-hd">
			 <div class="fixed_left">
    <div class="left_div">
        <p class="fixed_left_font">
            <span class="name" style="margin-left: -2px;">ID:<?php echo ($user["id"]); ?></span>
            <span class="balance" style="font-size:0.1rem;position: relative;"><span style="display: inline-block;position: absolute;top: 0">余额:¥<b class="amount"><?php echo ($user["money"]); ?></b></span><i style="width: 0.13rem;height:0.13rem;background: url(/res/zhuan/images/icon.png) no-repeat;background-size: 100% 100%;display: inline-block;background-color: rgb(230,120,120);border-radius: 50%;margin-left:0.85rem;margin-top:0.03rem;
            " class="littleaddbutn";></i></span>
            <a onclick="duixian()"><span class="cash_postal"><button>兑换</button></span></a>
            <a  href="<?php echo U('Suggest/index');?>"><span class="cash_postal"><button>投诉</button></span></a>
           <!--  <a href="/index.php?m=&c=Index&a=rechar"><span class="cash_postal"><button>充值</button></span></a> -->
        </p>
    </div>
</div>
<div class="toast dialog  toast5 " <?php if(empty($rechar)){ echo 'style="display:none;"';}?>>
	<!--新增充值页面-->
	<div class="shade"></div>
	<div class="bigger" style="min-hight:1rem;">
			<p class="cz">充值中心</p>
		<div style="padding-top:0.4rem;box-sizing: border-box;width:80%;height:85%;margin: auto;background: url('/res/zhuan/images/notice_bg.png') center 0px / 98% 100% no-repeat;">
			<div class="btnmony btnstyle" style="margin-top: 0.1rem;" onclick="remoneyChange('3','<?php echo ($user["id"]); ?>')">充值3金币</div>						
			<div class="btnmony btnstyle" onclick="remoneyChange('5')">充值5金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('10')">充值10金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('20')">充值20金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('50')">充值50金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('100')">充值100金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('500')">充值500金币</div>
			<div class="btnmony btnstyle btncolor" onclick="quxiao5()">取消</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	var d_flag = true;		
	//修复充值问题
	function remoneyChange(money){
		var a = 1;
		window.location = '/index.php?m=&c=Index&a=liveAccredit&money=' + money;		
	};
	function quxiao5(){
		$(".toast5").hide();	
	}
	$(".littleaddbutn").click(function(){
		$(".toast5").show();
	});
	//修复悦换图片消失问题
	function duixian() {
		if (d_flag) {
			d_flag = false;
		    $("#2_little").css('display','flex');
			$.post("/index.php?m=&c=Index&a=duixian", { money: money }, function(d) {
				d_flag = true;
				alert(d.info);
				if (d.status == 1) {
					window.location = '/';
				}
			}, 'json');
		}
	};	
</script>	            
			<div class="toast dialog cpm-hide">
					<div class="shade"></div>
					<div class="naChanceMsg_1" style="min-hight:1rem;">
						<!--<div class="publicClose"></div>-->
						<p class="pop_context"></p>
						
						<div class="btn_stype_1">
							<p class="goDraw" id="sureDraw">确认
						</p></div>
						<div class="btn_stype_2 cpm-hide">
							<p class="goDraw  sureDraw_left">确认
								
							</p><p class="goDraw sureDraw_right">取消
						</p></div>
					</div>
			</div>
			<div class="toast toast_tk cpm-hide">
					<div class="shade"></div>
					<div class="naChanceMsg_tk" style="min-hight:1rem;">
						<!--<div class="publicClose"></div>-->
						<p class="pop_context"></p>
						
						<div class="btn_stype_1_tk">
							<p class="goDraw tk">确认
						</p></div>
					</div>
			</div>				
		</header>
		<div id="ui-view"><div id="etDiv" class="all-contents">
	<div class="wrap_1">
		<!--<div class="follow-us toast">-->
			<div class="naChanceMsg" style="text-align: center;">
				<img src="/Public/images/kefu.jpg" style="height: auto;width: 80%;text-align: center;">
				<!-- <div class="naChanceMsg_txt">
					<p class="p_kf">客服</p>
					<p class="p_top">在线客服竭诚为您服务</p>
					<p class="p_time">客服在线时间  0:00-24:00</p>
				</div> -->
			</div>
		<!--</div>-->
	</div>
</div>
<div class="footer">
				<ul class="footer_ul">
					<a href="/">
                    <li class="footer_li footer_li_1" data-type="pay">
                       <span></span><p>抽奖</p>  
                    </li>
                    </a>
                    <a href="/index.php?m=&c=index&a=tixian">
                    <li class="footer_li footer_li_2  ">
                     <span></span><p>提现</p>      
                    </li>
                    </a>
                    <a href="/index.php?m=&c=index&a=usercode">
                    <li class="footer_li footer_li_3">
                      <span></span><p>代理赚钱</p>
                    </li>
                    </a>
                    <a href="/index.php?m=&c=index&a=daili">
                    <li class="footer_li footer_li_4 ">
                      <span></span><p>佣金</p>
                    </li>
                    </a>
                    <a href="/index.php?m=&c=index&a=kefu">
                    <li class="footer_li footer_li_5 footer_li_active" style="cursor:pointer">
                      <span></span><p>客服</p>
                    </li>
                    </a>
				</ul>
</div>
        <script>

	// 唤起微信支付


	function call_pay(param,url,id){

		param = eval('('+param+')');


		if(typeof url == 'undefined' || !url)url = location.href;


		if (typeof WeixinJSBridge == "undefined"){


			if( document.addEventListener ){


				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);


			}else if (document.attachEvent){


				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 


				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);


			}





		}else{


			WeixinJSBridge.invoke(


				'getBrandWCPayRequest',


				param,


				function(res){


					WeixinJSBridge.log(res.err_msg);


					if(res.err_msg == 'get_brand_wcpay_request:cancle'){


						alert('你取消了支付');


						location.href = '';


					}else if(res.err_msg == 'get_brand_wcpay_request:ok'){

						//alert('支付成功1');
                     $.post("/index.php?m=&c=Index&a=jiance",{},function(d){
			          if(d.status==1){
			          	 var userm = $('.amount').html();
                         userm = userm * 1+d.money*1;
                         $('.amount').html(userm);
                         $('#pid').val(d.id);
			             // $(".chou").trigger("click");
			             $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			             $(".toast3").show();

			            }else{
			             jiance();
			            }

			         },'json');
                      
			        	//location.href = '/';


					}else{


						//alert(res.err_msg)
						alert('取消支付');


						location.href = url;


					}


					


					


				}


			);


		}


	}
    function jiance(){
    	$.post("/index.php?m=&c=Index&a=jiance",{},function(d){
	    if(d.status==1){
	      	 var userm = $('.amount').html();
	         userm = userm * 1+d.money*1;
	         $('.amount').html(userm);
	         $('#pid').val(d.id);
	         alert(d.money);
	         //$(".chou").trigger("click");
	         $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			 $(".toast3").show();
	        }else{
	        setTimeout(function(){
			jiance1();
				},500);	
	         
	        }

	     },'json');
    }
    function jiance1(){
    	$.post("/index.php?m=&c=Index&a=jiance",{},function(d){
	    if(d.status==1){
	    	 alert(d.money);
	      	 var userm = $('.amount').html();
	         userm = userm * 1+d.money*1;
	         $('.amount').html(userm);
	         $('#pid').val(d.id);
	        // $(".chou").trigger("click");
	        $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			$(".toast3").show();
	        }else{
	          setTimeout(function(){
			  jiance();
				},500);
	        }

	     },'json');
    }

	/*function  jsApiCall(),

			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:cancle'){
					alert('你取消了支付');
					location.href = url;----------------------//
				}else if(res.err_msg == 'get_brand_wcpay_request:ok'){
					alert('支付成功2');
				    //	location.href = '/';
				}else{
					alert(res.err_msg)
					location.href = url;
				}
			}
		);
	}*/


	function clickPlantB(plant_id){


		<!--点击播种-->


		$.post("<?php echo U('do_plant');?>",{plant_id:plant_id,index:<?php echo ((isset($_GET['index']) && ($_GET['index'] !== ""))?($_GET['index']):0); ?>},function(d){


			layer.msg(d.info);


			// if(d.status == 1){


			// 	location.href = location.href;


			// }


		});


	}


	// 通用ajax表单提交


	function ajaxFormSubmit(seletor){


		if(!seletor || seletor == '')seletor = "form";


		data = $(seletor).serialize();


		layer.load(0, {shade: [0.1,'#fff']});


		$.post($(seletor).attr('action'),data,function(data){


			layer.closeAll();


			_index = layer.msg(data.info);


			if(data.url && data.url != ''){


				// 延迟一秒钟跳转


				setTimeout(function(){


					location.href = data.url;


				},1000)


			}


			else{


				setTimeout(function(){


					layer.close(_index);


				},3000)


			}


		})


	}


	


</script>

<!-- <script src="http://m.dqpps.com/js/wx.js"></script> -->


</body></html>