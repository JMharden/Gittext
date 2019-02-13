<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="x5-orientation" content="portrait">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>公告</title>
		<link rel="stylesheet" type="text/css" href="/res/zhuan/mystyle.css">

		<script type="text/javascript" src="/Public/js/jquery-1.7.2-min.js"></script>
	<meta name="poweredby" content="besttool.cn" />
</head>
	
	<body>
		<div>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="width:100%;height: 50px;display: inline-flex;">
				<div style="width: 55%;margin-right: 40px;"><?php echo ($vo["title"]); ?></div>
				<div style="width:35%"><?php echo ($vo["send_time"]); ?></div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		<script type="text/javascript" src="/Public/js/jquery.rotate.min.js"></script>
		
		<script>
			//签到
			$("#sign").click(function(){
				// alert(123);
			    // $("#click").text("is click"); 
	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url: "http://tt.wapwei.com/api.php?m=Api&c=api&a=signs",
				        data:{'type':1},
			       	    success:function(data){
			              // alert('签到成功');
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });



		    
		</script>

	</body>

		
</html>