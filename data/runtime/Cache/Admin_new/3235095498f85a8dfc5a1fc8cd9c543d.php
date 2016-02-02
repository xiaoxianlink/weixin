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
		<form action="<?php echo U('Dingyue/shuju');?> " method="post">
			<input type="submit"   class="query_btn"  value="查询"/>
			<div  class="query_div" > 
					车牌号 <input type="text"   name="che_number"  class="query_txt"   value="<?php echo ($number); ?>"/> 
			</div>
			<div class="query_div" > 
					发动机号 <input type="text" name="che_engine"  class="query_txt"    value="<?php echo ($engine); ?>"/> 
			</div>
			<div class="query_div" > 
					车架号 <input type="text" name="che_table"  class="query_txt"   value="<?php echo ($table); ?>"/> 
			</div>
		</form>
	</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>车辆编号</th>
		<th>车牌号</th>
		<th>VIN(车架号)</th>
		<th>发动机号</th>
		<th>车辆所属省</th>
		<th>车辆所属市</th>
		<th>添加时间</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["id"]); ?></td>
			<td><?php echo ($vo["license_number"]); ?></td>
			<td><?php echo ($vo["frame_number"]); ?></td>
			<td><?php echo ($vo["engine_number"]); ?></td>
			<td><?php echo ($vo["province"]); ?></td>
            <td><?php echo ($vo["city"]); ?></td>
			<td><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
</body>
</html>