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
								<a href="/index.php?m=Admin&c=Config&a=site"><i class="fa fa-circle-o"></i> 佣金设置</a>
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
			<div class="pageheader notab"><h1 class="pagetitle">会员管理</h1></div><!--pageheader-->
<div id="contentwrapper" class="contentwrapper lineheight21">
    <div class="tableoptions">
        <a href="<?php echo U('User/excelUser');?>">导出用户信息</a> | 
        <form method="post"> 
            <!-- 手机: <input type="text" name="mobile" value="<?php echo ($_GET['mobile']); ?>" class="smallinput" style="width:100px;"/> 
            姓名: <input type="text" name="name" value="<?php echo ($_GET['name']); ?>" class="smallinput" style="width:100px;"/>  -->
            用户昵称: <input type="text" name="nickname" value="<?php echo ($_GET['nickname']); ?>" class="smallinput" style="width:100px;"/>
           
            <br />
            余额排序: 
            <select name="money" style="width: 120px; min-width: 120px;">
                <option value="">请选择</option>
                <option value="asc" <?php if($_GET['money'] == asc): ?>selected<?php endif; ?> >正序</option>
                <option value="desc" <?php if($_GET['money'] == desc): ?>selected<?php endif; ?> >倒序</option>
            </select>

            活跃值: 
            <select name="active_point" style="width: 120px; min-width: 120px;">
                <option value="">请选择</option>
                <option value="asc" <?php if($_GET['active_point'] == asc): ?>selected<?php endif; ?> >正序</option>
                <option value="desc" <?php if($_GET['active_point'] == desc): ?>selected<?php endif; ?> >倒序</option>
            </select>

            段位分: 
            <select name="rank" style="width: 120px; min-width: 120px;">
                <option value="">请选择</option>
                <option value="asc" <?php if($_GET['rank'] == asc): ?>selected<?php endif; ?> >正序</option>
                <option value="desc" <?php if($_GET['rank'] == desc): ?>selected<?php endif; ?> >倒序</option>
            </select>
            <input type="submit" value="查找"/>
        </form>
    </div><!--tableoptions-->

    <form action="" method="post">
        <table cellpadding="0" cellspacing="0" border="0" id="table2" class="stdtable stdtablecb">
            <thead>
                <tr>
                    <th class="head1"><input type="checkbox" class="check_all"></th>
                    <th class="head1">编号</th>
                    <th class="head1">昵称</th>
                    <th class="head1">性别</th>
                    <th class="head0">所在俱乐部</th>
                   
                    <th class="head0">上级ID</th>
                    <th class="head0">余额</th>
                    <th class="head0">活跃值</th>
                    <th class="head0">段位分</th>
                    <th class="head0">娱乐赛场次</th>
                    <th class="head0">竞技赛场次</th>
                    <th class="head0">浏览广告次数</th>
                    <th class="head0">分享次数</th>
                    <th class="head0">注册日期</th>
                    <!-- <th class="head0">操作</th> -->
                </tr>
            </thead>

            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td><input type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>                        
                    <td><?php echo ($vo["nickname"]); ?></td>
                    <td>
                        <?php if($vo['sex'] == 0): ?>未知<?php endif; ?>
                        <?php if($vo['sex'] == 1): ?>男<?php endif; ?>
                        <?php if($vo['sex'] == 2): ?>女<?php endif; ?>
                    </td>
                    <td><?php echo ($vo["club_id"]); ?></td>
                    <td><?php echo ($vo["parent1"]); ?> -> <?php echo ($vo["parent2"]); ?> -> <?php echo ($vo["parent3"]); ?></td>
                    <td><?php echo ($vo["money"]); ?></td>
                    <td><?php echo ($vo["active_point"]); ?></td>
                    <td><?php echo ($vo["rank"]); ?></td>
                    <td><?php echo ($vo["fun_game"]); ?></td>
                    <td><?php echo ($vo["play_game"]); ?></td>
                    <td><?php echo ($vo["advert"]); ?></td>
                    <td><?php echo ($vo["share"]); ?></td>
                    <td><?php echo ($vo["join_time"]); ?></td>
                    <!-- <td><a href="<?php echo U('Finance/expense?user_id='.$vo['id']);?>"> <?php echo ($vo["expense"]); ?> </a></td> -->
                    <!-- <td><a href="<?php echo U('Finance/withdraw?user_id='.$vo['id']);?>"> <?php echo ($vo["withdraw"]); ?> </a></td> -->
                    <!-- <td class="center">
                        <a href="<?php echo U('product/buy', 'uid='.$vo['id']);?>">历史战绩</a> | 
                        <a href="<?php echo U('finance/payorder', 'userid='.$vo['id']);?>">充值记录</a> | 
                        <a href="<?php echo U('add_agent', 'id='.$vo['id']);?>">设为代理商</a> | 
                        <a href="<?php echo U('edit', 'id='.$vo['id']);?>">修改</a> | 
                        <a href="<?php echo U('del', 'id='.$vo['id']);?>" onclick="return confirm('你确实要删除这个会员吗？')">删除</a>
                    </td> -->
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <!-- <div style="margin:30px;">
            <input value="拉黑会员" type="submit" name="tongdao">&nbsp; &nbsp;&nbsp; 
            <input value="取消黑名单" type="submit" name="tongdao">&nbsp; &nbsp;&nbsp;            
        </div> -->
    </form>
    <div class="dataTables_paginate paging_full_numbers" id="dyntable2_paginate"> <?php echo ((isset($page) && ($page !== ""))?($page):"<p style='text-align:center'>暂时没有数据</p>"); ?>
    </div>
</div><!--contentwrapper-->

<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">        
$(function () {
    $(".check_all").click(function () {
        var checked = $(this).get(0).checked;
        $("input[type=checkbox]").attr("checked", checked);
    });
});       
</script>
		</div>
	</body>

</html>