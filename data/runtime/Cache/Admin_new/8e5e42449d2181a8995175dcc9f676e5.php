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
a:hover {color: #fff}
-->
</style>
<script type="text/javascript">
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
		<form action="<?php echo U('Xitong/window');?> " method="post">
		<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
		<div  class="query_div" > 
				状态 <select name="window_type" class="query_txt" >
							<option value="0"></option>
							<option value="1" <?php if($type==1){echo "selected='selected'";}?>>订单推送</option>
							<option value="3" <?php if($type==3){echo "selected='selected'";}?>>正在办理</option>
							<option value="4" <?php if($type==4){echo "selected='selected'";}?>>办理完成</option>
					   </select>
		</div>
		<div  class="query_div" > 
			  供应商编号 <input type="text"  class="query_txt"  name="window_number"value="<?php echo ($window_number); ?>"/>  
		</div>
		
		</form>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
	         		<th>处理编号</th>
	         		<th>推单编号</th>
	         		<th>供应商编号</th>
	         		<th>供应商手机号</th>
	         		<th>推送时间</th>
	         		<th>订单时间</th>
	         		<th>状态</th>
	         		<th>处理金额</th>
	         		<th>支付金额</th>
	         		<th>支付流水号</th>
	         		<th>状态计时</th>
	        </tr>
			<tr>
				<td><?php echo ($vo["order_sn"]); ?></td>
				<td><?php echo ($vo["so_id"]); ?></td>
				<td><?php echo ($vo["services_sn"]); ?></td>
				<td><?php echo ($vo["phone"]); ?></td>
				<td><?php if($vo['c_time'] != ''): echo (date('Y-m-d H:i:s',$vo["c_time"])); endif; ?></td>
				<td><?php if($vo['last_time'] != ''): echo (date('Y-m-d H:i:s',$vo["last_time"])); endif; ?></td>
				<td>
					<?php if($vo['state'] == 0): ?>订单推送
					<?php elseif($vo['state'] == 1): ?>手动转单
					<?php elseif($vo['state'] == 2): ?>超时转单
					<?php elseif($vo['state'] == 3): ?>正在办理
					<?php elseif($vo['state'] == 4): ?>办理完成
					<?php elseif($vo['state'] == 5): ?>结算完成
					<?php elseif($vo['state'] == 6): ?>推单取消<?php endif; ?>
				</td>
				<td><?php echo ($vo["money"]); ?></td>
				<td><?php echo ($vo["pay_money"]); ?></td>
				<td><?php echo ($vo["pay_sn"]); ?></td>
				<td><span name="tmr" lang="<?php echo ($vo["tmr"]); ?>"><?php echo ($vo["tmr"]); ?></span></td>
			</tr>
			<tr>
	                <td rowspan="2"></td>
	                <td>车牌号</td>
	                <td>发动机号</td>
	        		<td>车架号</td>
	        		<td>违章代码</td>
	        		<td>违章时间</td>
	        		<td>违章地区</td>
	        		<td>罚金</td>
	        		<td>罚分</td>
	        		<td rowspan="2" colspan="2"></td>
	     	</tr>
	     	<tr>
				<td><?php echo ($vo["license_number"]); ?></td>
				<td><?php echo ($vo["engine_number"]); ?></td>
				<td><?php echo ($vo["frame_number"]); ?></td>
				<td><?php echo ($vo["code"]); ?></td>
				<td><?php if($vo['time'] != ''): echo (date('Y-m-d H:i:s',$vo["time"])); endif; ?></td>
				<td><?php echo ($vo["area"]); ?></td>
				<td><?php echo ($vo["money"]); ?></td>
				<td><?php echo ($vo["points"]); ?></td>
			</tr>
			<tr>
				<?php if($vo['state'] == '0'): ?><td colspan="12"> 
				<a style="background: #f77462; line-height:30px;" class="query_btn" href="<?php echo U('Xitong/manage?state=3&id='); echo ($vo['id']); ?>">我来办理</a> 
				<a style="background: #f77462; line-height:30px;" class="query_btn" href="<?php echo U('Xitong/manage?state=1&id='); echo ($vo['id']); ?>">办不了</a></td>
				<?php elseif($vo['state'] == '3'): ?>
				<td colspan="12"> <a style="background: #6ad1df; line-height:30px;" class="query_btn" href="<?php echo U('Xitong/manage?state=4&id='); echo ($vo['id']); ?>"> 办理完成</a> <a style="background: #f77462; line-height:30px;" class="query_btn" href="<?php echo U('Xitong/manage?state=1&id='); echo ($vo['id']); ?>">办不了</a></td>
				<?php elseif($vo['state'] == '4'): endif; ?>
			</tr>
	<br /><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
</body>
</html>