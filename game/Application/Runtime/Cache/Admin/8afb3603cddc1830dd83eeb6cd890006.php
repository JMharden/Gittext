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
			        <div class="pageheader notab">
            <h1 class="pagetitle">站点设置</h1>
            <span class="pagedesc">设置网站的基本信息</span>
        </div><!--pageheader-->
        <div id="contentwrapper" class="contentwrapper lineheight21">
            <form class="stdform stdform2" method="post">
				<p>
					<label>网站名称</label>
					<span class="field"><input type="text" name="name" id="name" value="<?php echo ($_CFG["web_site"]["name"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>免费获取二维码</label>
					<span class="field">
					<input type="radio" name="code_is" value='1' <?php if($_CFG['web_site']['code_is']==1){ echo 'checked';}?>>开启
                    <input type="radio" name="code_is"  value='0' <?php if($_CFG['web_site']['code_is']==0){ echo 'checked';}?>>关闭
					</span>
				</p>
               <p>
					<label>开奖</label>
					<span class="field">
					<input type="radio" name="kaijiang" value='1' <?php if($_CFG['web_site']['kaijaing']==1){ echo 'checked';}?>>随机
                    <input type="radio" name="kaijiang"  value='2' <?php if($_CFG['web_site']['kaijiang']==2){ echo 'checked';}?>>最少
					</span>
				</p>
				 <p>
					<label>提现</label>
					<span class="field">
					<input type="radio" name="tixian" value='1' <?php if($_CFG['web_site']['tixian']==1){ echo 'checked';}?>>开启
                    <input type="radio" name="tixian"  value='0' <?php if($_CFG['web_site']['tixian']==0){ echo 'checked';}?>>关闭
					</span>
				</p>
                <p>
					<label>网站</label>
					<span class="field">
					<input type="radio" name="is_site" value='0' <?php if($_CFG['web_site']['is_site']==0){ echo 'checked';}?>>关闭
                    <input type="radio" name="is_site"  value='1' <?php if($_CFG['web_site']['is_site']==1){ echo 'checked';}?>>开启
					</span>
				</p> 
				<p>
					<label>网站地址</label>
					<span class="field"><input type="text" name="url" id="url" value="<?php echo ($_CFG["web_site"]["url"]); ?>" class="smallinput" /></span>
				</p>
<!-- 				<p>
					<label>关注时回复关键词<small>关注时自动回复此关键词对应的内容</small></label>
					<span class="field"><input type="text" name="subscribe" id="subscribe" value="<?php echo ($_CFG["web_site"]["subscribe"]); ?>" class="smallinput" /></span>
				</p> -->
				<p>
					<label>佣金比率<small>比如：8;4;3;表示1级8%,2级4%,3级3%</small></label>
					<span class="field"><input type="text" name="expense" id="expense" value="<?php echo ($_CFG["web_site"]["expense"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>黑名单跳转网址<small>加http://</small></label>
					<span class="field"><input type="text" name="black_url" id="black_url" value="<?php echo ($_CFG["web_site"]["black_url"]); ?>" class="smallinput" /></span>
				</p>
				<p class="stdformbutton">
					<button class="submit radius2">提交</button>
					<input type="reset" class="reset radius2" value="重置" />
				</p>
			</form>
			
			<script src="/Public/plugins/ueditor1.4.3/ueditor.config.js"></script>
			<script src="/Public/plugins/ueditor1.4.3/ueditor.all.min.js"></script>
			<script>
				ue = UE.getEditor('chou_body');
			</script>
        </div><!--contentwrapper-->
        
		</div>
	</body>

</html>