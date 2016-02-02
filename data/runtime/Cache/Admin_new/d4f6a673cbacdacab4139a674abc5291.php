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
	 var time_start = $("#time_start").val();
	 var time_end = $("#time_end").val();
	 var log_state = $("#log_state").val();
	 var log_number = $("#log_number").val();
	 window.location.href="<?php echo U('Xitong/log');?>&order="+order+"&time_start="+time_start+"&time_end="+time_end+"&log_state="+log_state+"&log_number="+log_number; 
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
			<form action="<?php echo U('Xitong/log');?> " method="post">
			<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
			<div  class="query_div" > 
					处理时间 <input type="text" name="time_start" id="time_start" class="input length_3 J_datetime"  value="<?php echo ($array["2"]); ?>"/>
					 — <input type="text"  class="input length_3 J_datetime" id="time_end" name="time_end" value="<?php echo ($array["3"]); ?>"/>
			</div>	
			<div  class="query_div" > 
				处理动作	<select name="log_state" id="log_state" class="query_txt"  >
										<option value="">全部</option>
										<option value="0" <?php if($array[1]=='0'){echo "selected='selected'";}?>>添加新记录</option>
										<option value="1" <?php if($array[1]==1){echo "selected='selected'";}?>>更改记录状态</option>
								</select>
			</div>
			<div  class="query_div" > 
					车牌号<input type="text"  class="query_txt" id="log_number" name="log_number" value="<?php echo ($array["0"]); ?>"/>
			</div>	
			</form>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>违章指纹</th>
		<th>车牌号</th>
		<th>违章时间</th>
		<th>违章城市</th>
		<th>违章代码</th>
		<th>罚金</th>
		<th>罚分</th>
		<th>原始状态</th>
		<th>处理后状态</th>
		<th>查询编号</th>
		<th >处理动作</th>
		<th onclick="order('<?php echo ($order); ?>')">处理时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo md5($vo['license_number'].$vo['time']); ?></td>
			<td><?php echo ($vo["license_number"]); ?></td>
			<td><?php if($vo['time'] != ''): echo (date('Y-m-d H:i:s',$vo["time"])); endif; ?></td>
			<td><?php echo ($vo["area"]); ?></td>
			<td><?php echo ($vo["code"]); ?></td>
			<td><?php echo ($vo["money"]); ?></td>
			<td><?php echo ($vo["points"]); ?></td>
			<td>未处理</td>
			<td><?php if($vo['l_type'] == 0): ?>未处理<?php elseif($vo['l_type'] == 1): ?>处理中<?php elseif($vo['l_type'] == 2): ?>已处理<?php endif; ?></td>
			<td><?php if($vo['l_type'] == 1): else: echo ($vo["query_no"]); endif; ?></td>
			<td><?php if($vo['l_state'] == '1'): ?>添加新记录<?php else: ?>更改记录状态<?php endif; ?></td>
			<td><?php if($vo['l_c_time'] != ''): echo (date('Y-m-d H:i:s',$vo["l_c_time"])); endif; ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>