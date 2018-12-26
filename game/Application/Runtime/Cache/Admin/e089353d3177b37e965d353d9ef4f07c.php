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
		<script type="text/javascript" src="../../../Public/admin/js/custom/jquery.cookie.js">
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
					<li class="header">开心大转盘</li>

					<li>
						<a href="/kpan.php?m=Admin&amp;c=Admin&amp;a=welcome"><i class="fa fa-dashboard"></i><span>系统首页</span></a>
					</li>

					<li class="treeview" onclick="index(0)">
						<a href="#">
							<i class="fa fa-cogs"></i>
							<span>系统设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu" style="display: none;">
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=web_site"><i class="fa fa-circle-o"></i>网站设置</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=user"><i class="fa fa-circle-o"></i> 管理员设置</a>
							</li>
							<!-- <li><a href="/kpan.php?m=Admin&c=Config&a=pay_mp"><i class="fa fa-circle-o"></i>支付公众号</a></li> -->
					</li>
					</ul>
					</li>
					<li class="treeview" onclick="index(1)">
						<a href="#">
							<i class="fa fa-gamepad"></i>
							<span>游戏设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=site"><i class="fa fa-circle-o"></i> 大转盘</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=site"><i class="fa fa-circle-o"></i> 佣金设置</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=withdraw"><i class="fa fa-circle-o"></i> 提现设置</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(2)">
						<a href="#">
							<i class="fa fa-comments"></i>
							<span>公众号设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=mp"><i class="fa fa-circle-o"></i> 主公众号</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=bei_mp"><i class="fa fa-circle-o"></i> 备份公众号</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(3)">
						<a href="#">
							<i class="fa fa-credit-card-alt"></i>
							<span>支付设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=Config&a=live_pay"><i class="fa fa-circle-o"></i> 生活圈支付</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(4)">
						<a href="#">
							<i class="fa fa-cloud"></i>
							<span>域名设置</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=Domain&a=index"><i class="fa fa-circle-o"></i> 游戏域名</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Domain&a=generalize"><i class="fa fa-circle-o"></i> 推广域名</a>
							</li>
						</ul>
					</li>
					<li class="treeview" onclick="index(5)">
						<a href="#">
							<i class="fa fa-user "></i> <span>用户管理</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=User&a=index"><i class="fa fa-circle-o"></i> 会员管理</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=User&a=qiehuan"><i class="fa fa-circle-o"></i> 调整会员数据</a>
							</li>
							<!-- <li><a href="#"><i class="fa fa-circle-o"></i> Editors</a></li> -->
						</ul>
					</li>
					<li class="treeview" onclick="index(6)">
						<a href="#">
							<i class="fa fa-pie-chart"></i> <span>报表查看</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu" ;>
							<li>
								<a href="/kpan.php?m=Admin&c=Product&a=buy"><i class="fa fa-circle-o"></i> 购买列表</a>
							</li>
							<li>
								<a href="/kpan.php?m=admin&c=product&a=zhong&money=5.00"><i class="fa fa-circle-o"></i> 中奖列表</a>
							</li>
							<li>
								<a href="/kpan.php?m=admin&c=product&a=bizhong"><i class="fa fa-circle-o"></i> 必中列表</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Finance&a=withdraw"><i class="fa fa-circle-o"></i> 提现记录</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Finance&a=expense"><i class="fa fa-circle-o"></i> 佣金记录</a>
							</li>
							<li>
								<a href="/kpan.php?m=Admin&c=Finance&a=payorder"><i class="fa fa-circle-o"></i> 充值记录</a>
							</li>
						</ul>
					</li>

					<li class="header">其他功能</li>
					<li>
						<a href="/kpan.php?m=Admin&c=Admin&a=clear_cache"><i class="fa fa-recycle text-yellow"></i> <span>[清除缓存]</span></a>
					</li>
					<li>
						<a href="/kpan.php?m=Admin&c=Index&a=logout"><i class="fa fa-reply-all text-aqua"></i> <span>[退出]</span></a>
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
            <h1 class="pagetitle">游戏设置</h1>
            <span class="pagedesc">设置游戏的基本信息</span>
        </div><!--pageheader-->
        <div id="contentwrapper" class="contentwrapper lineheight21">
            <form class="stdform stdform2" method="post">          
                <p>
					<label>利润比率 (此刻利润值为：<span style="color:#f65c20"><?php echo ($lirun); ?></span>)<small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="lirun" id="lirun" value="<?php echo ($_CFG["site"]["lirun"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>新盘前3000ID第一轮必中的金额<small>比如：2;5;10;2元5元10元</small></label>
					<span class="field"><input type="text" name="bizhong" id="bizhong" value="<?php echo ($_CFG["site"]["bizhong"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>邀请每满N人以上就会必中奖一回 <small>比如：20人就必中一次</small></label>
					<span class="field"><input type="text" name="meiman" id="meiman" value="<?php echo ($_CFG["site"]["meiman"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>无规律必中一次，例如第4  第9 第14</label>
					<span class="field"><input type="text" name="wuguili" id="wuguili" value="<?php echo ($_CFG["site"]["wuguili"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>小盘1.9倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="xiao19" id="xiao19" value="<?php echo ($_CFG["site"]["xiao19"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>小盘0.1倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="xiao01" id="xiao01" value="<?php echo ($_CFG["site"]["xiao01"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>中盘0.1倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="zhong01" id="zhong01" value="<?php echo ($_CFG["site"]["zhong01"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>中盘0.5倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="zhong05" id="zhong05" value="<?php echo ($_CFG["site"]["zhong05"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>中盘2.1倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="zhong21" id="zhong21" value="<?php echo ($_CFG["site"]["zhong21"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>中盘3.6倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="zhong36" id="zhong36" value="<?php echo ($_CFG["site"]["zhong36"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘0.05倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da005" id="da005" value="<?php echo ($_CFG["site"]["da005"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘0.1倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da01" id="da01" value="<?php echo ($_CFG["site"]["da01"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘0.2倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da02" id="da02" value="<?php echo ($_CFG["site"]["da02"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘0.3倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da03" id="da03" value="<?php echo ($_CFG["site"]["da03"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘0.5倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da05" id="da05" value="<?php echo ($_CFG["site"]["da05"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘1.1倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da11" id="da11" value="<?php echo ($_CFG["site"]["da11"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘2倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da2" id="da2" value="<?php echo ($_CFG["site"]["da2"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘3倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da3" id="da3" value="<?php echo ($_CFG["site"]["da3"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘5倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da5" id="da5" value="<?php echo ($_CFG["site"]["da5"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘6倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da6" id="da6" value="<?php echo ($_CFG["site"]["da6"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘8倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da8" id="da8" value="<?php echo ($_CFG["site"]["da8"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>大盘10倍中率 <small>比如：0.2表示20%</small></label>
					<span class="field"><input type="text" name="da10" id="da10" value="<?php echo ($_CFG["site"]["da10"]); ?>" class="smallinput" /></span>
				</p>
				<!-- 
				<p>
					<label>偷菜比率<small>比如：被偷后收益减少比率(%)</small></label>
					<span class="field"><input type="text" name="tou" id="tou" value="<?php echo ($_CFG["site"]["tou"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>抽抽乐价格<small>参与抽抽乐需要支付的积分</small></label>
					<span class="field"><input type="text" name="chou" id="chou" value="<?php echo ($_CFG["site"]["chou"]); ?>" class="smallinput" /></span>
				</p> 
				<p>
					<label>抽抽乐奖金<small>百分比（%）</small></label>
					<span class="field"><input type="text" name="chou_reward" id="chou_reward" value="<?php echo ($_CFG["site"]["chou_reward"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>抽抽乐推荐奖<small>百分比（%）</small></label>
					<span class="field"><input type="text" name="chou_expense" id="chou_expense" value="<?php echo ($_CFG["site"]["chou_expense"]); ?>" class="smallinput" /></span>
				</p>
				<p>
					<label>抽抽乐规则说明<small></small></label>
					<span class="field">
						<textarea name="chou_body" id="chou_body" style=" height:300px;"><?php echo ($_CFG["site"]["chou_body"]); ?></textarea>
					</span>
				</p> -->
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