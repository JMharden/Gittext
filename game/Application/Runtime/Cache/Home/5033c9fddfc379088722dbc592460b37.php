<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-CN">
<head>
    		<meta charset="utf-8">
	    <meta content="yes" name="apple-mobile-web-app-capable">
	    <meta content="black" name="apple-mobile-web-app-status-bar-style">
	    <meta content="telephone=no" name="format-detection">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no">
		<title>重置密码</title>
		
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
<meta name="poweredby" content="besttool.cn" />
</head>

<body style="background:#f2f2f2;">
<form method="post" name="form" id="form">
	<div class="tophead">
		重置密码
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
			<input type="password" name="login_pass2" placeholder="请再次输入密码" />
		</div>
	</div>
	<div class="btns">
		<input type="button" onclick="ajaxFormSubmit()" value="提 交" />
	</div>
	<div class="more">
		<a href="<?php echo U('login');?>">返回登陆</a><br/>
	</div>
</form>
<script>
var can_send = true;
function send_code(){
	
	if(!can_send)return false;
	can_send = false;
	
	mobile = $("#login_name").val();
	$.post("<?php echo U('send_code');?>",{mobile:mobile,act:'set_pass'},function(d){
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
		else{
			can_send = true;
		}
	});
}
</script>
	<script>
	window.shareData = {
		title : window.title,
		link : location.href,
		imgUrl:"<?php echo ($_CFG["site"]["url)"]); ?>/Public/images/logo.jpg",
		desc:'点击查看详情>>'
	}
	// window.shareData.title == undefined && window.shareData.title = document.title;
	</script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script>
	wx.config({
		debug: false,
		appId: "<?php echo ($jssdk['appId']); ?>",
		timestamp: <?php echo ($jssdk['timestamp']); ?>,
		nonceStr: '<?php echo ($jssdk['nonceStr']); ?>',
		signature: '<?php echo ($jssdk['signature']); ?>',
		jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','hideOptionMenu','hideMenuItems']
	});
	wx.ready(function () {
		//wx.hideOptionMenu();//menuItem:exposeArticle
		//wx.hideMenuItems({
		//	menuList: ['menuItem:exposeArticle','menuItem:share:appMessage'] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
		//});
		wx.checkJsApi({
			jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
			success: function(res) {
				//alert(JSON.stringify(res));
			}
		});
		wx.error(function(res){
			console.log('err:'+JSON.stringify(res));
			// config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

		});
		//分享给朋友
		wx.onMenuShareAppMessage({
			title: window.shareData.title, // 分享标题
			desc: window.shareData.desc, // 分享描述
			link: window.shareData.link, // 分享链接
			imgUrl: window.shareData.img, // 分享图标
			type: 'link', // 分享类型,music、video或link，不填默认为link
			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () { 
			},
			cancel: function () { 
				
			}
		});
		//分享到朋友圈
		wx.onMenuShareTimeline({
			title: window.shareData.title, // 分享标题
			link: window.shareData.link, // 分享链接
			imgUrl: window.shareData.img, // 分享图标
			success: function () { 
				// 用户确认分享后执行的回调函数
			},
			cancel: function () { 
				// 用户取消分享后执行的回调函数
			}
		});
	});
	</script>
	<?php echo ($_CFG["site"]["thirdcode"]); ?>
</body>
</html>