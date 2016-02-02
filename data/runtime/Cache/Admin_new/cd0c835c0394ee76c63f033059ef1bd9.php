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
#mask {
	position: fixed;
}
.iftd a{
	width: 50px;
}
.mask {
	left: 0;
	top:;
	right: 0;
	bottom: 0;
	width: 100%;
	height: 100%;
	background: #000;
	filter: alpha(opacity =   30);
	opacity: .3;
	z-index: 1;
}

#fuwu_div {
	position: absolute;
	width: 70%;;
	height: 70%;
	background: white;
	margin-top: -450px;
	border-radius: 5px;
	z-index: 2;
}

.city_float {
	float: left;
}

.city_top {
	background: #6DC4DC;
	border: 1px solid #6DC4DC;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	margin-bottom: 30px;
	color: white;
	height: 35px;
	line-height: 35px;
}

.city_close {
	display: block;
	float: right;
	color: black;
	margin-right: 2%;
}

.city_span {
	margin-left: 3%;
}

.count_txt {
	float: left;
	font-size: 14px;
	text-align: center;
	color: #FFF;
}
</style>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
<script type="text/javascript">
	function HiddenDiv() {
		divObj = document.getElementById("heidden_div");
		divObj.style.visibility = "hidden";
	}
	function ShowPrompt2(id,services_sn) {
		var type = document.getElementById("type").value;
		var time_start = document.getElementById("time_start").value;
		var time_end = document.getElementById("time_end").value;
		$.post('<?php echo U("Jiaoyi/fuwu_div");?>',
						{
							id : id,
							type : type,
							time_start : time_start,
							time_end : time_end,
							services_sn : services_sn
						},
						function(array) {
							$('body').append(
									'<div class="mask" id="mask"></div>');
							$('body')
									.append(
											'<div id="fuwu_div"><div class="city_top"><span class="city_span">添加城市</span><a href='
													+ "<?php echo U('Jiaoyi/fuwu');?>"
													+ ' class="city_close">X</a></div><div id="cheliang_submit" class="top_div"><form ><input type="button"   onclick="ShowPrompt3('
													+ id
													+ ',0)" class="query_btn" style="margin-left: 5px;"  value="查询" /><div  class="query_div" id="elect" ><a herf="#" id="a1" onclick="ShowPrompt3('+array[2]+',1)">今天</a><a id="a2" onclick="ShowPrompt3('+array[2]+',2)" herf="#">昨天</a><a id="a3" onclick="ShowPrompt3('+array[2]+',3)" herf="#">最近7天</a></div><div  class="query_div" style="margin-right: 5px;" > 交易时间 <input type="text" name="fu_time_start"  class="input length_3 J_datetime" id="time_start_a" style="width: 130px" value=""/> — <input type="text" style="width: 130px"   class="input length_3 J_datetime" id="time_end_b" name="fu_time_end" value=""/></div><div  class="query_div" > 交易类型 <select id="type_text" style="width: 130px;"><option value=""></option><option value="2">微信</option><option value="1">支付宝</option></select></div></form></div>		<div class="count_div" id="count_div_a">'
													+ array[1]
													+ '</div><table class="table table-hover table-bordered table-list window_tab" id="menus-table"><tr><th>账户变动时间</th><th>收入金额</th><th>支出金额</th><th>未结算金额</th><th>可提现金额</th><th>账户余额</th><th>订单号</th><tr/><div id="array_num">'
													+ array[0]
													+ '</div></table></div>');
							$('#fuwu_div').css('left',
									Math.ceil(($('body').width()) / 5) + 'px');
							$('#fuwu_div').css('top',
									Math.round(($(window).height() + 100) / 2 + document.body.scrollTop) + 'px');
						});
	}
	function ShowPrompt3(id,state) {
		if (state == 1) {
			$("#a1").css('background-color','#6ad1df');
			$("#a1").css('border','1px solid #6ad1df');
			$("#a1").css('color','#fff');
			$("#a2").css('background-color','#fff');
			$("#a2").css('border','1px solid #e2e7e7');
			$("#a2").css('color','#000');
			$("#a3").css('background-color','#fff');
			$("#a3").css('border','1px solid #e2e7e7');
			$("#a3").css('color','#000');
		} else if (state == 2) {
			$("#a2").css('background-color','#6ad1df');
			$("#a2").css('border','1px solid #6ad1df');
			$("#a2").css('color','#fff');
			$("#a1").css('background-color','#fff');
			$("#a1").css('border','1px solid #e2e7e7');
			$("#a1").css('color','#000');
			$("#a3").css('background-color','#fff');
			$("#a3").css('border','1px solid #e2e7e7');
			$("#a3").css('color','#000');
		} else if (state == 3){
			$("#a3").css('background-color','#6ad1df');
			$("#a3").css('border','1px solid #6ad1df');
			$("#a3").css('color','#fff');
			$("#a2").css('background-color','#fff');
			$("#a2").css('border','1px solid #e2e7e7');
			$("#a2").css('color','#000');
			$("#a1").css('background-color','#fff');
			$("#a1").css('border','1px solid #e2e7e7');
			$("#a1").css('color','#000');
		}
		var type = document.getElementById("type_text").value;
		var time_start = document.getElementById("time_start_a").value;
		var time_end = document.getElementById("time_end_b").value;
		$.post('<?php echo U("Jiaoyi/fuwu_div2");?>', {
			id : id,
			type : type,
			time_start : time_start,
			time_end : time_end,
			state : state
		}, function(data) {
			var date = data[0];
			$(".window_tab").html(date);
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
			<form action="<?php echo U('Jiaoyi/fuwu');?>  " method="post">
				<input type="submit" id="cheliang_submit_input" class="query_btn"
					value="查询" />
				<div class="query_div">
					提现户名<input type="text" class="query_txt" name="fu_name"
						value="<?php echo ($array_post["0"]); ?>" />
				</div>
				<div class="query_div">
					供应商编号 <input type="text" class="query_txt" name="fu_number"
						value="<?php echo ($array_post["1"]); ?>" />
				</div>
				<div id="elect"  class="query_div">
					状态 <a href="<?php echo U('Jiaoyi/fuwu?state=1');?>" <?php if($vstate == 1){echo "style='background: #6ad1df;border: 1px solid #6ad1df;
						color: #fff;'";}else{echo "style='border-left:1px solid
						#e2e7e7'";}?>>正常(<?php echo ($state["0"]); ?>)</a><a <?php if($vstate == 2){echo "style='background: #6ad1df;border: 1px solid #6ad1df;
						color: #fff;'";}else{echo "style='border-left:1px solid
						#e2e7e7'";}?>
						href="<?php echo U('Jiaoyi/fuwu?state=2');?>">封存(<?php echo ($state["1"]); ?>)</a><a <?php if(empty($vstate)){echo "style='background: #6ad1df;border: 1px solid #6ad1df;
						color: #fff;'";}else{echo "style='border-left:1px solid
						#e2e7e7'";}?>
						href="<?php echo U('Jiaoyi/fuwu');?>">全部(<?php echo ($state["2"]); ?>)</a>
				</div>
				<div class="query_div">
					手机号 <input type="text" class="query_txt" name="fu_phone"
						value="<?php echo ($array_post["2"]); ?>" />
				</div>
			</form>
		</div>
		<div class="count_div">
			<div class="count_txt">余额总计：<?php echo ($money["0"]["money"]); ?></div>
			<div class="count_txt">可提现金额：<?php echo ($money["0"]["user_money"]); ?></div>
			<div class="count_txt">未结算金额：<?php echo ($money["0"]["end_money"]); ?></div>
		</div>
		<table class="table table-hover table-bordered table-list"
			id="menus-table">
			<tr>
				<th>#</th>
				<th>服务商编号</th>
				<th>手机号</th>
				<th>状态</th>
				<th>账户余额</th>
				<th>可提现金额</th>
				<th>未结算金额</th>
				<th>提现账户类型</th>
				<th>提现户名</th>
				<th>银行卡号</th>
				<th>开户银行</th>
				<th>操作</th>
			</tr>
			<?php $i = 1;?>
			<?php $i = $pageIndex + $i;?>
			<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
				<td><?php echo ($i++); ?></td>
				<td><?php echo ($vo["services_sn"]); ?></td>
				<td><?php echo ($vo["phone"]); ?></td>
				<td><?php if($vo['state'] == 0): ?>正常<?php else: ?>封存<?php endif; ?></td>
				<td><span id="jiaoyi_fuwu_link" class="sview"
					onclick="ShowPrompt2('<?php echo ($vo["bank_id"]); ?>','<?php echo ($vo["services_sn"]); ?>')"><?php echo ($vo["money"]); ?></span></td>
				<td><?php echo ($vo["user_money"]); ?></td>
				<td><?php echo ($vo["end_money"]); ?></td>
				<td><?php if($vo['type'] == 2): ?>公司账户<?php else: ?>个人账户<?php endif; ?></td>
				<td><?php echo ($vo["name"]); ?></td>
				<td><?php echo ($vo["user_number"]); ?></td>
				<td><?php echo ($vo["user_bank"]); ?></td>
				<td class="iftd"><?php if($vo['state'] == 0): ?><a style="background: #66d99f; color: #fff;"
						href="<?php echo U('Jiaoyi/fuwu_update?id='); echo ($vo["id"]); ?>&state=<?php echo ($vo["state"]); ?>">封存</a><a>启封</a><?php else: ?><a>封存</a><a style="background: #f77462; color: #fff;"
						href="<?php echo U('Jiaoyi/fuwu_update?id='); echo ($vo["id"]); ?>&state=<?php echo ($vo["state"]); ?>">启封</a><?php endif; ?>
				</td>
			</tr><?php endforeach; endif; ?>
		</table>
	</div>
	<div id="hidden_div" style="display: none">
		<input type="button" id="cheliang_submit_input"
			onclick="ShowPrompt2()" class="query_btn" value="查询" />
		<div class="query_div">
			<a>今天</a> <a>昨天</a> <a>最近7天</a>
		</div>
		<div class="query_div">
			交易时间 <input type="text" name="fu_time_start"
				class="input length_3 J_datetime" id="time_start" value="" /> — <input
				type="text" class="input length_3 J_datetime" id="time_end"
				name="fu_time_end" value="" />
		</div>
		<div class="query_div">
			交易类型 <select id="type">
				<option value="2">微信</option>
				<option value="1">支付宝</option>
			</select>
		</div>
	</div>
</body>
</html>