<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="x5-orientation" content="portrait">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>签到</title>
		<link rel="stylesheet" type="text/css" href="/res/zhuan/mystyle.css">

		<script type="text/javascript" src="/Public/js/jquery-1.7.2-min.js"></script>
	<meta name="poweredby" content="besttool.cn" />
</head>
	
	<body>
		<div id="sign" style="width: 100px;height: 40px;position: absolute;left: 150px;top: 150px;font-size: 20px;background-color:red; ">签到</div>
		<div id="integral" style="width: 100px;height: 40px;position: absolute;left: 150px;top:250px;font-size: 20px;background-color:blue;">领取积分</div>
		<div id="commission" style="width: 100px;height: 40px;position: absolute;left: 150px;top:350px;font-size: 20px;background-color:green;">领取佣金</div>
		<div id="receive" style="width: 100px;height: 40px;position: absolute;left: 150px;top:450px;font-size: 20px;background-color:green;">123</div>

		
		<script type="text/javascript" src="/Public/js/jquery.rotate.min.js"></script>
		
		<script>
			var url = 'http://tt.wapwei.com/';
			//签到
			$("#sign").click(function(){
				// alert(123);
			    // $("#click").text("is click"); 
	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url:  url+"index.php?m=Index&c=index&a=signs",
				        data:{'type':1},
			       	    success:function(data){
			              alert('签到成功');
				        },
						// error:function(data){
						//         alert('已签到');
					 //    },
				 })
		    });
		    //领取积分
		    $("#integral").click(function(){
				// alert(123);
			    // $("#click").text("is click"); 
	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url:  url+"index.php?m=Index&c=index&a=signs",
				        data:{'type':2},
			       	    success:function(data){
			              // alert('领取成功');
				        },
						// error:function(data){
						//         // alert('已签到');
					 //    },
				 })
		    });
		    //领取佣金
		    $("#commission").click(function(){
				// alert(123);
			    // $("#click").text("is click"); 
	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url:  url+"index.php?m=Index&c=index&a=signs",
				        data:{'type':3},
			       	    success:function(data){
			              // alert('签到成功');
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });
		    //测试开始游戏
		    $("#receive").click(function(){
				// alert(123);
			    // $("#click").text("is click"); 
	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url: url+"index.php?m=Index&c=index&a=startGame",
				        data:{'type':2,'status':2,'uid':[76,18],'result':[123,555],'game_id':456},
			       	    success:function(data){
			              console.log(data);
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });

		    
		</script>


	</body>

		
</html>