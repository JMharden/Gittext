<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>网站后台</title>
		<link rel="stylesheet" href="/Public/admin/css/style.default.css" type="text/css" />
		<link rel="stylesheet" href="/Public/plugins/bootstrap/css/bootstrap.font.css" type="text/css" />
		<script type="text/javascript" src="../../../Public/admin/js/plugins/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="/Public/admin/js/plugins/jquery-ui.js"></script>
		<script type="text/javascript" src="/Public/admin/js/plugins/jquery.cookie.js"></script>
        <script type="text/javascript" src="/Public/admin/js/plugins/jquery-plugin.js"></script>
		<script type="text/javascript" src="../../../Public/admin/js/custom/jquery.cookie.js"></script>
		<script type="text/javascript" src="/Public/admin/js/custom/general.js"></script>

		</script>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="/Public/admin/dist/sidebar-menu.css">
		<style type="text/css">
			.main-sidebar {
				position: absolute;
				position: absolute;
				top: 0;
				left: 0;
				height: 100%;
				min-height: 100%;
				width: 230px;
				z-index: 810;
				background-color: #222d32;
			}
			
			html,
			body {
				margin: 0;
				height: 100%;
			}
		</style>
	<meta name="poweredby" content="besttool.cn" />
</head>

	<body>
		<aside class="main-sidebar">
			<section class="sidebar">
				<ul class="sidebar-menu">
					<li class="header">蹦蹦史莱姆</li>

					<li>
						<a href="/index.php?m=Admin&amp;c=Admin&amp;a=dayReport"><i class="fa fa-dashboard"></i><span>系统首页</span></a>
					</li>

					<li class="treeview" onclick="index(0)">
						<a href="#">
							<i class="fa fa-cogs"></i>
							<span>系统设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu" style="display: none;">
							<li>
								<a href="/index.php?m=Admin&c=Config&a=web_site"><i class="fa fa-circle-o"></i>网站设置</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=Config&a=user"><i class="fa fa-circle-o"></i> 管理员设置</a>
							</li>
							<!-- <li><a href="/index.php?m=Admin&c=Config&a=pay_mp"><i class="fa fa-circle-o"></i>支付公众号</a></li> -->
					</li>


				</ul>
					<li class="treeview" onclick="index(1)">
						<a href="#">
							<i class="fa fa-user "></i> <span>用户管理</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=User&a=index"><i class="fa fa-circle-o"></i> 会员管理</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=User&a=feedback"><i class="fa fa-circle-o"></i>用户反馈</a>
							</li>
						
						</ul>
					</li>
					</li>
					<li class="treeview" onclick="index(2)">
						<a href="#">
							<i class="fa fa-gamepad"></i>
							<span>游戏设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=Config&a=site"><i class="fa fa-circle-o"></i> 大转盘</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=Config&a=hongbao"><i class="fa fa-circle-o"></i>红包设置</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=Config&a=withdraw"><i class="fa fa-circle-o"></i> 提现设置</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(3)">
						<a href="#">
							<i class="fa fa-comments"></i>
							<span>公众号设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=Config&a=mp"><i class="fa fa-circle-o"></i> 主公众号</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=Config&a=bei_mp"><i class="fa fa-circle-o"></i> 备份公众号</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(4)">
						<a href="#">
							<i class="fa fa-credit-card-alt"></i>
							<span>支付设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=Config&a=live_pay"><i class="fa fa-circle-o"></i> 生活圈支付</a>
							</li>
						</ul>
					</li>

					<li class="treeview" onclick="index(5)">
						<a href="#">
							<i class="fa fa-cloud"></i>
							<span>域名设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=Domain&a=index"><i class="fa fa-circle-o"></i> 游戏域名</a>
							</li>
							<li>
								<a href="/index.php?m=Admin&c=Domain&a=generalize"><i class="fa fa-circle-o"></i> 推广域名</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(6)">
						<a href="#">
							<i class="fa fa-cloud"></i>
							<span>商品管理</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/index.php?m=Admin&c=Product&a=index"><i class="fa fa-circle-o"></i> 商品列表</a>
							</li>
							<!-- <li>
								<a href="/index.php?m=Admin&c=Domain&a=generalize"><i class="fa fa-circle-o"></i> 推广域名</a>
							</li> -->
						</ul>
					</li>
					
				
				

					<li class="header">其他功能</li>
					<li>
						<a href="/index.php?m=Admin&c=Admin&a=clear_cache"><i class="fa fa-recycle text-yellow"></i> <span>[清除缓存]</span></a>
					</li>
					<li>
						<a href="/index.php?m=Admin&c=Index&a=logout"><i class="fa fa-reply-all text-aqua"></i> <span>[退出]</span></a>
					</li>
				</ul>
			</section>
		</aside>
		<!--修复选项卡点击关闭问题，导航栏页面重写-->
		<script type="text/javascript">			
			var aTreeview = document.getElementsByClassName("treeview")
			var atreeviewmenu = document.getElementsByClassName("treeview-menu")
			var a = 0;
			for(var i = 0; i < aTreeview.length; i++){
				aTreeview[i].setAttribute("onoff", "true");
			}
			var c = getCookie("name1");
			var d = getCookie("name2");
			if(c != null && c != undefined) {
				if(d = "false") {
					open(c);
				}
				a = c;
			}
			function index(num) {
				if(a!=num){
				close(a);//关掉上一个按钮
				open(num);
				}
				a = num; 																		
				if(aTreeview[num].getAttribute("onoff") == "true") {
					open(num)					
				} else{
					close(num)
				} 
				setCookie("name1", num) //储存点击的哪一个按钮
				setCookie("name2", aTreeview[num].getAttribute("onoff")) //这个地方是储存按钮的状态
			};
			/*设置按钮关闭*/
			function close(n) {
				aTreeview[n].setAttribute("onoff", "true")
				aTreeview[n].className = "treeview"; //关闭状态
				atreeviewmenu[n].className = "treeview-menu" //设置按钮关闭的状态
				atreeviewmenu[n].style.display = "none"
			}
			/*设置按钮打开*/
			function open(n) {
				aTreeview[n].setAttribute("onoff", "false") //初始化按钮让其属性为打开的
				aTreeview[n].className = "treeview active"; //表示按钮之前是开的，需打开
				atreeviewmenu[n].className = "treeview-menu menu-open" //设置按钮展开的状态
				atreeviewmenu[n].style.display = "block"
			}
			function setCookie(key, value) {
				document.cookie = key + "=" + value;
			}
			function getCookie(key) {
				var arr0 = document.cookie.replace(/\s/g, ""); //去除空格很重要;
				var arr1 = arr0.split(';');
				for(var i = 0; i < arr1.length; i++) {
					var arr2 = arr1[i].split('=');
					if(arr2[0] == key) {
						return decodeURI(arr2[1])
					}
				}
			}
			function removeCookie(key) {
				setCookie(key, '', -1);
			}
		</script>

		<script src="/Public/admin/dist/sidebar-menu.js"></script>
		<script>
			$.sidebarMenu($('.sidebar'))
		</script>
		<div class="centercontent">
			
<div id="contentwrapper" class="contentwrapper lineheight21">
  
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1"  colspan="2">*用户相关</th>
      </tr>
    </thead>
    <tbody>
     
      <tr>
        <td>今日新增用户</td>
        <td><?php echo ($user_count); ?>个</td>
        <td>用户总数</td>
        <td><?php echo ($user_count_all); ?>个</td>
        <td>自然注册人数</td>
        <td><?php echo ($user_nature); ?>个</td>
      </tr>
      <tr>
        <td>广告注册人数</td>
        <td>个</td>
        <td>邀请注册人数</td>
        <td><?php echo ($user_invite); ?>个</td>
        <td>分享注册人数</td>
        <td><?php echo ($user_share); ?>个</td>
      </tr>
      <tr>
        <td>新增留存率(1-2-3-4-5-6-7-15-30-60-90日留存率)</td>
        
        <td><?php echo ($one); ?>%,<?php echo ($two); ?>%,<?php echo ($three); ?>%,<?php echo ($four); ?>%,<?php echo ($five); ?>%,<?php echo ($six); ?>%,<?php echo ($seven); ?>%,</td>
      </tr>
      <tr>
        <td>活跃留存率(1-2-3-4-5-6-7-15-30-60-90日留存率)</td>
        <td>%,%,%,%,%,%,%,%,%,</td>
      </tr>
      <tr>
        <td>在线时长(1-5-10-30-60-120min的在线时长分布)</td>
        <td></td>
      </tr>
      <tr>
        <td>用户在线时间段分布(2个小时为时间段进行划分统计)</td>
        <td></td>
      </tr>
    
    </tbody>
  </table>
  <div class="contentwrapper lineheight21"></div>

  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">*游戏平台(娱乐赛)</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <td>分享点击人数</td>
        <td><?php echo ($fun_share_people); ?>人</td>
        <td>分享点击次数</td>
        <td><?php echo ($fun_share_num); ?>次</td>
      </tr>
     
      <tr>
        <td>浏览广告人数</td>
        <td><?php echo ($fun_advert_people); ?>人</td>
        <td>浏览广告次数</td>
        <td><?php echo ($fun_advert_num); ?>次</td>
      </tr>
      <tr>

      <tr>
        <td>开始游戏人数</td>
        <td></td>
        <td>开始游戏次数(开局1次，2次，3次，5次，10次，20次，30次，50次，50次以上的人数占比)</td>
        <td></td>
      </tr>
      <tr>
      <tr>
        <td>对局房间总数</td>
        <td><?php echo ($fun_game_count); ?>个</td>
        <td>对局总人数</td>
        <td><?php echo ($fun_people_count); ?>人</td>
      </tr>
    </tbody>
  </table>
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">*游戏平台(竞技赛)</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <td>浏览广告人数</td>
        <td><?php echo ($play_advert_people); ?>人</td>
        <td>浏览广告次数</td>
        <td><?php echo ($play_advert_num); ?>次</td>
      </tr>
      <tr>
        <td>10气泡数对局房间数</td>
        <td><?php echo ($ten_game_count); ?>个</td>
        <td>10气泡数对局房间人数</td>
        <td><?php echo ($ten_people_count); ?>人</td>
      </tr>
      <tr>
        <td>50气泡数对局房间数</td>
        <td><?php echo ($fifty_game_count); ?>个</td>
        <td>50气泡数对局房间人数</td>
        <td><?php echo ($fifty_people_count); ?>人</td>
      </tr>
      <tr>
        <td>100气泡数对局房间数</td>
        <td><?php echo ($hundred_game_count); ?>个</td>
        <td>100气泡数对局房间人数</td>
        <td><?php echo ($hundred_people_count); ?>人</td>
      </tr>
    </tbody>
  </table>
  <div class="contentwrapper lineheight21"></div>

 
</div>

<!--contentwrapper
		</div>
	</body>

</html>