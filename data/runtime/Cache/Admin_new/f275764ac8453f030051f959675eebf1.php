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
 function ShowPrompt(objEvent,code,money,points,content) {
	 $("#frame_number").html(code+'-'+money+'元/'+points+'分');
	 $("#engine_number").html(content);
     var divObj = document.getElementById("promptDiv");
     divObj.style.visibility = "visible";
     var left = objEvent.clientX - 100;
     var top = objEvent.clientY + document.body.scrollTop;     //clientY 为鼠标在窗体中的 y 坐标值
     $("#promptDiv").css({"top":top,"left":left});
 }
 function HiddenPrompt() {
     divObj = document.getElementById("promptDiv");
     divObj.style.visibility = "hidden";
     divObj2 = document.getElementById("promptDiv2");
     divObj2.style.visibility = "hidden";
 }
 function order(order) {
	 if (order=='desc') {
		 order = 'asc';
	 } else {
		 order = 'desc';
	 }
	 var number = $("#number").val();
	 var city = $("#city").val();
	 var state = $("#state").val();
	 var time_end = $("#time_end").val();
	 var time_start = $("#time_start").val();
	 window.location.href="<?php echo U('Xitong/jilu');?>&order="+order+"&number="+number+"&city="+city+"&state="+state+"&time_end="+time_end+"&time_start="+time_start; 
 }
 function finish(e_id) {
 	if(window.confirm('你确定违章已处理吗？')){
 		$.post("<?php echo u('Xitong/e_finish');?>", {
			'e_id' : e_id
		}, function(data) {
			location.replace();
		});
    }else{
        return false;
	}
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
			<form action="<?php echo U('Xitong/jilu');?> " method="post">
			<input type="submit"  id="cheliang_submit_input"   class="query_btn"  value="查询"/>
			<div  class="query_div" > 
					违章时间 <input type="text" name="time_start" id="time_start"  class="input length_3 J_datetime"    value="<?php echo ($array["3"]); ?>"/> 
					— <input type="text"   class="input length_3 J_datetime" id="time_end" name="time_end" value="<?php echo ($array["4"]); ?>"/>
			</div>	
			<div  class="query_div" > 
				状态			<select name="state" id="state" class="query_txt"  >
								<option value="" >全部(<?php echo ($sums["0"]["numbers"]); ?>)</option>
								<option value="1" <?php if($array[2]==1){echo "selected='selected'";}?>>未处理</option>
								<option value="2" <?php if($array[2]==2){echo "selected='selected'";}?>>处理中</option>
								<option value="3" <?php if($array[2]==3){echo "selected='selected'";}?>>已处理</option>
							</select>
			</div>	
			<div  class="query_div" > 
			   违章城市<select name="city" id="city" class="query_txt"  >
							<option value="">全部</option>
							</select>
			</div>	
			<div  class="query_div" > 
					车牌号<input type="text"  class="query_txt"  name="number" id="number" value="<?php echo ($array["0"]); ?>"/>
			</div>	
			</form>
	</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>违章指纹</th>
		<th>车牌号</th>
		<th onclick="order('<?php echo ($order); ?>')">违章时间<?php if ($order == 'desc'){?>↓<?php }else{ ?>↑<?php } ?></th>
		<th>违章城市</th>
		<th>违章代码</th>
		<th>罚金</th>
		<th>罚分</th>
		<th>查询编号</th>
		<th>证书编号</th>
		<th>违章地点</th>
		<th style='width:20%'>违法信息</th>
		<th>采证机关</th>
		<th>是否已处理</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo md5($vo['license_number'].$vo['time']); ?></td>
			<td><?php echo ($vo["license_number"]); ?></td>
			<td><?php if($vo['time'] != ''): echo (date('Y-m-d H:i:s',$vo["time"])); endif; ?></td>
			<td><?php echo ($vo["area"]); ?></td>
			<td><span class="sview" onmouseover="ShowPrompt(event,'<?php echo ($vo["v_code"]); ?>','<?php echo ($vo["v_money"]); ?>','<?php echo ($vo["v_points"]); ?>','<?php echo ($vo["v_content"]); ?>')" onmouseout="HiddenPrompt()"><?php echo ($vo["code"]); ?></span></td>
			<td><?php echo ($vo["money"]); ?></td>
			<td><?php echo ($vo["points"]); ?></td>
			<td><?php echo ($vo["query_no"]); ?></td>
			<td><?php echo ($vo["certificate_no"]); ?></td>
			<td><?php echo ($vo["address"]); ?></td>
			<td><?php echo ($vo["content"]); ?></td>
			<td><?php echo ($vo["office"]); ?></td>
			<td><?php if($vo['is_manage'] == 0): ?><a href="#" onclick="finish('<?php echo ($vo["id"]); ?>')">未处理</a><?php elseif($vo['is_manage'] == 1): ?><a href="#" onclick="finish('<?php echo ($vo["id"]); ?>')">处理中</a><?php elseif($vo['is_manage'] == 2): ?>已处理<?php endif; ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div class="pagination"><?php echo ($Page); ?></div>
</div>
<div id="promptDiv" class="promptStyle">
	<br /><br />
	<div id="frame_number" class="prom_div"></div>
	<div id="engine_number" class="prom_div"></div>
</div>
<script type="text/javascript" src="/statics/js/common.js"></script>
<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
</body>
</html>