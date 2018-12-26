<?php if (!defined('THINK_PATH')) exit();?><html><head>
    <meta charset="UTF-8">
    <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>投诉</title>
    <link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.1.0/weui.min.css">
    
<link rel="stylesheet" type="text/css" href="__Public__/css/w_type33e22b.css">

<meta name="poweredby" content="besttool.cn" />
</head>
<body class="" ontouchstart="" debug="" wechat_real_lang="" scene="34">

<div id="pageType" name="type" class="j_page page_type">
    <p class="weui-cells__title" id="j_title">请选择投诉该网页的原因：</p>
    <div class="weui-cells" id="j_list">
    <a href="<?php echo U('Suggest/submit',array('typeid'=> 1));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页包含欺诈信息（如：假红包）</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 2));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页包含色情信息</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 3));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页包含暴力恐怖信息</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 4));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页包含政治敏感信息</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 5));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页在收集个人隐私信息（如：钓鱼链接）</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 6));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页包含诱导分享性质的内容</div>
        <div class="weui-cell__ft"></div>
    </a>

    <a href="<?php echo U('Suggest/submit',array('typeid'=> 7));?>" class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">网页可能包含谣言信息</div>
        <div class="weui-cell__ft"></div>
    </a>
</div>
    <div class="article_list" id="j_articles">
    
</div>
    <p class="foot_link"><a href="https://weixin110.qq.com/security/readtemplate?t=w_security_center_website/report_agreement&amp;lang=zh_CN">投诉须知</a></p>
</div>









<script type="text/javascript" src="https://res.wx.qq.com/open/libs/zepto/1.1.6/zepto.js"></script>



</body></html>