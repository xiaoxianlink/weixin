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

<style>
input[type="text"] {
	width: 100px;
}
</style>
<meta charset="UTF-8">
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
			<form action="<?php echo U('Fuwu/pinggu');?> " method="post">
				<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
				<div  class="query_div" > 
							评分 <input type="text"  class="query_txt"   name="user_mark_start" value="<?php echo ($array_post["5"]); ?>"/>—
							<input type="text"  class="query_txt"  name="user_mark_end" value="<?php echo ($array_post["6"]); ?>"/>
				</div>
				<div  class="query_div" > 
							成交额 <input type="text"   class="query_txt"  name="user_make_start" value="<?php echo ($array_post["3"]); ?>"/>—
							<input type="text"  class="query_txt"  name="user_make_end" value="<?php echo ($array_post["4"]); ?>"/><br/>
				</div>
				<div  class="query_div" > 
							接单率<input type="text"   class="query_txt"   name="user_order_start"   value="<?php echo ($array_post["1"]); ?>"/>—
									 <input type="text"   class="query_txt"    name="user_order_end"   value="<?php echo ($array_post["2"]); ?>"/><br/>
				</div>
				<div  class="query_div" > 
							手机号 <input type="text"   class="query_txt"  name="user_number" value="<?php echo ($array_post["0"]); ?>"/><br/> 
				</div>
			</form>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>服务商编号</th>
		<th>手机号</th>
		<th>服务时长(天)</th>
		<th>推单数</th>
		<th>接单数</th>
		<th>接单率</th>
		<th>成交额</th>
		<th>日均推单数</th>
		<th>日均接单数</th>
		<th>日均成交额</th>
		<th>评分</th>
		<th>状态类型</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["services_sn"]); ?></td>
			<td><?php echo ($vo["phone"]); ?></td>
			<td><?php echo ($vo["create_time"]); ?></td>
			<td><?php echo ($vo["all_nums"]); ?></td>
			<td><?php echo ($vo["nums"]); ?></td>
			<td><?php echo ($vo["mod_one"]); ?></td>
			<td><?php echo ($vo["income_money"]); ?></td>
			<td><?php echo ($vo["tuidan"]); ?></td>
			<td><?php echo ($vo["jiedan"]); ?></td>
			<td><?php echo ($vo["turnover"]); ?></td>
			<td><?php echo ($vo["grade"]); ?></td>
			<td><?php if($vo['state'] == '1'): ?>封存<?php else: ?>正常<?php endif; ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
</body>
</html>