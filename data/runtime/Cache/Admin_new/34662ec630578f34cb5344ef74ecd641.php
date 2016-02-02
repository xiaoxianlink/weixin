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
<style type="text/css">
#mask{
	position:fixed;
}
.mask{
	left:0;
	top:;
	right:0;
	bottom:0;
	width:100%;
	height:100%;
	background:#000;
	filter:alpha(opacity=30);
	opacity:.3;
	z-index:999;
}
#city_add{
	position:absolute;	
	width:60%;;
	height:70%;
	margin-top:110px;
	background:#EDF2F4;
	border-radius:5px;
	z-index:1100;
}
</style>
<script type="text/javascript">
	$(function() {
		$('#city_add').hide();
		$('#city_link').click(function() {
			$('body').append('<div class="mask" id="mask"></div>');
			$('#city_add').css('left', Math.ceil(($('body').width()) / 5) + 'px');
			$('#city_add').css('top', Math.round($(this).position().top) + 'px');
			$('#city_add').show();
		});
		$('.closebtn').click(function() {
			$('#city_add').hide();
			$('#mask').remove();
		});
	});
	function order(order) {
		 if (order=='desc') {
			 order = 'asc';
		 } else {
			 order = 'desc';
		 }
		 var province_name = $("#province_name").val();
		 window.location.href="<?php echo U('Xitong/city');?>&order="+order+"&province_name="+province_name; 
	 }
	function city_update(id, is_dredge) {
		$.post('<?php echo U("Xitong/city_update");?>', {
			id : id,
			is_dredge : is_dredge
		}, function(data) {
			location.reload();
		});
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
			<form action="<?php echo U('Xitong/city');?> " method="post">
				<input type="button" id="city_link" class="query_btn"
					style="background: #f77462; width: 150px;" value="添加城市" /> <input
					type="submit" id="cheliang_submit_input" class="query_btn"
					value="查询" />
				<div class="query_div" id="elect" style="margin-right: 50px;">
					<a href="<?php echo U('Xitong/city?vcode=1');?>"<?php if($vcode == 1){echo "style='background: #6ad1df;border: 1px solid #6ad1df;
						color: #fff;'";}else{echo "style='border-left:1px solid
						#e2e7e7'";}?>>开通 (<?php echo ($is_num["0"]["num"]); ?>)</a><a
						href="<?php echo U('Xitong/city?vcode=2');?>"<?php if($vcode == 2){echo "style='background: #6ad1df;border: 1px solid #6ad1df; color:
						#fff;'";}?>>封存 (<?php echo ($no_num["0"]["nums"]); ?>)</a><a href="<?php echo U('Xitong/city');?>"<?php
 if(empty($vcode)){echo "style='background: #6ad1df;border: 1px
						solid #6ad1df; color: #fff;'";}?>>全部 (<?php echo ($all_dregde["0"]["numer"]); ?>)</a>
				</div>
				<div class="query_div">是否开通&nbsp&nbsp&nbsp</div>
				<div class="query_div">
					省份名称 <input type="text" class="query_txt" name="province_name" id="province_name"
						value="<?php echo ($cityname); ?>" />
				</div>
			</form>
		</div>
		<table class="table table-hover table-bordered table-list"
			id="menus-table">
			<tr>
				<th>#</th>
				<th onclick="order('<?php echo ($order); ?>')">省份编码<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
				<th>省份名称</th>
				<th>省份简称</th>
				<th>城市编码</th>
				<th>城市名称</th>
				<th>车首页CityID</th>
				<th>爱车坊CityID</th>
				<th>车牌头前两位</th>
				<th>车首页发动机号位数</th>
				<th>车首页VIN(车架)位数</th>
				<th>爱车坊发动机号位数</th>
				<th>爱车坊VIN(车架)位数</th>
				<th>Registno</th>
				<th>vcode</th>
				<th>是否开通</th>
			</tr>
			<?php $i = 1;?>
			<?php $i = $pageIndex + $i;?>
			<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
				<td><?php echo ($i++); ?></td>
				<td><?php echo ($vo["sf_id"]); ?></td>
				<td><?php echo ($vo["province"]); ?></td>
				<td><?php echo ($vo["abbreviation"]); ?></td>
				<td><?php echo ($vo["id"]); ?></td>
				<td><?php echo ($vo["city"]); ?></td>
				<td><?php echo ($vo["code"]); ?></td>
				<td><?php echo ($vo["acode"]); ?></td>
				<td><?php echo ($vo["nums"]); ?></td>
				<td><?php echo ($vo["c_engine_nums"]); ?></td>
				<td><?php echo ($vo["c_frame_nums"]); ?></td>
				<td><?php echo ($vo["engine_nums"]); ?></td>
				<td><?php echo ($vo["frame_nums"]); ?></td>
				<td><?php echo ($vo["registno"]); ?></td>
				<td><?php echo ($vo["vcode"]); ?></td>
				<td class="iftd"><?php if($vo['is_dredge'] != 0): ?><a
						onclick="city_update('<?php echo ($vo["id"]); ?>','<?php echo ($vo["is_dredge"]); ?>')" href="#">是</a><a style="background: #f77462; color: #fff;">否</a>
					<?php else: ?>
					<a style="background: #66d99f; color: #fff;">是</a><a
						onclick="city_update('<?php echo ($vo["id"]); ?>','<?php echo ($vo["is_dredge"]); ?>')" href="#">否</a><?php endif; ?>
				</td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="pagination"><?php echo ($Page); ?></div>
		<div id="city_add">
			<div class="city_top">
				<span class="city_span">添加城市</span><a href="<?php echo U('Xitong/city');?>"
					class="city_close closebtn">X</a>
			</div>
			<form method="post" class="form-horizontal J_ajaxForm"
				action="<?php echo U('Xitong/city_add');?>">
				<table id="city_tab1" width="100%">
					<tr>
						<td class="td1">省份编码</td>
						<td class="td2"><input type="text" class="input" name="sf_id"
							id="model" value=""></td>
						<!-- <td class="td1">城市编码</td>
						<td class="td2"><input type="text" class="input" name="code"
							value=""></td> -->
						<td class="td1">是否开通</td>
						<td class="td2"><input type="radio" class="input"
							name="is_dredge" value="0" checked="checked"> 是 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
							type="radio" class="input" name="is_dredge" value="1"
							checked="checked"> 否</td>
					</tr>
					<tr>
						<td class="td1">车首页CityID</td>
						<td class="td2"><input type="text" class="input"
							name="code" id="code" value=""></td>
						<td class="td1">爱车坊CityID</td>
						<td class="td2"><input type="text" class="input" name="acode"
							id="acode" value=""></td>
					</tr>
					<tr>
						<td class="td1">省份名称</td>
						<td class="td2"><input type="text" class="input"
							name="province" id="app" value=""></td>
						<td class="td1">城市名称</td>
						<td class="td2"><input type="text" class="input" name="city"
							id="model" value=""></td>
					</tr>
					<tr>
						<td class="td1">省份简称</td>
						<td class="td2"><input type="text" class="input"
							name="abbreviation" id="model" value=""></td>
						<td class="td1">车牌头前两位</td>
						<td class="td2"><input type="text" class="input" name="nums"
							id="action" value=""></td>
					</tr>
				</table>
				<table id="city_tab2">
					<tr>
						<td class="td1">车首页发动机号位数</td>
						<td><input type="text" class="input" name="c_engine_nums"
							id="action" value=""></td>
						<td class="td3">需要输入发动机号尾数（"-1": 全部输入，"0":
							不需要输入，其他的显示几位输入发动机号后面几位）</td>
					</tr>
					<tr>
						<td class="td1">车首页VIN(车架)位数</td>
						<td><input type="text" class="input" name="c_frame_nums"
							id="action" value=""></td>
						<td class="td3">需要输入车架号位数（"-1": 全部输入，"0":
							不需要输入，其他的显示几位输入车架号后面几位）</td>
					</tr>
					<tr>
						<td class="td1">爱车坊发动机号位数</td>
						<td><input type="text" class="input" name="engine_nums"
							id="action" value=""></td>
						<td class="td3">需要输入发动机号尾数（"-1": 全部输入，"0":
							不需要输入，其他的显示几位输入发动机号后面几位）</td>
					</tr>
					<tr>
						<td class="td1">爱车坊VIN(车架)位数</td>
						<td><input type="text" class="input" name="frame_nums"
							id="action" value=""></td>
						<td class="td3">需要输入车架号位数（"-1": 全部输入，"0":
							不需要输入，其他的显示几位输入车架号后面几位）</td>
					</tr>
					<tr>
						<td class="td1">registno(证书编号)</td>
						<td><input type="text" class="input" name="registno"
							id="model" value=""></td>
						<td class="td3">需要输入证书编号位数（"-1": 全部输入，"0":
							不需要输入，其他的显示几位输入证书编号后面几位）</td>
					</tr>
					<tr>
						<td class="td1">vcode</td>
						<td><input type="text" class="input" name="vcode" id="action"
							value=""></td>
						<td></td>
					</tr>
				</table>
				<div>
					<input type="submit" style="background:#66d99f;" class="query_btn" value="添加" /> <input
						type="button" style="background:#ffa600;" class="query_btn closebtn" value="取消" />
				</div>
			</form>
		</div>
	</div>
</body>
</html>