<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="x5-orientation" content="portrait">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>搜索好友</title>
		<link rel="stylesheet" type="text/css" href="/res/zhuan/mystyle.css">

		<script type="text/javascript" src="/Public/js/jquery-1.7.2-min.js"></script>
	<meta name="poweredby" content="besttool.cn" />
</head>
	
	<body>
		<div class="container">
		<div id="receive" style="width: 100px;height: 60px;position: absolute;left: 150px;top:150px;font-size: 20px;">
			<input type="text" id="search" placeholder="请输入内容进行搜索">
			<button type="submit" onclick="search()">搜索</button>
		</div>
		<div id="addFriend" style="width: 100px;height: 60px;position: absolute;left: 150px;top:250px;font-size: 20px;">
			
			添加好友
		</div>
		<div id="applylist" style="width: 100px;height: 60px;position: absolute;left: 150px;top:350px;font-size: 20px;">
			
			获取好友列表
		</div>
		<div id="agree" style="width: 100px;height: 60px;position: absolute;left: 150px;top:450px;font-size: 20px;">
			
			同意添加
		</div>
		</div>
		<script type="text/javascript" src="/Public/js/jquery.rotate.min.js"></script>
		
		<script>
		//搜索好友
			function search(){
				var keyword=$("#search").val();
				$.ajax({
					type:"post",
					url:'http://tt.wapwei.com/api.php?m=Api&c=api&a=findFriend', 
					dataType:"json", 
					data:{'keyword':keyword},
					success: function (data) {
						console.log(data.data);
					}
					
				})
			}
			   //添加好友
		    $("#addFriend").click(function(){

	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url: "http://tt.wapwei.com/api.php?m=Api&c=api&a=addFriend",
				        data:{'uid':37,'fid':39,'text':'老铁加个好友呗'},
			       	    success:function(data){
			              console.log(data.data);
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });
	   		//添加好友
		    $("#applylist").click(function(){

	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url: "http://tt.wapwei.com/api.php?m=Api&c=api&a=applyList",
				        data:{},
			       	    success:function(data){
			              console.log(data.data);
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });
		    	   		//同意添加
		    $("#agree").click(function(){

	
			     $.ajax({
				        type: "post",
				        dataType: "json",
				        url: "http://tt.wapwei.com/api.php?m=Api&c=api&a=agree",
				        data:{},
			       	    success:function(data){
			              console.log(data.data);
				        },
						error:function(data){
						        // alert('已签到');
					    },
				 })
		    });
		</script>

	</body>

		
</html>