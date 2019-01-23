<?php if (!defined('THINK_PATH')) exit(); if(C('LAYOUT_ON')) { echo ''; } ?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta content="telephone=no" name="format-detection">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no">

<title>跳转提示</title>
<style type="text/css">
.dragondean-loading {margin: 100px auto; margin-bottom:0; width: 55px; height: 60px; text-align: center; font-size: 10px; }
.dragondean-loading > div {  background-color: #67CF22;  height: 100%;  width: 6px; display: inline-block; -webkit-animation: stretchdelay 1.2s infinite ease-in-out; animation: stretchdelay 1.2s infinite ease-in-out; }
.dragondean-loading .rect2 { -webkit-animation-delay: -1.1s; animation-delay: -1.1s; }
.dragondean-loading .rect3 { -webkit-animation-delay: -1.0s; animation-delay: -1.0s; }
.dragondean-loading .rect4 { -webkit-animation-delay: -0.9s;  animation-delay: -0.9s; }
.dragondean-loading .rect5 { webkit-animation-delay: -0.8s; animation-delay: -0.8s; }
@-webkit-keyframes stretchdelay { 0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  20% { -webkit-transform: scaleY(1.0) } }
@keyframes stretchdelay { 0%, 40%, 100% {transform: scaleY(0.4); -webkit-transform: scaleY(0.4);  }  20% { transform: scaleY(1.0); -webkit-transform: scaleY(1.0); }}
.message{}
img.face{ width:40px; padding:10px; text-align:center; vertical-align:middle;}
</style>
<meta name="poweredby" content="besttool.cn" />
</head>
<body>
<div class="dragondean-loading"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div>
<div class="message" style=" text-align:center;">
	<?php if(isset($message)) {?>
	<img src="/Public/images/ok.jpg" class="face" />
	<?php echo($message); ?>
	<?php }else{?>
	<img src="/Public/images/error.jpg" class="face" />
	<?php echo($error); ?>
	<?php }?>
</div>

<div style=" position:fixed; bottom:10px; height:30px; line-height:30px; width:100%; text-align:center; color:#666;">
<b id="wait"><?php echo($waitSecond); ?></b> 后页面自动 <a style=" color:#7462B9; text-decoration:none;" id="href" href="<?php echo($jumpUrl); ?>">跳转</a>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>