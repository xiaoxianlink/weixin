<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/statics/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/statics/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/statics/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/statics/simpleboot/font-awesome/4.2.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
		.tr_hover {background: #c0e5ff !important;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/statics/simpleboot/font-awesome/4.2.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "statics/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/statics/js/jquery.js"></script>
    <script src="/statics/js/wind.js"></script>
    <script src="/statics/simpleboot/bootstrap/js/bootstrap.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>

<meta charset="UTF-8">
<style type="text/css">
</style>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
<p id="fu_once">服务商帮助内容预览  &nbsp;  &nbsp;  &nbsp;  &nbsp;  上次保存时间:<?php if($info['c_time'] != null): echo (date('Y/m/d H:i:s',$info["c_time"])); endif; ?></p>
<form method="post" action="<?php echo U('Fuwu/help');?>" >
			
			<div id="fu_box1" style="width: 320px; height: 480px;">
				<iframe id="" src="<?php echo ($info["url"]); ?>" width="320x" height="480px" scrolling="yes" frameborder="0">
				</iframe>
			</div>
			<br />
			<div style="margin-left: 30px;">
				URL: <input type="text" name="url" value="<?php echo ($info["url"]); ?>">
				<button type="submit" class="fu_button_a" name="fu_submit" value="111" style="float:none; margin-top:0;">预览</button>
			</div>
</form>
</div>
</body>
</html>