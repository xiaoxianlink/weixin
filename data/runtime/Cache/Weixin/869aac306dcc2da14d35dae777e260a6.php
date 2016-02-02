<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>订阅车辆</title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet">
<script src="/statics/js/jquery.js"></script>
<script type="text/javascript">
	function select_nums(name,code) {
		var license_number = $("#license_number").val();
		var frame_number = $("#frame_number").val();
		var engine_number = $("#engine_number").val();
		var user_id = $("#user_id").val();
		window.location.href="<?php echo u('Sub/add_car');?>"+"&code="+code+"&name="+name+"&id="+user_id+"&license_number="+license_number+"&frame_number="+frame_number+"&engine_number="+engine_number;
	}
</script>
</head>
<body style="margin: 8px;">
	<input type="hidden" id="user_id" value="<?php echo ($user_id); ?>" />
	<input type="hidden" id="license_number" value="<?php echo ($license_number); ?>" />
	<input type="hidden" id="frame_number" value="<?php echo ($frame_number); ?>" />
	<input type="hidden" id="engine_number" value="<?php echo ($engine_number); ?>" />
	<table width="75%" border="0" cellpadding="0" cellspacing="0" class="pad_l" id="br_tb" style="border-top:0;width:100%;">
		<tr>
			<td class="cd_td" style="width: 25%; text-align:center;border-top:1px solid #c3c3c3;"><span style="font-size: 20px;"><?php echo ($region_list[$i*4+$j]['abbreviation']); ?></span><span style="font-size: 12px;"><?php echo ($region_list[$i*4+$j]['province']); ?></span></td>
			<td style="background-color:#ebebeb; border-right:0;"></td>
            <td style="background-color:#ebebeb; border-right:0;"></td>
            <td style="background-color:#ebebeb; border-right:0;"></td>
		</tr>
		<?php $__FOR_START_2143951248__=0;$__FOR_END_2143951248__=count($region_list)/4;for($i=$__FOR_START_2143951248__;$i < $__FOR_END_2143951248__;$i+=1){ ?><tr>
			<?php $c = 0;?>
			<?php $__FOR_START_1954495146__=0;$__FOR_END_1954495146__=4;for($j=$__FOR_START_1954495146__;$j < $__FOR_END_1954495146__;$j+=1){ if(!empty($region_list[$i*4+$j]['province'])): $c ++; ?>
			<td class="cd_td" style="width: 25%; text-align:center;"
				onclick="select_nums('<?php echo $region_list[$i*4+$j]['nums'];?>','<?php echo $region_list[$i*4+$j]['code'];?>')"><span style="font-size: 20px;"><?php echo ($region_list[$i*4+$j]['nums']); ?></span><span style="font-size: 12px;"><?php echo ($region_list[$i*4+$j]['city']); ?></span></td><?php endif; } ?>
			<?php $__FOR_START_1720830903__=0;$__FOR_END_1720830903__=3;for($j=$__FOR_START_1720830903__;$j < $__FOR_END_1720830903__;$j+=1){ if((($i+1) > count($region_list)/4) and ($c%4 != 0)): $c ++; ?>
			<td style="width: 25%"></td><?php endif; } ?>
		</tr><?php } ?>
	</table>
    <div style="position:fixed;bottom:0; width: 95%; padding-bottom: 20px;"><button type="submit" onclick="location.href='javascript:history.go(-1);'" class="addcar_btn" style="border:#5d9cec 2px solid; background-color: #5d9cec;">返回</button></div>
	<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>