<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="x5-orientation" content="portrait">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>转个不停</title>
		<link rel="stylesheet" type="text/css" href="/res/zhuan/mystyle.css">
		<style type="text/css">
			.public {
				background: url("/res/zhuan/images/banner.png") no-repeat;
				background-size: 100% 100%;
				width: 90%;
				margin: auto;
			}		
			.tab2 .tab_item {
				margin-right: 0;
			}			
			@media screen and (max-width: 414px) {
				.tab_list {
					padding: 0
				}
				.public,
				.swiper-container,
				.swiper-wrapper,
				.swiper-slide {
					height: 25px;
				}
				header .chance,
				.tab_list {
					z-index: 9999;
				}
				.tab_item_disc,
				.tab1 {
					z-index: 9999;
				}
			}		
			@media screen and (min-width:321px) and (orientation:portrait) {
				html {
					font-size: 120px;
				}
			}
			@media screen and (max-width:320px) and (orientation:portrait) {
				html {
					font-size: 95px;
				}
			}
		</style>
		<script type="text/javascript" src="/Public/js/jquery-1.7.2-min.js"></script>
	<meta name="poweredby" content="besttool.cn" />
</head>
	<body>
		<header class="l-hd">
			<div class="fixed_left">
    <div class="left_div">
        <p class="fixed_left_font">
            <span class="name" style="margin-left: -2px;">ID:<?php echo ($user["id"]); ?></span>
            <span class="balance" style="font-size:0.1rem;position: relative;"><span style="display: inline-block;position: absolute;top: 0">余额:¥<b class="amount"><?php echo ($user["money"]); ?></b></span><i style="width: 0.13rem;height:0.13rem;background: url(/res/zhuan/images/icon.png) no-repeat;background-size: 100% 100%;display: inline-block;background-color: rgb(230,120,120);border-radius: 50%;margin-left:0.85rem;margin-top:0.03rem;
            " class="littleaddbutn";></i></span>
            <a onclick="duixian()"><span class="cash_postal"><button>兑换</button></span></a>
            <a  href="<?php echo U('Suggest/index');?>"><span class="cash_postal"><button>投诉</button></span></a>
           <!--  <a href="/index.php?m=&c=Index&a=rechar"><span class="cash_postal"><button>充值</button></span></a> -->
        </p>
    </div>
</div>
<div class="toast dialog  toast5 " <?php if(empty($rechar)){ echo 'style="display:none;"';}?>>
	<!--新增充值页面-->
	<div class="shade"></div>
	<div class="bigger" style="min-hight:1rem;">
			<p class="cz">充值中心</p>
		<div style="padding-top:0.4rem;box-sizing: border-box;width:80%;height:85%;margin: auto;background: url('/res/zhuan/images/notice_bg.png') center 0px / 98% 100% no-repeat;">
			<div class="btnmony btnstyle" style="margin-top: 0.1rem;" onclick="remoneyChange('3','<?php echo ($user["id"]); ?>')">充值3金币</div>						
			<div class="btnmony btnstyle" onclick="remoneyChange('5')">充值5金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('10')">充值10金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('20')">充值20金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('50')">充值50金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('100')">充值100金币</div>
			<div class="btnmony btnstyle" onclick="remoneyChange('500')">充值500金币</div>
			<div class="btnmony btnstyle btncolor" onclick="quxiao5()">取消</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	var d_flag = true;		
	//修复充值问题
	function remoneyChange(money){
		var a = 1;
		window.location = '/index.php?m=&c=Index&a=liveAccredit&money=' + money;		
	};
	function quxiao5(){
		$(".toast5").hide();	
	}
	$(".littleaddbutn").click(function(){
		$(".toast5").show();
	});
	//修复悦换图片消失问题
	function duixian() {
		if (d_flag) {
			d_flag = false;
		    $("#2_little").css('display','flex');
			$.post("/index.php?m=&c=Index&a=duixian", { money: money }, function(d) {
				d_flag = true;
				alert(d.info);
				if (d.status == 1) {
					window.location = '/';
				}
			}, 'json');
		}
	};	
</script>	
			<div class="toast dialog  toast3 " <?php if(empty($rechar)){ echo 'style="display:none;"';}?>>
				<div class="shade"></div>
				<div class="naChanceMsg_1" style="min-hight:1rem;">
					<!--<div class="publicClose"></div>-->
					<p class="pop_context pop_context3">您有<?php echo ($rechar['money']); ?>元的转盘红包没有抽奖</br>是否立即抽奖?</p>
					<input value="<?php if(empty($rechar)){ echo '0';}else{ echo $rechar ['id'];}?>" id="rechar" type="hidden" />
					<div class="btn_stype_1">
						<p class="goDraw" id="sureDraw" onclick="rechar()">立即抽奖</p>
					</div>
					<div class="btn_stype_2 cpm-hide">
						<p class="goDraw  sureDraw_left">确认
						</p>
						<p class="goDraw sureDraw_right">取消
						</p>
					</div>
				</div>
			</div>
			<div class="toast dialog toast1" style="display: none;">
				<div class="shade"></div>
				<div class="naChanceMsg_1" style="min-hight:1rem;">
					<!--<div class="publicClose"></div>-->
					<p class="pop_context pop_context1"></p>

					<div class="btn_stype_1 cpm-hide">
						<p class="goDraw chou" id="sureDraw">立即抽奖</p>
					</div>
					<div class="btn_stype_2 ">
						<p class="goDraw  sureDraw_left chou">确认

						</p>
						<p class="goDraw sureDraw_right" onclick="quxiao()">取消
						</p>
					</div>
				</div>
			</div>
			<div class="toast dialog toast2" style="display: none;">
				<div class="shade"></div>
				<div class="naChanceMsg_1" style="min-hight:1rem;">
					<!--<div class="publicClose"></div>-->
					<p class="pop_context pop_context2">你的账户余额不足,</br>需充值抽奖</p>
					<div class="btn_stype_1 cpm-hide">
						<p class="goDraw" id="sureDraw">确定</p>
					</div>
					<div class="btn_stype_2 ">
						<p class="goDraw  sureDraw_left" onclick="queding2()">确认
						</p>
						<p class="goDraw sureDraw_right" onclick="quxiao2()">取消
						</p>
					</div>
				</div>
			</div>
			<div class="toast dialog toast4" style="display: none;">
				<div class="shade"></div>
				<div class="naChanceMsg_1" style="min-hight:1rem;">
					<!--<div class="publicClose"></div>-->
					<p class="pop_context pop_context4">你的账户余额不足,</br>最低需支付<span class="zhifu"></span>元抽奖</p>

					<div class="btn_stype_1 cpm-hide">
						<p class="goDraw" id="sureDraw">确定</p>
					</div>
					<div class="btn_stype_2 ">
						<p class="goDraw  sureDraw_left" onclick="payinner()">确认

						</p>
						<p class="goDraw sureDraw_right" onclick="quxiao4()">取消
						</p>
					</div>
				</div>
			</div>
		</header>
		<div id="ui-view">
			<div id="etDiv" class="all-contents">
				<div class="wrap">

					<!--头部中奖记录与规则-->
					<header>
						<div class="chance">

							<div class="tab1">
								<div class="tab_item_disc little tab_actives" data-type="little" onclick="pan('little')">小盘</div>
								<div class="tab_item_disc middle" data-type="middle" onclick="pan('middle')">中盘</div>
								<div class="tab_item_disc big" data-type="big" onclick="pan('big')">大盘</div>
							</div>
						</div>

					</header>
					<!--大转盘抽奖区-->
					<article>
						<div class="turnPlate">
							<div class="disc" style="position: relative;">
								<div id="2_little" class="turnPlateBgc star_disc" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change:transform;" src="/res/zhuan/images/2_rmbn_little.png">
								</div>
								<div id="2_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/2_rmbn_middle.png">
								</div>
								<div id="2_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/2_rmbn_big.png">
								</div>

								<div id="5_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/5_rmbn_little.png">
								</div>
								<div id="5_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/5_rmbn_middle.png">
								</div>
								<div id="5_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/5_rmbn_big.png">
								</div>

								<div id="10_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/10_rmbn_little.png">
								</div>
								<div id="10_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/10_rmbn_middle.png">
								</div>
								<div id="10_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/10_rmbn_big.png">
								</div>

								<div id="30_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/30_rmbn_little.png">
								</div>
								<div id="30_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/30_rmbn_middle.png">
								</div>
								<div id="30_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/30_rmbn_big.png">
								</div>

								<div id="100_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/100_rmbn_little.png">
								</div>
								<div id="100_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/100_rmbn_middle.png">
								</div>
								<div id="100_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/100_rmbn_big.png">
								</div>

								<div id="300_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/300_rmbn_little.png">
								</div>
								<div id="300_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/300_rmbn_middle.png">
								</div>
								<div id="300_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/300_rmbn_big.png">
								</div>

								<div id="1000_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/1000_rmbn_little.png">
								</div>
								<div id="1000_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/1000_rmbn_middle.png">
								</div>
								<div id="1000_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/1000_rmbn_big.png">
								</div>

								<div id="2000_little" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/2000_rmbn_little.png">
								</div>
								<div id="2000_middle" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/2000_rmbn_middle.png">
								</div>
								<div id="2000_big" class="turnPlateBgc cpm-hide" style="transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);-webkit-transition-timing-function:cubic-bezier(0.27, 0.62, 0.71, 0.85);">
									<img class="turnPlateImg" style="will-change: transform" src="/res/zhuan/images/2000_rmbn_big.png">
								</div>
							</div>
							<div class="go" onclick="go()">
								<img id="start" src="/res/zhuan/images/pointer-.png">
							</div>
						</div>
					</article>

					<div class="public">
						<div class="banner">
							<div class="swiper-container swiper-container-vertical swiper-container-android">
								<div class="swiper-wrapper" style="transform: translate3d(0px, -165px, 0px); transition-duration: 0ms;">
									<?php if(is_array($z_log)): $i = 0; $__LIST__ = $z_log;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide swiper-slide-duplicate" data-swiper-slide-index="4" style="height: 25px; margin-bottom: 30px;">&nbsp;恭喜会员［<?php echo ($vo["uid"]); ?>］在
											<?php echo ceil($vo['money']);?>元区抢得红包<?php echo ($vo["ying"]); ?>元！</div><?php endforeach; endif; else: echo "" ;endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="tab_list">
						<div class="tab2" style="width: 90%;margin:auto">
							<div class="tab_item tab_item2 tab2_actives" style="width:25%" onclick="money(2)">2</div>
							<!--下注区域-->
							<div class="tab_item tab_item5" style="width:25% " onclick="money(5)">5</div>
							<div class="tab_item tab_item10" style="width:25% " onclick="money(10)">10</div>
							<div class="tab_item tab_item30" style="width:25% " onclick="money(30)">30</div>
							<div class="tab_item tab_item100" style="width:25% " onclick="money(100)">100</div>
							<div class="tab_item tab_item300" style="width:25% " onclick="money(300)">300</div>
							<div class="tab_item tab_item1000" style="width:25% " onclick="money(1000)">1000</div>
							<div class="tab_item tab_item2000 " style="width:25% " onclick="money(2000)">2000</div>
						</div>
					</div>

				</div>
				<!--中奖弹出层-->
				<div class="prize_box cpm-hide">
					<div class="shade"></div>
					<div class="prize_content">
						<div class="zj">
							<h5>恭喜您</h5>
							<p class="prize_txt">恭喜您赢得红包<span class="RMB">3.8</span>金币</p>
							<p class="goDraw">点击领取</p>
						</div>
						<div class="cz cpm-hide">
							<h5>很遗憾</h5>
							<p class="prize_txt">您的帐户余额不足，请支付！</p>
							<p class="chongzhi">前往支付</p>
							<p class="qx">取消</p>
						</div>
					</div>
				</div>
				<div class="mask_blank cpm-hide"></div>
				<div class="empty_code"></div>
				<div class="footer">
					<ul class="footer_ul">
						<a href="/">
							<li class="footer_li footer_li_1 footer_li_active" data-type="pay">
								<span></span>
								<p>抽奖</p>
							</li>
						</a>
						<a href="/index.php?m=&c=index&a=tixian">
							<li class="footer_li footer_li_2">
								<span></span>
								<p>提现</p>
							</li>
						</a>
						<a href="/index.php?m=&c=index&a=usercode">
							<li class="footer_li footer_li_3">
								<span></span>
								<p>代理赚钱</p>
							</li>
						</a>
						<a href="/index.php?m=&c=index&a=daili">
							<li class="footer_li footer_li_4">
								<span></span>
								<p>佣金</p>
							</li>
						</a>
						<a href="/index.php?m=&c=index&a=kefu">
							<li class="footer_li footer_li_5" style="cursor:pointer">
								<span></span>
								<p>客服</p>
							</li>
						</a>
					</ul>
				</div>
			</div>
			<div class="loading" style="display: none;">
				<img src="/res/zhuan/images/loading.gif">//每次跳转时读取页面
			</div>
		</div>
		<input value="little" id="pan" type="hidden" />
		<input value="0" id="status" type="hidden" />
		<input value="0" id="zhuan" type="hidden" />
		<input value="2" id="money" type="hidden" />
		<input value="1" id="type" type="hidden" />
		<input value="<?php if(empty($rechar)){ echo 0;}else{ echo $rechar['id'];}?>" id="pid" type="hidden" />
		<script type="text/javascript" src="/Public/js/jquery.rotate.min.js"></script>
		<script>

	// 唤起微信支付


	function call_pay(param,url,id){

		param = eval('('+param+')');


		if(typeof url == 'undefined' || !url)url = location.href;


		if (typeof WeixinJSBridge == "undefined"){


			if( document.addEventListener ){


				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);


			}else if (document.attachEvent){


				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 


				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);


			}





		}else{


			WeixinJSBridge.invoke(


				'getBrandWCPayRequest',


				param,


				function(res){


					WeixinJSBridge.log(res.err_msg);


					if(res.err_msg == 'get_brand_wcpay_request:cancle'){


						alert('你取消了支付');


						location.href = '';


					}else if(res.err_msg == 'get_brand_wcpay_request:ok'){

						//alert('支付成功1');
                     $.post("/index.php?m=&c=Index&a=jiance",{},function(d){
			          if(d.status==1){
			          	 var userm = $('.amount').html();
                         userm = userm * 1+d.money*1;
                         $('.amount').html(userm);
                         $('#pid').val(d.id);
			             // $(".chou").trigger("click");
			             $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			             $(".toast3").show();

			            }else{
			             jiance();
			            }

			         },'json');
                      
			        	//location.href = '/';


					}else{


						//alert(res.err_msg)
						alert('取消支付');


						location.href = url;


					}


					


					


				}


			);


		}


	}
    function jiance(){
    	$.post("/index.php?m=&c=Index&a=jiance",{},function(d){
	    if(d.status==1){
	      	 var userm = $('.amount').html();
	         userm = userm * 1+d.money*1;
	         $('.amount').html(userm);
	         $('#pid').val(d.id);
	         alert(d.money);
	         //$(".chou").trigger("click");
	         $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			 $(".toast3").show();
	        }else{
	        setTimeout(function(){
			jiance1();
				},500);	
	         
	        }

	     },'json');
    }
    function jiance1(){
    	$.post("/index.php?m=&c=Index&a=jiance",{},function(d){
	    if(d.status==1){
	    	 alert(d.money);
	      	 var userm = $('.amount').html();
	         userm = userm * 1+d.money*1;
	         $('.amount').html(userm);
	         $('#pid').val(d.id);
	        // $(".chou").trigger("click");
	        $(".pop_context3").html("您有"+d.money+"元的转盘红包没有抽奖</br>是否立即抽奖?");
			$(".toast3").show();
	        }else{
	          setTimeout(function(){
			  jiance();
				},500);
	        }

	     },'json');
    }

	/*function  jsApiCall(),

			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:cancle'){
					alert('你取消了支付');
					location.href = url;----------------------//
				}else if(res.err_msg == 'get_brand_wcpay_request:ok'){
					alert('支付成功2');
				    //	location.href = '/';
				}else{
					alert(res.err_msg)
					location.href = url;
				}
			}
		);
	}*/


	function clickPlantB(plant_id){


		<!--点击播种-->


		$.post("<?php echo U('do_plant');?>",{plant_id:plant_id,index:<?php echo ((isset($_GET['index']) && ($_GET['index'] !== ""))?($_GET['index']):0); ?>},function(d){


			layer.msg(d.info);


			// if(d.status == 1){


			// 	location.href = location.href;


			// }


		});


	}


	// 通用ajax表单提交


	function ajaxFormSubmit(seletor){


		if(!seletor || seletor == '')seletor = "form";


		data = $(seletor).serialize();


		layer.load(0, {shade: [0.1,'#fff']});


		$.post($(seletor).attr('action'),data,function(data){


			layer.closeAll();


			_index = layer.msg(data.info);


			if(data.url && data.url != ''){


				// 延迟一秒钟跳转


				setTimeout(function(){


					location.href = data.url;


				},1000)


			}


			else{


				setTimeout(function(){


					layer.close(_index);


				},3000)


			}


		})


	}


	


</script>

<!-- <script src="http://m.dqpps.com/js/wx.js"></script> -->


		<script type="text/javascript">
			if(screen<320){
			var a = $(".turnPlateImg").css("width");
			var b = $(".turnPlateImg").css("height");
			$(".turnPlateImg").css({ "position": "absolute", "left": "50%", "top": "50%", "marginLeft": -parseInt(a) / 2 + "px", "marginTop": -parseInt(b) / 2 + "px" });				
		/*	$(".btnmony").addClass("aopengfei");*/
			};
		</script>
		<script>
			function payinner(){
				window.location = '/index.php?m=&c=Index&a=liveAccredit&money=' + $("#money").val();
			}
			function rechar() {
				$(".toast3").hide();
				$(".chou").trigger("click");
			}

			function money(money) {
				var st = $("#status").val();
				if(st == '0') {
					var money2 = $("#money").val();
					var pan2 = $("#pan").val();
					$("#money").val(money);
					$("#zhuan").val(0);
					$(".tab_item").removeClass('tab2_actives');
					$(".tab_item" + money).addClass('tab2_actives');
					$("#" + money2 + "_" + pan2).removeClass('star_disc');
					$("#" + money2 + "_" + pan2).addClass('cpm-hide');
					$("#" + money + "_" + pan2).removeClass('cpm-hide');
					$("#" + money + "_" + pan2).addClass('star_disc');
				}
			}
			function pan(pan) {
				var st = $("#status").val();
				if(st == '0') {
					$(".tab_item_disc").removeClass('tab_actives');
					$("." + pan).addClass('tab_actives');
					var money2 = $("#money").val();
					var pan2 = $("#pan").val();
					$("#" + money2 + "_" + pan2).removeClass('star_disc');
					$("#" + money2 + "_" + pan2).addClass('cpm-hide');
					$("#pan").val(pan);
					var money = 2;
					$(".tab_item").removeClass('tab2_actives');
					$(".tab_item2").addClass('tab2_actives');
					$("#money").val(money);
					$("#pan").val(pan);
					$("#zhuan").val(0);
					$("#" + money + "_" + pan).removeClass('cpm-hide');
					$("#" + money + "_" + pan).addClass('star_disc');
				}
			};

			function go() {
				var st = $("#status").val();
				if(st == '0') {
					var money2 = $("#money").val();
					var userm = $('.amount').html();
					userm = userm * 1;
					var pid = $("#pid").val();
					if(userm < money2 * 1 && pid == '0') { 
						//当抽奖次数为0的时候执行
						//$(".pop_context1").html('你的账户余额不足,</br>需充值抽奖');
						//            if(money2<11){
						//                money2=11;
						//            }

						$(".zhifu").html(money2);
						$(".toast4").show();
						//  goPayment(money2);
					} else {
						$(".pop_context1").html('你即将消耗' + money2 + '金币进行抽奖');
						$(".toast1").show();
					}
				}
			}

			function quxiao() {
				$(".toast1").hide();
			}
			function quxiao2() {
				$(".toast2").hide();
			}
			function quxiao4() {
				$(".toast4").hide();
			}
			function queding() {
				$(".toast2").hide();
			}
			function queding2() {
				$(".toast2").hide();
			}

			function queding4() {
				$(".toast4").hide();
				$(".toast5").show()
				var money2 = $("#money").val();
				var pan2 = $("#pan").val();
				//location.href="/index.php?m=&c=Index&a=zhijie&money="+money2+"&pan="+pan2;
				//				$(".toast5").show();
			}
			$(function() {
				var isture = 0;
				$('.chou').click(function() {
					$(".toast1").hide();
					var money2 = $("#money").val();
					var pan2 = $("#pan").val();
					var st = $("#status").val();
					var zhuan = $("#zhuan").val();
					var pid = $("#pid").val();
					if(st == '0') {
						var userm = $('.amount').html();
						userm = userm * 1;
						if(userm < money2 * 1 && pid == '0') { 
							//当抽奖次数为0的时候执行
							$(".toast4").show();
							isture = false;
						} else { //还有次数就执行
							var newm = userm - money2 * 1;
							newm = newm.toFixed(2);
							if(pid == '0') {
								$('.amount').html(newm);
							}
							$("#status").val('1');

							$("#pid").val('0');
							$.post('/index.php?m=&c=Index&a=paybuy&tid=' + pan2 + "&money=" + money2 + "&zhuan" + zhuan + '&pid=' + pid, {}, function(data) {
								if(data.status == 1) {
									rotateFunc(data.zhuan, data.info, data.money);
									$("#zhuan").val(data.zhuan);
								}
								if(data.status == 0) {

									$(".pop_context2").html(data.info);
									$(".toast2").show();
								}
							}, "json");
						}

					}
				});
				var rotateFunc = function(zhuan, txt, money) {
					// alert(zhuan + "度" + ", txt:" + txt + "$:" + money);
					isture = true;
					var money2 = $("#money").val();
					var pan2 = $("#pan").val();
					var $btn = $("#" + money2 + "_" + pan2);
					var num = new Number(money);
					$btn.stopRotate();
					$btn.rotate({
						angle: 0,
						duration: 5000, 
						//旋转时间
						animateTo: 1800 + zhuan, 
						//让它根据得出来的结果加上1440度旋转
						callback: function() {
							isture = false; 
							// 标志为 执行完毕
							$('.amount').html(num.toFixed(2));
							$(".pop_context2").html(txt);
							$(".toast2").show();
							$("#status").val('0');
						}
					});
				};
			});
		</script>
		<input value="0" class="ispay" type="hidden" />
		<input value="0" class="isdui" type="hidden" />
		<script type="text/javascript">
			function goPayment(money) {
				var ispay = $(".ispay").val();
				if(ispay == 0) {
					$(".ispay").val(1);
					$.post("/index.php?m=&c=Index&a=payhao", { money: money }, function(d) {
						$(".ispay").val(0);
						if(d.status == 1) {
							call_pay(d.pay_param, '', '');
						// window.location = d.info;
						} else {
							alert(d.info);
						}
					});
				}
			}
			$(document).ready(function() {
				setInterval('autoScroll(".swiper-container")', 2000);
			});
			function autoScroll(obj) {
				$(obj).find(".swiper-wrapper:first").animate({
					marginTop: "-30px"
				}, 500, function() {
					$(this).css({ marginTop: "0px" }).find(".swiper-slide:first").appendTo(this);
				});
			};
			<?php if(!empty($rechar)){ echo '$(function(){pan("'.$pan.'");money("'.$rechar['money'].'");}) '; }?>
		</script>
	</body>
</html>