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
<style>
<!--
input[type="text"]{width: 130px;}
-->
</style>
<script type="text/javascript">
function order(order) {
	 if (order=='desc') {
		 order = 'asc';
	 } else {
		 order = 'desc';
	 }
	 var che_number = $("#che_number").val();
	 var che_time_start = $("#che_time_start").val();
	 var che_time_end = $("#che_time_end").val();
	 var che_port = $("#che_port").val();
	 var che_state = $("#che_state").val();
	 window.location.href="<?php echo U('Dingyue/select');?>&order="+order+"&che_number="+che_number+"&che_time_start="+che_time_start+"&che_time_end="+che_time_end+"&che_port="+che_port+"&che_state="+che_state; 
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
			<form action="<?php echo U('Dingyue/select');?> " method="post">
				<input type="submit"  id="cheliang_submit_input"  class="query_btn"  value="查询"/>
					<div  id="cheliang_submit_text" class="query_div" > 
						查询时间 <input type="text" style="width: 154px" class="input length_3 J_datetime" name="che_time_start" id="che_time_start" value="<?php echo ($time_start); ?>"/> —
									  <input type="text" style="width: 154px" class="input length_3 J_datetime" id="che_time_end" name="che_time_end" value="<?php echo ($time_end); ?>"/>
					</div>
				<div  id="cheliang_submit_text" class="query_div" > 
					查询接口 <input type="text"   class="query_txt" id="che_port" name="che_port" value="<?php echo ($port); ?>"/>
				</div>
				<div  id="cheliang_submit_text" class="query_div" > 
					查询状态码 <input type="text"  class="query_txt" name="che_state" id="che_state" value="<?php echo ($state); ?>"/>
				</div>
				<div class="query_div" > 
					查询编号 <input type="text"  class="query_txt" id="query_no" name="query_no" value="<?php echo ($query_no); ?>"/> 
				</div>
				<div  id="cheliang_submit" class="query_div" > 
						车牌号 <input type="text"  id="che_number" class="query_txt" name="che_number" value="<?php echo ($number); ?>"/> 
				</div>
			</form>
		</div>
	<div class="count_div">
			<div class="count_txt">总查询数：<?php echo ($count); ?></div>
			<div class="count_txt">本周查询数： <?php echo ($count1); ?></div>      
			<div class="count_txt">本月查询数：<?php echo ($count2); ?></div> 
		    <div class="count_txt">上月查询数：<?php echo ($count3); ?></div>     
		</div>
	<table class="table table-hover table-bordered table-list" id="menus-table">
		<tr>
			<th>查询编号</th>
			<th>订阅车辆号</th>
			<th>车牌号</th>
			<th>车架号</th>
			<th>发动机号</th>
			<th>查询城市</th>
			<th onclick="order('<?php echo ($order); ?>')">查询时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
			<th>查询接口</th>
			<th>查询状态码</th>
			<th>查询状态结果</th>
			<th>查询类型</th>
			<th>扣分总计</th>
			<th>罚款总计</th>
			<th>返回违章记录条数</th>
			<th>新增违章记录条数</th>
			<th>修改违章记录条数</th>
		</tr>
		<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
				<td><?php echo ($vo["id"]); ?></td>
				<td><?php echo ($vo["car_id"]); ?></td>
				<td><?php echo ($vo["license_number"]); ?></td>
				<td><?php echo ($vo["frame_number"]); ?></td>
				<td><?php echo ($vo["engine_number"]); ?></td>
				<td><?php echo ($vo["city"]); ?></td>
				<td><?php echo (date('Y-m-d H:i:s',$vo["c_time"])); ?></td>
				<td><?php echo ($vo["port"]); ?></td>
				<td><?php echo ($vo["code"]); ?></td>
				<td><?php echo ($vo["content"]); ?></td>
				<td><?php if($vo['state'] == '0'): ?>自动<?php elseif($vo['state'] == '1'): ?>手动<?php else: ?>未知<?php endif; ?></td>
				<td><?php echo ($vo["points"]); ?></td>
				<td><?php echo ($vo["money"]); ?></td>
				<td><?php echo ($vo["all_nums"]); ?></td>
				<td><?php echo ($vo["add_nums"]); ?></td>
				<td><?php echo ($vo["edit_nums"]); ?></td>
			</tr><?php endforeach; endif; ?>
	</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>