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
<script type="text/javascript">
 function ShowPrompt(objEvent,frame_number,engine_number) {
	 $("#frame_number").html('车架号：'+frame_number);
	 $("#engine_number").html('发动机号：'+engine_number);
     var divObj = document.getElementById("promptDiv");
     divObj.style.visibility = "visible";
     var left = objEvent.clientX - 100;
     var top = objEvent.clientY;     //clientY 为鼠标在窗体中的 y 坐标值
     $("#promptDiv").css({"top":top,"left":left});
 }
 function HiddenPrompt() {
     divObj = document.getElementById("promptDiv");
     divObj.style.visibility = "hidden";
     divObj2 = document.getElementById("promptDiv2");
     divObj2.style.visibility = "hidden";
 }
 function ShowPrompt2(objEvent,nickname,openid,city,c_time) {
	 $("#nickname").html('昵称：'+nickname);
	 $("#openid").html('OpenID：'+openid);
	 $("#city").html('城市：'+city);
	 $("#c_time").html('关注时间：'+c_time);
     var divObj = document.getElementById("promptDiv2");
     divObj.style.visibility = "visible";
     var left = objEvent.clientX - 280;
     var top = objEvent.clientY;     //clientY 为鼠标在窗体中的 y 坐标值
     $("#promptDiv2").css({"top":top,"left":left});
 }
 function order(order) {
	 if (order=='desc') {
		 order = 'asc';
	 } else {
		 order = 'desc';
	 }
	 var che_number = $("#che_number").val();
	 window.location.href="<?php echo U('Dingyue/cheliang');?>&order="+order+"&che_number="+che_number; 
 }
</script>
<script type="text/javascript">
$(document).ready(function(){
	$('tr').click(function(){
		$('tr').siblings().removeClass("tr_hover");
		$(this).addClass("tr_hover");
	});
});
</script>
<body  class="J_scroll_fixed">
<div class="wrap J_check_wrap">
<div id="cheliang_submit" class="top_div">
	<form action="<?php echo U('Dingyue/cheliang');?> " method="post">
				<input type="submit"  id="cheliang_submit_input"  class="query_btn"  value="查询"/>
				<div  id="cheliang_submit_text" class="query_div" > 
						车牌号   <input type="text"   class="query_txt" name="che_number" id="che_number" value="<?php echo ($number); ?>"/>
				</div>
	</form>
</div>
<div class="count_div">
		<div class="count_txt">总订阅数：<a href="<?php echo U('Dingyue/cheliang?user_is_sub=1');?>"><?php echo ($d_number["0"]["count(*)"]); ?></a></div>
		<div class="count_txt">全部：<a href="<?php echo U('Dingyue/cheliang');?>"><?php echo ($z_number["0"]["count(*)"]); ?></a></div>
		<div class="count_txt" style="width: 500px;">订阅渠道：&nbsp;&nbsp;微信&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Dingyue/cheliang?channel=1');?>"><?php if($sum['0']['nums'] == ''): ?>0<?php else: echo ($sum["0"]["nums"]); endif; ?></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;alipay&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Dingyue/cheliang?channel=2');?>"><?php if($sum['1']['nums'] == ''): ?>0<?php else: echo ($sum["1"]["nums"]); endif; ?></a></div>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>车辆编号</th>
		<th>车牌号</th>
		<th onclick="order('<?php echo ($order); ?>')">订阅时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
		<th>是否订阅</th>
		<th>关注用户</th>
		<th>退订时间</th>
		<th>订阅渠道</th>
		<th>昵称</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["id"]); ?></td>
			<td><span class="sview" onmouseover="ShowPrompt(event,'<?php echo ($vo["frame_number"]); ?>','<?php echo ($vo["engine_number"]); ?>')" onmouseout="HiddenPrompt()"><?php echo ($vo["license_number"]); ?></span></td>
			<td><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?></td>
			<td><?php if($vo['is_sub'] == 0): ?>是<?php else: ?>否<?php endif; ?></td>
			<td><?php echo ($vo["username"]); ?></td>
			<td><?php if($vo['c_time'] == 0): else: echo (date('Y-m-d H:i:s',$vo["c_time"])); endif; ?></td>
			<td><?php if($vo['channel'] == 0): ?>wechat<?php else: ?>alipay<?php endif; ?></td>
			<td><span class="sview" onmouseover="ShowPrompt2(event,'<?php echo ($vo["nickname"]); ?>','<?php echo ($vo["openid"]); ?>','<?php echo ($vo["city"]); ?>','<?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?>')" onmouseout="HiddenPrompt()"><?php echo ($vo["nickname"]); ?></span></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<div id="promptDiv" class="promptStyle">
	<br /><br />
	<div id="frame_number" class="prom_div"></div>
	<div id="engine_number" class="prom_div"></div>
</div>
<div id="promptDiv2" class="promptStyle2">
    <br /><br />
	<div id="nickname" class="prom_div2"></div>
	<div class="prom_div2">渠道：微信</div>
	<div id="openid" class="prom_div2"></div>
	<div id="city" class="prom_div2"></div>
	<div class="prom_div2">语言：zh_CN</div>
	<div id="c_time" class="prom_div2"></div>
</div>
</body>
</html>