<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" />
    <title>设置头像</title>
    <link rel="stylesheet" type="text/css" href="/Public/css/aui.css" />
    <link rel="stylesheet" href="/Public/css/intial.css" />
    <style type="text/css">
      * {
        font-family: "黑体";
      }

      body,
      html {
        height: 100%;
        overflow: auto;
      }

      html {
        background-color: hsla(0, 0%, 96%, 1.00);
      }

      body {
        background: none;
      }

      section {
        padding-top: 3rem;
        font-size: 0.9rem;
      }

      #headimg {
        width: 12rem;
        height: 12rem;
        border: 2px solid #ffffff;
        border-radius: 100%;
        margin: auto;
        margin-bottom: 1.7rem;
        background-size: 100% 100% !important;
        background-repeat: no-repeat;
        background-position: center center;
      }

      .btn {
        position: relative;
        z-index: 10;
        padding: 0.5rem;
        height: 1.6rem;
        line-height: 1.6rem;
        margin: auto;
        font-size: 0.8rem;
        color: #fd1142 !important;
        border: 1px solid #fd1142;
        border-radius: 0.2rem;
      }

      .btn img {
        display: inline-block;
        width: 0.75rem;
        margin-left: 0.1rem;
      }


      /*截图上传页面*/
      .clipbg {
        position: fixed;
        background: black;
        top: 0;
        z-index: 999;
        width: 100%;
        height: 100%;
        left: 0;
      }

      .loading {
        position: absolute;
        top: 40%;
        width: 38%;
        left: 31%;
        height: 1.6rem;
        line-height: 1.6rem;
        z-index: 99999;
        text-align: center;
        color: #ffffff;
        border-radius: 0.2rem;
        background: #9f9f9f;
      }

      .clipbg #clipArea {
        width: 100%;
        height: 80%;
        margin: auto;

      }

      .clipbg .footer {
        width: 90%;
        position: fixed;
        left: 5%;
        bottom: 0px;
        text-align: center;
      }

      .clipbg dl {
        background: #ffffff;
        border-radius: 0.4rem;
        overflow: hidden;
        margin-bottom: 0.6rem;
      }

      .clipbg dd {
        width: 48%;
        display: inline-block;
        position: relative;
        height: 2.25rem;
        line-height: 2.25rem;
        border-bottom: 1px solid #999999;
      }

      .clipbg .back {
        height: 2.25rem;
        line-height: 2.25rem;
        border-radius: 0.4rem;
        margin-bottom: 0.4rem;
        background: #ffffff;
      }

      .clipbg dd input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 11;
        filter: alpha(opacity=0);
        -moz-opacity: 0;
        -khtml-opacity: 0;
        opacity: 0;
      }
    </style>
  <meta name="poweredby" content="besttool.cn" />

  <body>
    <!-- <section class="aui-text-center">
      <div id="headimg" style="background-image:url(img/headbig.png);"></div>
      <span class="btn">
        点击设置头像
      </span>
    </section> -->
    <!--图片裁剪-->
    <div class="clipbg displaynone">
      <div id="clipArea"></div>
      <div class="loading displaynone">正在载入图片...</div>
      <div class="footer">
        <dl>
          <dd>选择图片<input type="file" id="file" accept="image/*"></dd>
          <dd id="clipBtn">完成裁剪</dd>
        </dl>
      </div>
    </div>
  </body>
  <script type="text/javascript" src="/Public/js/jquery.min.js"></script>
  <script type="text/javascript" src="/Public/js/camera.js/hammer.min.js"></script>
  <script type="text/javascript" src="/Public/js/camera.js/lrz.all.bundle.js"></script>
  <script type="text/javascript" src="/Public/js/camera.js/iscroll-zoom-min.js"></script>
  <script type="text/javascript" src="/Public/js/camera.js/PhotoClip.js"></script>
  <script>
    $(function(){
      $(".clipbg").fadeIn();
    });
    var clipArea = new PhotoClip("#clipArea", {
      size: [300, 300], //裁剪框大小
      outputSize: [0, 0], //打开图片大小，[0,0]表示原图大小
      file: "#file",
      ok: "#clipBtn",
      loadStart: function() { //图片开始加载的回调函数。this 指向当前 PhotoClip 的实例对象，并将正在加载的 file 对象作为参数传入。（如果是使用非 file 的方式加载图片，则该参数为图片的 url）
        $(".loading").removeClass("displaynone");

      },
      loadComplete: function() { //图片加载完成的回调函数。this 指向当前 PhotoClip 的实例对象，并将图片的 <img> 对象作为参数传入。
        $(".loading").addClass("displaynone");

      },
      done: function(dataURL) { //裁剪完成的回调函数。this 指向当前 PhotoClip 的实例对象，会将裁剪出的图像数据DataURL作为参数传入。      
        // console.log(dataURL); //dataURL裁剪后图片地址base64格式提交给后台处理
        imgUpload(dataURL);
        // $(".clipbg").fadeOut();
        
      }
    });

    function imgUpload(imgBase) {
      var data = {
        imgBase: imgBase
      };

      $.post('http://tt.wapwei.com/api.php?m=Api&c=Index&a=upload', data, function(res) {
        console.log(res);
        if (res.status == 1) {
          console.log('上传成功');
          console.log(res.data);
          sessionStorage.setItem("imgBase",res.data);
        } else {
          console.log('上传失败');
        }
      }, 'json');
    }
  </script>
</html>