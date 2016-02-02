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
.table td{padding:0;}
.table .th2{background:#93e3f9;}
.table .th4{background:#85f8be;}
-->
</style>
<script type="text/javascript">
<!--
function select_services(id) {
	$("#id").val(id);
	$.post('<?php echo U("Fuwu/select_services3");?>', { id: id }, function (data) {
		$("#province").html(data[0]);
		$("#city").html('<tr><th class="th4">城市编码</th><th class="th4">城市名称</th><th class="th4">简称</th></tr>');
		$("#sod").html('<tr><th class="th3">违章代码</th><th class="th3">罚款</th><th class="th3">罚分</th><th class="th3">定价（元）</th><th class="th3">操作</th></tr>');
		$('#province tr').click(function(){
			$('#province tr').siblings().removeClass("tr_hover");
			$(this).addClass("tr_hover");
		});
	});
}
function select_city(province) {
	var id = $("#id").val();
	$.post('<?php echo U("Fuwu/select_city2");?>', { province: province, id : id }, function (data) {
		$("#city").html(data[0]);
		$('#city tr').click(function(){
			$('#city tr').siblings().removeClass("tr_hover");
			$(this).addClass("tr_hover");
		});
	});
}
function select_scode(city_id){
	var id = $("#id").val();
	$.post('<?php echo U("Fuwu/select_scode");?>', { city_id: city_id, id : id }, function (data) {
		$("#sod").html(data[0]);
	});
}
function insert_sod(code, city_id){
	var id = $("#id").val();
	var money = $("#money_"+code).val();
	$.post('<?php echo U("Fuwu/insert_sod");?>', { id: id, money: money, code: code, city_id: city_id }, function (data) {
		if (data == 1) {
			alert('定价单已提交');
		}
	});
}
//-->
</script>
<script type="text/javascript">
$(document).ready(function(){
	$('#menus-table tr').click(function(){
		$('tr').siblings().removeClass("tr_hover");
		$(this).addClass("tr_hover");
	});
});
</script>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
	<input type="hidden" id="id"/>
		<table style="width: 100%">
			<tr>
				<td style="vertical-align: top; width: 15%"><table class="table table-hover table-bordered table-list"
						id="menus-table">
						<tr>
							<th class="th1">服务商编号</th>
							<th class="th1">手机号</th>
						</tr>
						<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><tr onclick="select_services(<?php echo ($vo["id"]); ?>)">
							<td><?php echo ($vo["services_sn"]); ?></td>
							<td><?php echo ($vo["phone"]); ?></td>
						</tr><?php endforeach; endif; ?>
					</table><div class="pagination"><?php echo ($Page); ?></div></td>
				<td style="vertical-align: top; width: 20%"><table class="table table-hover table-bordered table-list" id="province">
						<tr>
							<th class="th2">省份编码</th>
							<th class="th2">省份名称</th>
							<th class="th2">简称</th>
						</tr>
					</table></td>
				<td style="vertical-align: top; width: 20%"><table class="table table-hover table-bordered table-list" id="city">
						<tr>
							<th class="th4">城市编码</th>
							<th class="th4">城市名称</th>
							<th class="th4">简称</th>
						</tr>
						<tr></tr>
					</table></td>
				<td style="vertical-align: top;"><table class="table table-hover table-bordered table-list" id="sod">
						<tr>
							<th class="th3">违章代码</th>
							<th class="th3">罚款</th>
							<th class="th3">罚分</th>
							<th class="th3">定价（元）</th>
							<th class="th3">操作</th>
						</tr>
						<tr></tr>
					</table></td>
			</tr>
		</table>
	</div>
</body>
</html>