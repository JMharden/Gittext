<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
</head>
<body>
<div class="error">
	<h3><?php echo strip_tags($e['message']);?></h3>
<?php if(isset($e['file'])) {?>
	<div class="info">
		<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
	</div>
<?php }?>
<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
			<h3>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br($e['trace']);?></p>
		</div>
	</div>
<?php }?>
</div>
<div class="copyright">
	<a title="官方网站" href="javascript:;" target="_blank">弘井科技</a>
</div>
</body>
</html>
