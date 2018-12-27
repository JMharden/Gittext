<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-CN">
<head>
    		<meta charset="utf-8">
	    <meta content="yes" name="apple-mobile-web-app-capable">
	    <meta content="black" name="apple-mobile-web-app-status-bar-style">
	    <meta content="telephone=no" name="format-detection">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no">
		<title>注册</title>
		
		<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
		<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>

		<link rel="stylesheet" href="/Public/css/reset.css" />
		<!--script type="text/javascript" src="/Public/plugins/layer_mobile/layer.js" >
		</script-->
		<script type="text/javascript" src="/Public/plugins/layer/layer.js" ></script>
		<style>
		.head{ padding:10px; height:50px; position:relative; padding-left:70px; background:#fff; position:relative;}
		.head img.headimg{ width:50px; height:50px; border-radius:50%; position:absolute; top:10px; left:10px;}
		.head .nickname{ font-size:16px;}
		.head .time{ color:#666; font-size:12px;}
		.head .id{ position:absolute; top:10px; right:10px;}
		
		.page a{ background:#f1f1f1; padding:3px 5px; margin-right:5px;}
		
		/*底部*/
		.footer-blank{ height:50px;}
		.footer{ z-index:999; height:50px; background:#ff8a00; color:#fff; position:fixed; width:100%; bottom:0; left:0;}
		.footer a{ color:inherit;}
		.footer ul{
			list-style:none; padding:0; margin:0; text-align:center;
			display:-moz-box; /* Firefox */
			display:-webkit-box; /* Safari and Chrome */
			display:box;
		}
		.footer ul li{
			font-size:14px;
			font-family: "微软雅黑";
			line-height:50px;
			-moz-box-flex:1.0; /* Firefox */
			-webkit-box-flex:1.0; /* Safari 和 Chrome */
			box-flex:1.0;
		}
		.footer ul li span{font-size:16px; padding-top:5px; color: #fff; margin-right:10px;}
		</style>
	<link href="/Public/css/public.css" rel="stylesheet" type="text/css" />
	<style>
	.item input{ box-sizing:border-box;}
	</style>
<meta name="poweredby" content="besttool.cn" />
</head>

<body style="background:#f2f2f2;">
<form method="post" name="form" id="form">
	<div class="tophead">
		注册
	</div>
	<div class="main">
		<div class="item">
			<span class="glyphicon glyphicon-user"></span>
			<input type="text" name="login_name" id="login_name" placeholder="请输入手机号" />
		</div>
		<div class="item code-box">
			<span class="glyphicon glyphicon-refresh"></span>
			<input type="text" name="code" placeholder="请输入验证码" />
			<a href="javascript:;" id="send_btn" onclick="send_code()" class="send_btn">获取验证码</a>
		</div>
		<div class="item">
			<span class="glyphicon glyphicon-lock"></span>
			<input type="password" name="login_pass" placeholder="请输入登陆密码" />
		</div>
		<div class="item">
			<span class="glyphicon glyphicon-lock"></span>
			<input type="password" name="login_pass2" placeholder="再次输入密码" />
		</div>
		<?php if($_GET['parent'] == ''): ?><div class="item">
			<span class="glyphicon glyphicon-tags"></span>
			<input type="text" name="parent" placeholder="推荐人ID" />
		</div><?php endif; ?>
	</div>
	<div class="btns">
		<?php if($_GET['parent'] != ''): ?><input type="hidden" name="parent" value="<?php echo ($_GET['parent']); ?>" /><?php endif; ?>
		<input type="button" onclick="ajaxFormSubmit()" value="提 交" />
	</div>
	<div class="more">
		<a href="<?php echo U('login');?>">已有账号?点此登陆</a><br/>
	</div>
</form>
<script>
var can_send = true;
function send_code(){
	
	if(!can_send)return false;
	can_send = false;
	
	mobile = $("#login_name").val();
	$.post("<?php echo U('send_code');?>",{mobile:mobile,act:'reg'},function(d){
		alert(d.info);
		if(d.status==1){
			$("#send_btn").text('60秒后重试');
			$("#send_btn").css('color','#9A9C9E');
			var left_time = 60;
			var count_down = setInterval(function(){
				left_time --;
				if(left_time<=0){
					left_time = 60;
					clearInterval(count_down);
					can_send = true;
					
					$("#send_btn").text('重发验证码');
					$("#send_btn").css('color','#337ab7');
				}
				else{
					$("#send_btn").text(left_time+'秒后重试');
				}
			},1000);
		}
	});
}
</script>
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


</body>
</html>