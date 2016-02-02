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
<script type="text/javascript">
function ShowPrompt(objEvent,address,content) {
	 $("#frame_number").html('地点：'+address);
	 $("#engine_number").html('内容：'+content);
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
     var left = objEvent.clientX - 145;
     var top = objEvent.clientY;     //clientY 为鼠标在窗体中的 y 坐标值
     $("#promptDiv2").css({"top":top,"left":left});
 }
 function order(order,state) {
	 if (order=='desc') {
		 order = 'asc';
	 } else {
		 order = 'desc';
	 }
	 var che_number = $("#che_number").val();
	 var che_time_start = $("#che_time_start").val();
	 var che_time_end = $("#che_time_end").val();
	 window.location.href="<?php echo U('Dingyue/weizhang');?>&order="+order+"&state="+state+"&che_number="+che_number+"&che_time_start="+che_time_start+"&che_time_end="+che_time_end; 
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
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
		<div id="cheliang_submit" class="top_div">
			<form action="<?php echo U('Dingyue/weizhang');?> " method="post">
				<input type="submit"   class="query_btn"  value="查询"/>
				<div  id="cheliang_submit" class="query_div" > 
					推送时间 <input type="text"  class="input length_3 J_datetime" name="che_time_start" id="che_time_start" class="input length_3 J_datetime"  value="<?php echo ($time_start); ?>"/> —
								  <input type="text"  class="input length_3 J_datetime" id="che_time_end" name="che_time_end"  class="input length_3 J_datetime"  value="<?php echo ($time_end); ?>"/>
				</div>
				<div class="query_div" > 
					查询编号 <input type="text"  class="query_txt" id="query_no" name="query_no" value="<?php echo ($query_no); ?>"/> 
				</div>
				<div  id="cheliang_submit_text" class="query_div" > 
					车牌号 <input type="text"  class="query_txt" id="che_number" name="che_number" value="<?php echo ($number); ?>"/> 
				</div>
			</form>
		</div>
		<div class="count_div">
			<div class="count_txt">总推送数：<?php echo ($roles_sums); ?></div>
			<div class="count_txt">本周推送数：<?php echo ($count1); ?></div>      
			<div class="count_txt">本月推送数：<?php echo ($count2); ?></div> 
		    <div class="count_txt">上月推送数：<?php echo ($count3); ?></div>     
		</div>

		<table class="table table-hover table-bordered table-list" id="menus-table">
			<tr>
				<th>#</th>
				<th>推送渠道</th>
				<th onclick="order('<?php echo ($order); ?>',1)">推送时间<?php if ($order == 'desc' || $state == 2){?>↓<?php }else if ($order == 'asc' && $state == 1){ ?>↑<?php } ?></th>
				<th>昵称</th>
				<th>查询编号</th>
				<th>车牌号</th>
				<th onclick="order('<?php echo ($order); ?>',2)">违章时间<?php if ($order == 'desc' || $state == 1){?>↓<?php }else if ($order == 'asc' && $state == 2){ ?>↑<?php } ?></th>
				<th>违章地区</th>
				<th>违章代码</th>
				<th>罚金</th>
				<th>罚分</th>
			</tr>
			<?php $i = 1;?>
			<?php $i = $pageIndex + $i;?>
			<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
					<td><?php echo ($i++); ?></td>
					<td>wechat</td>
					<td><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?></td>
					<td><span class="sview" onmouseover="ShowPrompt2(event,'<?php echo ($vo["nickname"]); ?>','<?php echo ($vo["openid"]); ?>','<?php echo ($vo["city"]); ?>','<?php echo (date('Y-m-d H:i:s',$vo["u_time"])); ?>')" onmouseout="HiddenPrompt()"><?php echo ($vo["nickname"]); ?></span></td>
					<td><?php echo ($vo["query_no"]); ?></td>
					<td><?php echo ($vo["license_number"]); ?></td>
					<td><span class="sview" onmouseover="ShowPrompt(event,'<?php echo ($vo["address"]); ?>','<?php echo ($vo["content"]); ?>')" onmouseout="HiddenPrompt()"><?php echo (date('Y-m-d H:i:s',$vo["time"])); ?></span></td>
					<td><?php echo ($vo["area"]); ?></td>
					<td><?php echo ($vo["code"]); ?></td>
					<td><?php echo ($vo["money"]); ?></td>
					<td><?php echo ($vo["points"]); ?></td>
				</tr><?php endforeach; endif; ?>
		</table>
		<div class="pagination"><?php echo ($Page); ?></div>
</div>
<div id="promptDiv" class="promptStyle">
	<br /><br />
	<div id="frame_number" class="prom_div"></div>
	<div id="engine_number" class="prom_div"></div>
</div>
<div id="promptDiv2" class="promptStyle3">
    <br /><br />
	<div id="nickname" class="prom_div2"></div>
	<div class="prom_div2">渠道：微信</div>
	<div id="openid" class="prom_div2"></div>
	<div id="city" class="prom_div2"></div>
	<div class="prom_div2">语言：zh_CN</div>
	<div id="c_time" class="prom_div2"></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>