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
	function refund(order_sn) {
		if (confirm('是否确认退单?')) {
			$.post('<?php echo U("Jiaoyi/refund");?>', {
				order_sn : order_sn
			}, function(data) {
				alert('退款成功！');
				location.reload();
			});
		} else {
			return false;
		}
	}
	function order(order) {
		 if (order=='desc') {
			 order = 'asc';
		 } else {
			 order = 'desc';
		 }
		 var td_number = $("#td_number").val();
		 var fu_number = $("#fu_number").val();
		 var order_sn = $("#order_sn").val();
		 window.location.href="<?php echo U('Jiaoyi/zhuandan');?>&order="+order+"&td_number="+td_number+"&fu_number="+fu_number+"&order_sn="+order_sn; 
	 }
	 function HiddenPrompt() {
	     divObj2 = document.getElementById("promptDiv2");
	     divObj2.style.visibility = "hidden";
	 }
	 function ShowPrompt2(objEvent,order_sn,license_number,e_time,e_area,e_code,e_money,e_points,pay_money) {
		 $("#d_order_sn").html('订单编号：'+order_sn);
		 $("#d_license_number").html('车牌号：'+license_number);
		 $("#d_time").html('违章时间：'+e_time);
		 $("#d_area").html('违章地区：'+e_area);
		 $("#d_code").html('违章代码：'+e_code);
		 $("#d_punish").html('罚金/罚分：'+e_money+'元/'+e_points+'分');
		 $("#d_money").html('支付金额：'+pay_money+'元');
	     var divObj = document.getElementById("promptDiv2");
	     divObj.style.visibility = "visible";
	     var left = objEvent.clientX - 115;
	     var top = objEvent.clientY;     //clientY 为鼠标在窗体中的 y 坐标值
	     $("#promptDiv2").css({"top":top,"left":left});
	 }
	 function countdown() {
		 var nowtime = Date.parse(new Date()) / 1000;
		 var obj = $("span[name='tmr']");
		 $.each(obj, function(){
			 if ($(this).attr("lang") != '--') {
				 var time = $(this).attr("lang") - nowtime;//$(this).html();
				 var hour = parseInt(time/3600);
				 if (hour < 10 ) {
					 hour = '0' + hour;
				 }
				 var minute = parseInt(time/60%60);
				 if (minute < 10 ) {
					 minute = '0' + minute;
				 }
				 var second = parseInt(time%60);
				 if (second < 10 ) {
					 second = '0' + second;
				 }
				 if (hour > 0 || minute > 0 || second > 0) {
					 $(this).html(hour + ':' + minute + ':' + second);
				 } else {
					 $(this).html('--');
					 $(this).attr("lang", '--')
				 }
			 }
		 });
	 }
	 setInterval("countdown()",1000);
	 window.onload = countdown;
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
			<form action="<?php echo U('Jiaoyi/zhuandan');?> " method="post">
			<input type="submit" id="cheliang_submit_input" class="query_btn"
					value="查询" />
				<div  class="query_div" > 
					转推单编号 <input type="text"   class="query_txt"  name="td_number" id="td_number" value="<?php echo ($td_number); ?>"/> 
				</div>
				<div  class="query_div" > 
					服务商编号 <input type="text"   class="query_txt"  name="fu_number" id="fu_number" value="<?php echo ($fu_number); ?>"/> 
				</div>
				<div  class="query_div" > 
					订单编号 <input type="text"   class="query_txt"  name="order_sn" id="order_sn" value="<?php echo ($order_sn); ?>"/> 
				</div>
			</form>
		</div>
		<table class="table table-hover table-bordered table-list"
			id="menus-table">
			<tr>
				<th>转推单编号</th>
				<th>服务商编号</th>
				<th>服务商手机号</th>
				<th onclick="order('<?php echo ($order); ?>')">推送时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
				<th>转单原因</th>
				<th>状态计时</th>
				<th>订单编号</th>
				<th>订单状态</th>
				<th>转单次数</th>
				<th>原推单号</th>
				<th>原服务商编号</th>
				<th>原服务手机号</th>
				<th>操作</th>
			</tr>
			<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
				<td><?php echo ($vo["sod_id"]); ?></td>
				<td><?php echo ($vo["services_sn"]); ?></td>
				<td><?php echo ($vo["phone"]); ?></td>
				<td><?php echo (date('Y/m/d H:i:s',$vo["c_time"])); ?></td>
				<td><?php echo ($vo["older_state"]); ?></td>
				<td><span name="tmr" lang="<?php echo ($vo["tmr"]); ?>"><?php echo ($vo["tmr"]); ?></span></td>
				<td><span class="sview" onmouseover="ShowPrompt2(event,'<?php echo ($vo["order_sn"]); ?>','<?php echo ($vo["license_number"]); ?>','<?php echo ($vo["e_time"]); ?>','<?php echo ($vo["e_area"]); ?>','<?php echo ($vo["e_code"]); ?>','<?php echo ($vo["e_money"]); ?>','<?php echo ($vo["e_points"]); ?>','<?php echo ($vo["pay_money"]); ?>')" onmouseout="HiddenPrompt()"><?php echo ($vo["order_sn"]); ?></span></td>
				<td><?php if($vo['order_status'] == 1): ?>未支付
									<?php elseif($vo['order_status'] == 2): ?>确认中
									<?php elseif($vo['order_status'] == 3): ?>处理中
									<?php elseif($vo['order_status'] == 5): ?>已处理
									<?php elseif($vo['order_status'] == 6): ?>退款中
									<?php elseif($vo['order_status'] == 7): ?>已退款
									<?php elseif($vo['order_status'] == 8): ?>已取消<?php endif; ?></td>
				<td><?php echo ($vo["tod_count"]); ?></td>
				<td><?php echo ($vo["older_sod_id"]); ?></td>
				<td><?php echo ($vo["older_s_sn"]); ?></td>
				<td><?php echo ($vo["older_phone"]); ?></td>
				<td><input type="button" onclick="refund('<?php echo ($vo["order_sn"]); ?>')"
					style="background: #ffa600; float: initial; margin: 0;"
					class="query_btn edit" value="退款" /></td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="pagination"><?php echo ($Page); ?></div>
	</div>
<div id="promptDiv2" class="promptStyle4">
    <br />
	<div id="d_order_sn" class="prom_div2"></div>
	<div id="d_license_number" class="prom_div2"></div>
	<div id="d_time" class="prom_div2"></div>
	<div id="d_area" class="prom_div2"></div>
	<div id="d_code" class="prom_div2"></div>
	<div id="d_punish" class="prom_div2"></div>
	<div id="d_money" class="prom_div2"></div>
	<div class="prom_div2">支付渠道：微信支付</div>
</div>
</body>
</html>