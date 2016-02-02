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
#menus-table{background: #fff;}
#menus-table tr{background: #f9fcfd;}
#menus-table th{font-weight:normal;}
#menus-table #top_tr th{background: #66d99f; color: #fff;}
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
	 var che_water = $("#che_water").val();
	 var che_time_start = $("#che_time_start").val();
	 var che_time_end = $("#che_time_end").val();
	 var che_type = $("#che_type").val();
	 window.location.href="<?php echo U('Jiaoyi/dingdan');?>&order="+order+"&che_number="+che_number+"&che_water="+che_water+"&che_time_start="+che_time_start+"&che_time_end="+che_time_end+"&che_type="+che_type; 
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
			 if (hour=='00' && minute=='00' && second=='00') {
				 $(this).html('--');
				 $(this).attr("lang", '--')
			 } else {
				 $(this).html(hour + ':' + minute + ':' + second);
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
<body  class="J_scroll_fixed">
<div class="wrap J_check_wrap">
<div id="cheliang_submit" class="top_div">
	<form action="<?php echo U('Jiaoyi/dingdan');?> " method="post">
	<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
	 		 <div  class="query_div" > 
					支付流水号 <input type="text" id="che_water" class="query_txt" name="che_water" value="<?php echo ($water); ?>"/> 
			</div>
			<div  class="query_div" > 
					 车牌号<input type="text" id="che_number"  class="query_txt" name="che_number" value="<?php echo ($number); ?>"/>
	 		</div>
			 <div  class="query_div" > 
				订单时间<input type="text" name="che_time_start" id="che_time_start" class="input length_3 J_datetime" value="<?php echo ($time_start); ?>"/> 
				— <input type="text"  class="input length_3 J_datetime" id="che_time_end" name="che_time_end" value="<?php echo ($time_end); ?>"/>
			</div>
			<div  class="query_div" > 
			订单状态 
			<select name="che_type" id="che_type" class="query_txt"  >
				<option value="0" ></option>
				<option value="1" <?php if($type==1){echo "selected='selected'";}?>>未支付</option>
				<option value="2" <?php if($type==2){echo "selected='selected'";}?>>确认中</option>
				<option value="3" <?php if($type==3){echo "selected='selected'";}?>>处理中</option>
				<!-- <option value="6" <?php if($type==6){echo "selected='selected'";}?>>退款中</option> -->
		   </select>
		   </div>
	</form>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
					<tr id="top_tr">
							<th>#</th>
	  						<th>处理编号</th>
                    		<th>指纹</th>
                    		<th>车牌号</th>
                    		<th>违章时间</th>
                    		<th>违章地区</th>
                    		<th>违章代码</th>
                    		<th>罚金</th>
                    		<th>罚分</th>
                    		<th onclick="order('<?php echo ($order); ?>')">订单时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
                    		<th>支付金额</th>
                    		<th>订单状态</th>
                    		<th>支付流水号</th>
                    		<th>支付渠道</th>
					</tr>
					<?php $i = 1;?>
					<?php $i = $pageIndex + $i;?>
					<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
							<th><?php echo ($i++); ?></th>
							<th><?php echo ($vo["order_sn"]); ?></th>
							<th><?php echo md5($vo['license_number'].$vo['time']); ?></th>
							<th><?php echo ($vo["license_number"]); ?></th>
							<th><?php if($vo['time'] == ''): else: echo (date('Y-m-d H:i:s',$vo["time"])); endif; ?></th>
							<th><?php echo ($vo["area"]); ?></th>
							<th><?php echo ($vo["code"]); ?></th>
							<th><?php echo ($vo["money"]); ?></th>
							<th><?php echo ($vo["points"]); ?></th>
							<th><?php if($vo['last_time'] == ''): else: echo (date('Y-m-d H:i:s',$vo["last_time"])); endif; ?></th>
							<th><?php echo ($vo["pay_money"]); ?></th>
							<th><?php if($vo['order_status'] == 1): ?>未支付
									<?php elseif($vo['order_status'] == 2): ?>确认中
									<?php elseif($vo['order_status'] == 3): ?>处理中
									<?php elseif($vo['order_status'] == 5): ?>已处理
									<?php elseif($vo['order_status'] == 6): ?>退款中
									<?php elseif($vo['order_status'] == 7): ?>已退款
									<?php elseif($vo['order_status'] == 8): ?>已取消<?php endif; ?>
							</th>
							<th><?php echo ($vo["pay_sn"]); ?></th>
							<th>wechat</th>
							</tr>
							<tr >
								<td colspan="3" style="background: #fff"></td>
								<td>推单编号</td>
		                        <td>服务商编号</td>
		                        <td>服务商手机号</td>
		                        <td>推送时间</td>
		                        <td>推单状态</td>
		                        <td>状态计时</td>
		                        <td>操作时间</td>
		                        <td colspan="4" style="background: #fff"></td>
	                        </tr>
	                        <?php if(is_array($vo["to_list"])): foreach($vo["to_list"] as $key=>$to): ?><tr >
								<td colspan="3" style="background: #fff"></td>
								<td><?php echo ($to["so_id"]); ?></td>
		                        <td><?php echo ($to["services_sn"]); ?></td>
		                        <td><?php echo ($to["phone"]); ?></td>
		                        <td><?php echo (date('Y/m/d H:i:s',$to["c_time"])); ?></td>
		                        <td><?php if($to['state'] == 0): ?>订单推送
									<?php elseif($to['state'] == 1): ?>手动转单
									<?php elseif($to['state'] == 2): ?>超时转单
									<?php elseif($to['state'] == 3): ?>正在办理
									<?php elseif($to['state'] == 4): ?>办理完成
									<?php elseif($to['state'] == 5): ?>结算完成
									<?php elseif($to['state'] == 6): ?>推单取消<?php endif; ?>
								</td>
		                        <td><span name="tmr" lang="<?php echo ($to["tmr"]); ?>"><?php echo ($to["tmr"]); ?></span></td>
		                        <td><?php echo (date('Y/m/d H:i:s',$to["l_time"])); ?></td>
		                        <td colspan="4" style="background: #fff"></td>
	                        </tr><?php endforeach; endif; endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>