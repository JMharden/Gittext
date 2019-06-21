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
							<!-- <li><a href="/index.php?m=Admin&c=Config&a=pay_mp"><i class="fa fa-circle-o"></i>支付公众号</a></li> -->
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
							<i class="fa fa-gamepad"></i>
							<span>活动管理</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							<li>
								<a href="/kpan.php?m=Admin&c=Activity&a=index"><i class="fa fa-circle-o"></i>活动列表</a>
							</li>
							
							
						</ul>
					</li>
					<li class="treeview" onclick="index(7)">
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
        <!-- <th class="head1">内容</th> -->
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>今日新增用户统计</td>
        <td><?php echo ($user_count); ?>个</td>
      </tr>
      <tr>
        <td>用户总数</td>
        <td><?php echo ($user_count_all); ?>个</td>
      </tr>
      <tr>
        <td>新增三日留存</td>
        <td><?php echo ($usre_three); ?>个</td>
      </tr>
       <tr>
        <td>活跃三日留存</td>
        <td><?php echo ($user_count); ?>个</td>
      </tr>
      <tr>
        <td>用户游玩时长统计</td>
        <td><?php echo ($user_count); ?>个</td>
      </tr>
     <!--  <tr>
        <td> 当天用户在线时间段的分布</td>
        <td></td>
      </tr> -->
    
    </tbody>
  </table>
  <div class="contentwrapper lineheight21"></div>

  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">* 对局相关</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>竞技赛总场次</td>
        <td><?php echo ($game_count_all); ?></td>
      </tr>
      <tr>
        <td>今日竞技赛场次</td>
        <td><?php echo ($game_count); ?></td>
      </tr>
      <tr>
        <td>比赛平均时长</td>
        <td><?php echo ($game_aver_time); ?></td>
      </tr>
      <tr>
        <td>总竞技赛场次</td>
        <td>初级场总场次:<?php echo ($game_count_first); ?> ;中级场总场次:<?php echo ($game_count_middle); ?> ;高级场总场次:<?php echo ($game_count_high); ?> ;</td>
      </tr>
    </tbody>
    <tbody>
      <tr>
        <td>娱乐赛总场次</td>
        <td><?php echo ($fun_game_count_all); ?></td>
      </tr>
      <tr>
        <td>今日娱乐赛场次</td>
        <td><?php echo ($fun_game_count); ?></td>
      </tr>
      <tr>
        <td>比赛平均时长</td>
        <td><?php echo ($game_fun_time); ?></td>
      </tr>
     
    </tbody>
  </table>
  <div class="contentwrapper lineheight21"></div>

  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">* 提成相关  </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>总门票收入</td>
        <td><?php echo ($zuo_in); ?></td>
      </tr>
      <tr>
        <td>提成收入</td>
        <td><?php echo ($zuo_xiadan); ?></td>
      </tr>
      <tr>
        <td>俱乐部提成收入</td>
        <td>中奖:<?php echo ($zuo_ying); ?> 佣金:<?php echo ($zuo_win_ext); ?> 合计:<?php echo ($zuo_ying + $zuo_win_ext); ?></td>
      </tr>
      <tr>
        <td>上级提成收入</td>
        <td><?php echo ($zuo_out); ?></td>
      </tr>
<!--          <tr>
          <td>昨日净利润</td>
          <td><?php echo ($zuo_xiadan - $zuo_ying - $zuo_win_ext); ?></td>
        </tr>
        <tr>
          <td>利润率</td>
          <td>
            <?php echo sprintf("%.2f",(($zuo_xiadan - $zuo_ying - $zuo_win_ext)/$zuo_xiadan)) * 100 ?>%</td>
        </tr> -->
    </tbody>
  </table>

  <div class="contentwrapper lineheight21"></div>

  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">* 金币相关  </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>总金币</td>
        <td><?php echo ($qian_in); ?></td>
      </tr>
      <tr>
        <td>玩家留存金币</td>
        <td><?php echo ($qian_xiadan); ?></td>
      </tr>
      <tr>
        <td>代理商留存金币</td>
        <td>中奖:<?php echo ($qian_ying); ?> 佣金:<?php echo ($qian_win_ext); ?> 合计:<?php echo ($qian_ying + $qian_win_ext); ?></td>
      </tr>
      <tr>
        <td>总门票收入</td>
        <td><?php echo ($qian_out); ?></td>
        <tr>
          <td>玩家总充值金额</td>
          <td><?php echo ($qian_xiadan - $qian_ying - $qian_win_ext); ?></td>
        </tr>
       </tr>
        <!-- <tr>
          <td>利润率</td>
          <td>
            <?php echo sprintf("%.2f",(($qian_xiadan - $qian_ying - $qian_win_ext)/$qian_xiadan)) * 100 ?>%</td>
        </tr> -->
    </tbody>
  </table>

  <!-- <div class="contentwrapper lineheight21"></div>
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">本月盈亏</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>本月收入</td>
        <td><?php echo ($month_in); ?></td>
      </tr>
      <tr>
        <td>用户下单金额</td>
  
        <td><?php echo ($yue_xiadan); ?></td>
      </tr>
  
      <tr>
        <td>用户总收益</td>
        <td>中奖:<?php echo ($yue_ying); ?> 佣金:<?php echo ($month_win_ext); ?> 合计:<?php echo ($yue_ying + $month_win_ext); ?></td>
      </tr>
      <tr>
        <td>本月支出</td>
        <td><?php echo ($month_out); ?></td>
      </tr>
      <tr>
        <td>本月净利润</td>
        <td><?php echo ($yue_xiadan- $yue_ying - $month_win_ext); ?> </td>
      </tr>
      <tr>
        <td>利润率</td>
        <td>
          <?php echo sprintf("%.2f",($yue_xiadan- $yue_ying - $month_win_ext)/$yue_xiadan) * 100 ?>%</td>
      </tr>
    </tbody>
  </table> -->

  <div class="contentwrapper lineheight21"></div>
<!--   <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
    <thead>
      <tr>
        <th class="head1" colspan="2">总盈亏</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>总收入</td>
        <td><?php echo ($all_in); ?></td>
      </tr>
      <td>用户下单金额</td>

      <td><?php echo ($all_xiadan); ?></td>
      </tr>
      <tr>
        <td>用户总收益</td>
        <td>中奖:<?php echo ($all_ying); ?> 佣金:<?php echo ($all_win_ext); ?> 合计:<?php echo ($all_ying + $all_win_ext); ?></td>
      </tr>
      <tr>
        <td>总支出</td>
        <td><?php echo ($all_out); ?></td>
      </tr>
      <tr>
        <td>净利润</td>
        <td><?php echo ($all_in - $all_ying - $all_win_ext); ?> </td>
      </tr>
      <tr>
        <td>利润率</td>
        <td>
          <?php echo sprintf("%.2f",($all_in - $all_ying - $all_win_ext)/$all_in) * 100 ?>%</td>
      </tr>
    </tbody>
  </table> -->
</div>
<!--contentwrapper
		</div>
	</body>

</html>