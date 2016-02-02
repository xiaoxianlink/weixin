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
 function order(order) {
	 if (order=='desc') {
		 order = 'asc';
	 } else {
		 order = 'desc';
	 }
	 var fu_numbers = $("#fu_numbers").val();
	 var fu_number = $("#fu_number").val();
	 var fu_time_start = $("#fu_time_start").val();
	 var fu_time_end = $("#fu_time_end").val();
	 var fu_code = $("#fu_code").val();
	 window.location.href="<?php echo U('Jiaoyi/history');?>&order="+order+"&fu_numbers="+fu_numbers+"&fu_number="+fu_number+"&fu_time_start="+fu_time_start+"&fu_time_end="+fu_time_end+"&fu_code="+fu_code; 
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
<style>
<!--
.query_div {float:left;};
-->
</style>
<body class="J_scroll_fixed" style="overflow-x:scroll; width: atuo;" >
	<div class="wrap J_check_wrap" style="width: 150%;">
		<div id="cheliang_submit" class="top_div">
				<form action="<?php echo U('Jiaoyi/history');?> " method="post">
				<div  class="query_div" style="margin-left: 20px;" > 
					车牌号  <input type="text"   class="query_txt"  name="fu_code" id="fu_code"  value="<?php echo ($array_post["0"]); ?>"/> 
				</div>
				<div  class="query_div" style="margin-right: 20px;" > 
						订单时间<input type="text" name="fu_time_start" id="fu_time_start" class="input length_3 J_datetime"  value="<?php echo ($array_post["1"]); ?>"/> 
						— <input type="text"  class="input length_3 J_datetime" id="fu_time_end" name="fu_time_end" value="<?php echo ($array_post["2"]); ?>"/>
				</div>
				<div  class="query_div" > 
					服务商编号 <input type="text"   class="query_txt"  name="fu_number" id="fu_number" value="<?php echo ($array_post["3"]); ?>"/> 
				</div>
				<div  class="query_div" > 
					处理编号 <input type="text"   class="query_txt"  name="fu_numbers" id="fu_numbers" value="<?php echo ($array_post["4"]); ?>"/> 
				</div>
				<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询" style="float:left;"/>
				</form>
		</div>
		<div class="count_div" >
			<div class="count_txt">订单总数 ：<a href="<?php echo U('Jiaoyi/history');?>"><?php echo ($order_status["0"]); ?></a></div>
			<div class="count_txt">结算完成 ：<a href="<?php echo U('Jiaoyi/history?order_status=5');?>"><?php echo ($order_status["1"]); ?></a></div>      
			<div class="count_txt"> 退款成功：<a href="<?php echo U('Jiaoyi/history?order_status=7');?>"><?php echo ($order_status["2"]); ?></a></div> 
		</div>
<table class="table table-hover table-bordered table-list" id="menus-table" style="width: 150%;">
	<tr>
		<th>#</th>
		<th>处理编号</th>
		<th>指纹</th>
		<th>车牌号</th>
		<th>违章时间</th>
		<th>违章地区</th>
		<th>违章代码</th>
		<th>罚金</th>
		<th>罚分</th>
		<th>支付金额</th>
		<th onclick="order('<?php echo ($order); ?>')">订单时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
		<th>推单编号</th>
		<th>服务商编号</th>
		<th>服务商手机号</th>
		<th>结算金额</th>
		<th>结算/取消时间</th>
		<th>订单状态</th>
		<th>支付流水号</th>
		<th>支付渠道 </th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["order_sn"]); ?></td>
			<td><?php echo md5($vo['license_number'].$vo['time']); ?></td>
			<td><?php echo ($vo["license_number"]); ?></td>
			<td><?php if($vo['time'] != ''): echo (date('Y-m-d H:i:s',$vo["time"])); endif; ?></td>
			<td><?php echo ($vo["area"]); ?></td>
			<td><?php echo ($vo["code"]); ?></td>
			<td><?php echo ($vo["money"]); ?></td>
			<td><?php echo ($vo["points"]); ?></td>
			<td><?php echo ($vo["end_money"]); ?></td>
			<td><?php echo (date('Y-m-d H:i:s',$vo["o_time"])); ?></td>
			<td><?php echo ($vo["so_id"]); ?></td>
			<td><?php echo ($vo["services_sn"]); ?></td>
			<td><?php echo ($vo["phone"]); ?></td>
			<td><?php echo ($vo["pay_money"]); ?></td>
			<td><?php if($vo['order_status'] == 5 or $vo['order_status'] == 7 or $vo['order_status'] == 8): echo (date('Y-m-d H:i:s',$vo["last_time"])); endif; ?></td>
			<td><?php if($vo['order_status'] == 1): ?>未支付
									<?php elseif($vo['order_status'] == 2): ?>确认中
									<?php elseif($vo['order_status'] == 3): ?>处理中
									<?php elseif($vo['order_status'] == 5): ?>已处理
									<?php elseif($vo['order_status'] == 6): ?>退款中
									<?php elseif($vo['order_status'] == 7): ?>已退款
									<?php elseif($vo['order_status'] == 8): ?>已取消<?php endif; ?>
			 </td>
			<td><?php echo ($vo["pay_sn"]); ?></td>
			<td>微信</td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>