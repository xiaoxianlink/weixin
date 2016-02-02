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
</style>
<script type="text/javascript">
	function edit(id,code,money,points,content,explain,gist,area,state) {
		$("#id").val(id);
		$("#code").val(code);
		$("#money").val(money);
		$("#points").val(points);
		$("#content").val(content);
		$("#explain").val(explain);
		$("#gist").val(gist);
		$("#area").val(area);
		if (state == 0) {
			$("#state0").attr("checked", true);
		} else {
			$("#state1").attr("checked", true);
		}
	}
	function add() {
		$("#id").val(0);
		$("#code").val('');
		$("#money").val('');
		$("#points").val('');
		$("#content").val('');
		$("#explain").val('');
		$("#gist").val('');
		$("#area").val('');
		$("#state1").attr("checked", false);
	}
	$(function() {
		$('#city_add').hide();
		$('#city_link').click(function() {
			$('body').append('<div class="mask" id="mask"></div>');
			$('#city_add').css('left', Math.ceil(($('body').width()) / 5) + 'px');
			$('#city_add').css('top', Math.round(($(window).height() - $("#city_add").height()) / 2 - 100 + document.body.scrollTop) + 'px');
			$('#city_add').show();
		});
		$('.edit').click(function() {
			$('body').append('<div class="mask" id="mask"></div>');
			$('#city_add').css('left', Math.ceil(($('body').width()) / 5) + 'px');
			$('#city_add').css('top', Math.round(($(window).height() - $("#city_add").height()) / 2 - 100 + document.body.scrollTop) + 'px');
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
		 var wei_code = $("#wei_code").val();
		 var wei_range = $("#wei_range").val();
		 var wei_state = $("#wei_state").val();
		 var wei_type = $("#wei_type").val();
		 window.location.href="<?php echo U('Xitong/daima');?>&order="+order+"&wei_code="+wei_code+"&wei_range="+wei_range+"&wei_state="+wei_state+"&wei_type="+wei_type; 
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
			<form action="<?php echo U('Xitong/daima');?> " method="post">
			<input type="button" id="city_link" class="query_btn"
					style="background: #f77462; width: 150px;" onclick="add()" value="添加" />
			<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
			<div  class="query_div" >
			状态		<select name="wei_type" id="wei_type" class="query_txt" >
								<option value=""<?php if($array[3]==0){echo "selected='selected'";}?>>全部(<?php echo ($array_num["2"]); ?>)</option>
								<option value="1"<?php if($array[3]==1){echo "selected='selected'";}?>>使用(<?php echo ($array_num["0"]); ?>)</option>
								<option value="2" <?php if($array[3]==2){echo "selected='selected'";}?>>不使用(<?php echo ($array_num["1"]); ?>)</option>
							</select>
			</div>			
			<div  class="query_div" >
			处罚类别 	<select name="wei_state" id="wei_state" class="query_txt" >
								<option value=""></option>
								<option value="1" <?php if($array[2]==1){echo "selected='selected'";}?>>0分处罚</option>
								<option value="2" <?php if($array[2]==2){echo "selected='selected'";}?>>1分处罚</option>
								<option value="3" <?php if($array[2]==3){echo "selected='selected'";}?>>2分处罚</option>
								<option value="4" <?php if($array[2]==4){echo "selected='selected'";}?>>3分处罚</option>
								<option value="5" <?php if($array[2]==5){echo "selected='selected'";}?>>4分处罚</option>
								<option value="6" <?php if($array[2]==6){echo "selected='selected'";}?>>5分处罚</option>
								<option value="7" <?php if($array[2]==7){echo "selected='selected'";}?>>6分处罚</option>
								<option value="13" <?php if($array[2]==13){echo "selected='selected'";}?>>12分处罚</option>
							</select>
			</div>	
			 <div  class="query_div" > 
					适用范围 <input type="text" name="wei_range" id="wei_range" class="query_txt" value="<?php echo ($array["1"]); ?>"/>
			</div>	
			<div  class="query_div" > 
					违章代码 <input type="text" name="wei_code" id="wei_code" class="query_txt" value="<?php echo ($array["0"]); ?>"/>
			</div>					
			</form>
	</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th onclick="order('<?php echo ($order); ?>')">违章代码<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
		<th>处罚金额</th>
		<th>处罚扣分</th>
		<th>违章内容</th>
		<th>处罚说明</th>
		<th>处罚依据</th>
		<th>适用范围</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["code"]); ?></td>
			<td><?php echo ($vo["money"]); ?></td>
			<td><?php echo ($vo["points"]); ?></td>
			<td style="text-align:left"><?php echo ($vo["content"]); ?></td>
			<td><?php echo ($vo["explain"]); ?></td>
			<td><?php echo ($vo["gist"]); ?></td>
			<td><?php echo ($vo["city"]); ?></td>
			<td><?php if($vo['state'] == 0): ?>使用<?php else: ?>不使用<?php endif; ?></td>
			<td width="110"><input type="button" onclick="edit('<?php echo ($vo["id"]); ?>','<?php echo ($vo["code"]); ?>','<?php echo ($vo["money"]); ?>','<?php echo ($vo["points"]); ?>','<?php echo ($vo["content"]); ?>','<?php echo ($vo["explain"]); ?>','<?php echo ($vo["gist"]); ?>','<?php echo ($vo["area"]); ?>','<?php echo ($vo["state"]); ?>')" style="background:#ffa600; float: initial; margin: 0;" class="query_btn edit" value="修改" /></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
<div id="city_add" style="width: 930px; height: 480px;">
			<div class="city_top">
				<span class="city_span">添加代码</span><a href="#"
					class="city_close closebtn">X</a>
			</div>
			<form method="post" class="form-horizontal J_ajaxForm" action="<?php echo U('Xitong/dai_add');?>">
			<input type="hidden" id="id" name="id" value="0" />
				<table id="city_tab1" class="city_tab1" width="100%">
					<tr>
							<td class="td1">违章代码</td>
							<td class="td3"><input type="text" class="input" id="code" name="code" value=""></td>
							<td colspan="3">适用范围 <input type="text" class="input" id="area" name="area" value=""></td>
					</tr>
					<tr>
						<td class="td1">处罚金额</td>
						<td class="td3"><input type="text" class="input" id="money" name="money" value="">
							元</td>
							<td colspan="3" style="font-size: 13px;">（适用范围填写城市编码，全国以0代替，多个城市以,分隔）</td>
					</tr>
					<tr>
						<td class="td1">处罚扣分</td>
						<td class="td3"><input type="text" class="input" name="points" id="points" value="">
							分</td>
							<td >状态</td>
							<td >使用<input type="radio" id="state0" name="state" value="0"></td>
							<td >不使用<input type="radio" id="state1" name="state" value="1"></td>
					</tr>
					<tr>
						<td class="td1">违章内容</td>
						<td class="td3" colspan="4"><textarea id="content" class="control_textarea" name="content" rows="3" ></textarea></td>
					</tr>
					<tr>
						<td class="td1">处罚说明</td>
						<td class="td3" colspan="4"><textarea id="explain" class="control_textarea" name="explain" rows="2"  ></textarea></td>
					</tr>
					<tr>
						<td class="td1">处罚依据</td>
						<td class="td3" colspan="4"><textarea id="gist" class="control_textarea" name="gist" rows="2"  ></textarea></td>
					</tr>
				</table>
				<div>
					<input type="submit" style="background:#66d99f;" class="query_btn" value="确定" /> <input
						type="button" style="background:#ffa600;" class="query_btn closebtn" value="取消" />
				</div>
			</form>
 </div>
</body>
</html>