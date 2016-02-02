<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>添加车辆</title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet">
<script src="/statics/js/jquery.js"></script>
<script type="text/javascript">
	function select_license(user_id) {
		var license_number = $("#license_number").val();
		var frame_number = $("#frame_number").val();
		var engine_number = $("#engine_number").val();
		window.location.href="<?php echo u('Sub/select_license');?>"+"&user_id="+user_id+"&license_number="+license_number+"&frame_number="+frame_number+"&engine_number="+engine_number;
	}
	function section() {
		var frame_number = $("#frame_number").val();
		frame_number = frame_number.replace(/\s/g, "");
		if (frame_number != '') {
			var newstr1 = frame_number.substr(0, 3);
		    var newstr2 = frame_number.substr(3, 6);
		    var newstr3 = frame_number.substr(9);
		    var newstr = newstr1 + ' ' + newstr2 + ' ' + newstr3;
		    $("#frame_number").val("").focus().val(newstr);
		}
	}
	function onse() {
		var frame_number = $("#frame_number").val();
		var newstr = frame_number.replace(/\s/g, "");
		
		$("#frame_number").val(newstr);
	}
	/* $("#frame_number").bind('input propertychange', function() {
		var frame_number = $("#frame_number").val();
		if (frame_number != '') {
			var newstr1 = frame_number.substr(0, 3);
		    var newstr2 = frame_number.substr(3, 6);
		    var newstr3 = frame_number.substr(9);
		    var newstr = newstr1 + ' ' + newstr2 + ' ' + newstr3;
		    $("#frame_number").val(newstr);
		}
	}); */
	function valid(){
		var license_number = $("#license_number").val();
		var frame_number = $("#frame_number").val();
		var engine_number = $("#engine_number").val();
		frame_number = frame_number.replace(/\s/ig,"");
		if(license_number==""){
			$("#list").html("车牌号码不能为空！");
			showbtn();
			return false;
		}
		if(frame_number==""){
			$("#list").html("车架号不能为空！");
			showbtn();
			return false;
		}
		if(engine_number==""){
			$("#list").html("发动机号不能为空！");
			showbtn();
			return false;
		}
		var Regx = /^[A-Za-z0-9]*$/;
		if (!Regx.test(license_number) || license_number.length != 5) {
			$("#list").html("车牌号码不正确！");
			showbtn();
			return false;
		}
		if (!Regx.test(frame_number) || frame_number.length < 10) {
			$("#list").html("车架号不正确！");
			showbtn();
			return false;
		}
		if (!Regx.test(engine_number)) {
			$("#list").html("发动机号不正确！");
			showbtn();
			return false;
		}
		return true;
	}
	function showbtn() {
        $("#bg").css({
            display: "block", height: $(document).height()
        });
        var $box = $('.box');
        $box.css({
            //设置弹出层距离左边的位置
            left: ($("body").width() - $box.width()) / 2 + "px",
            //设置弹出层距离上面的位置
            top: ($(window).height() - $box.height()) / 2 + $(window).scrollTop() + "px",
            display: "block"
        });
        setTimeout("close()",1200);
    }
	function close() {
        $("#bg,.box").css("display", "none");
    }
</script>
</head>
<body>
	<form name="myform" id="myform" action="<?php echo U('Sub/insert_car');?>"
		method="post" class="form-horizontal J_ajaxForms"
		enctype="multipart/form-data">
		<input type="hidden" name="user_id" value="<?php echo ($user_id); ?>" /> <input
			type="hidden" name="code" value="<?php echo ($code); ?>" />
        <div style="background-color: #FFFFFF;">
		<table border="0" cellpadding="0" cellspacing="0" class="pad_l" id="car_info">
			<tr>
				<td class="td cd_td td_br">车牌号码</td>
				<td class="font_color td cd_td td_br">|</td>
				<td class="font_color td cd_td td_br"><span style="color:#000" onclick="select_license(<?php echo ($user_id); ?>)">&nbsp;<?php echo ($abbreviation); ?></span>
				  <input type="hidden" name="license" id="license" value="<?php echo $abbreviation;?>" />〉
				  <input type="text" name="license_number" id="license_number" value="<?php echo $license_number; ?>" class="font_color text" style="width:100px;text-transform:uppercase;"/></td>
			</tr>
			<tr>
				<td class="td cd_td td_br">车&nbsp;&nbsp;架&nbsp;&nbsp;号</td>
				<td class="font_color td cd_td td_br">|</td>
				<td class="td cd_td td_br"><input oninput="section()" type="text" name="frame_number" id="frame_number" value="<?php echo $frame_number; ?>" class="font_color text" style="text-transform:uppercase;"/></td>
			</tr>
			<tr>
				<td class="td cd_td">发动机号</td>
				<td class="font_color td cd_td">|</td>
				<td class="td cd_td"><input type="text" name="engine_number" id="engine_number"
					value="<?php echo $engine_number; ?>" class="font_color text" style="text-transform:uppercase;" /></td>
			</tr>
		</table>
        </div>
        <br/>
		<div style="text-align:center;">
        	<table class="img_tb">
            	<tr>
                	<td style="width:33%"><div align="center"><img style="width:50px;" src="/tpl/simplebootx/Public/images/weixin/s3.png" /></div></td>
                    <td style="width:33%"><div align="center"><img style="width:50px;" src="/tpl/simplebootx/Public/images/weixin/s4.png" /></div></td>
                    <td style="width:33%"><div align="center"><img style="width:50px;" src="/tpl/simplebootx/Public/images/weixin/s5.png" /></div></td>
                </tr>
            </table>
		</div>
        <br/><br/>
			<button type="submit" class="addcar_btn" style="border:#66d99f 2px solid;" onclick="return valid();">免费订阅</button>
	</form>
	
<div id="bg"></div>
<div class="box" style="display:none">
    <div class="list" id="list">
        不能为空
    </div>
</div>
<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>