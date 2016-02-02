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
<body  class="J_scroll_fixed">
<div class="wrap J_check_wrap">
<div id="cheliang_submit" class="top_div">
	<form action="<?php echo U('Dingyue/users');?> " method="post">
				<input type="submit"  id="cheliang_submit_input"  class="query_btn"  value="查询"/>
				<div  id="cheliang_submit_text" class="query_div" > 
						用户昵称   <input type="text"   class="query_txt" name="username"  value="<?php echo ($input_sum["0"]); ?>"/>
				</div>
				<div  id="cheliang_submit_text" class="query_div" > 
						订阅渠道  <select  class="query_txt" name="type"  >
											<option value=""></option>
											<option value="1" <?php if($input_sum[1]==1){echo "selected='selected'";}?>>wechat</option>
											<option value="2" <?php if($input_sum[1]==2){echo "selected='selected'";}?>>alipay</option>
										</select>
				</div>
	</form>
</div>
<div class="count_div">
		<div class="count_txt">关注用户数：<a href="<?php echo U('Dingyue/users?concern=1');?>"><?php echo ($array_type["0"]); ?></a></div>
		<div class="count_txt">总数：<a href="<?php echo U('Dingyue/users');?>"><?php echo ($array_type["1"]); ?></a></div>
		<div class="count_txt" style="width: 500px;">订阅渠道：&nbsp;&nbsp;微信&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Dingyue/users');?>"><?php echo ($array_type["1"]); ?></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;alipay&nbsp;&nbsp;&nbsp;&nbsp;</div>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>用户编号</th>
		<th>OpenID</th>
		<th>GroupID</th>
		<th>用户名</th>
		<th>是否关注</th>
		<th>关注时间</th>
		<th>订阅渠道</th>
		<th>昵称</th>
		<th>所在城市</th>
	</tr>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($vo["iid"]); ?></td>
			<td><?php echo ($vo["id"]); ?></td>
			<td><?php echo ($vo["openid"]); ?></td>
			<td><?php echo ($vo["group_id"]); ?></td>
			<td><?php echo ($vo["username"]); ?></td>
			<td><?php if($vo['is_att'] == '0'): ?>是<?php else: ?>否<?php endif; ?></td>
			<td><?php if($vo['create_time'] == 0): else: echo (date('Y-m-d H:i:s',$vo["create_time"])); endif; ?></td>
			<td>wechat</td>
			<td><?php echo ($vo["nickname"]); ?></td>
			<td><?php echo ($vo["city"]); ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<div id="promptDiv" class="promptStyle">
	<br /><br />
	<div id="frame_number" class="prom_div"></div>
	<div id="engine_number" class="prom_div"></div>
</div>
<div id="promptDiv2" class="promptStyle2">
    <br /><br />
	<div id="nickname" class="prom_div2"></div>
	<div class="prom_div2">渠道：微信</div>
	<div id="openid" class="prom_div2"></div>
	<div id="city" class="prom_div2"></div>
	<div class="prom_div2">语言：zh_CN</div>
	<div id="c_time" class="prom_div2"></div>
</div>
</body>
</html>