<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo ($license_number); ?></title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet">
<script src="/statics/js/jquery.js"></script>
<script type="text/javascript">
	function select_coupon(cuc_id, id, license_number, so_id, user_id, order_id) {
		window.location.href = "<?php echo u('Scan/scan_info');?>&state=1" + "&cuc_id="
				+ cuc_id + "&id=" + id + "&license_number=" + license_number
				+ "&so_id=" + so_id + "&user_id=" + user_id + "&order_id="
				+ order_id;
	}
</script>
</head>
<body>
	<?php if(is_array($uuc_list)): foreach($uuc_list as $k=>$v): ?><div onclick="select_coupon('<?php echo ($v["cuc_id"]); ?>','<?php echo ($id); ?>','<?php echo ($license_number); ?>','<?php echo ($so_id); ?>','<?php echo ($user_id); ?>','<?php echo ($order_id); ?>')" class="coupon_bg">
    <table width="100%" style="padding: 20px;">
    	<tr>
        	<td style="color: #a9a6a1; font-size: 26px;" class="dy_td"><?php echo ($v["name"]); ?></td>
            <td align="right" rowspan="3" style="color: #9ebbaf; font-size: 28px;">￥<?php echo (int)$v['money']; ?></td>
        </tr>
        <tr>
        	<td style="color: #a9a6a1; font-size: 13px;" class="dy_td">满<?php echo ($v["condition"]); ?>使用</td>
        </tr>
        <tr>
        	<td style="color: #a9a6a1; font-size: 13px; height: 15px; line-height: 15px;">使用期限：<?php echo date('Y.m.d', $v['start_time']);?>-<?php echo date('Y.m.d', $v['expiration_time']);?></td>
        </tr>
    </table>
	</div><br/><?php endforeach; endif; ?>
	<div style="position:fixed;bottom:0; width: 100%; padding-bottom: 20px;"><button type="submit" onclick="select_coupon('0','<?php echo ($id); ?>','<?php echo ($license_number); ?>','<?php echo ($so_id); ?>','<?php echo ($user_id); ?>','<?php echo ($order_id); ?>')"class="addcar_btn" style="border:#fbcc5c 2px solid; background-color: #fbcc5c;">取消</button></div>
	<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>