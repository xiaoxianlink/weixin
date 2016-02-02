<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
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
	function scan_info(id,license_number,so_id,user_id){
		window.location.href="<?php echo u('Scan/scan_info');?>"+"&id="+id+"&license_number="+license_number+"&so_id="+so_id+"&user_id="+user_id;
	}
</script>
</head>
<body style="margin:0;padding-top:40px;">
	<div align="center" class="top_div">
		<span>违章：<?php echo ($endorsement["nums"]); ?>条</span>&nbsp;&nbsp;<span>罚款：<?php echo ($endorsement["all_money"]); ?>元</span>&nbsp;&nbsp;
		<span>扣分：<?php echo ($endorsement["all_points"]); ?>分</span>
	</div><br/>
	<?php if(is_array($endorsement_list)): foreach($endorsement_list as $k=>$v): ?><table border="0" cellpadding="0" cellspacing="0" class="pad_l">
		<tr>
			<td colspan="2" class="td" style="padding-top: 20px;">违章地区：<?php echo ($v["area"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章时间：<?php echo date('Y-m-d H:i:s', $v['time']); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章地点：<?php echo ($v["address"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章内容：<?php echo ($v["content"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章代码：<?php echo ($v["code"]); ?></td>
		</tr>
        <tr>
			<td colspan="2" class="td dy_td" style="padding-right: 20px;"><hr /></td>
		</tr>
		<tr>
			<td class="td lttd">罚款：<?php echo ($v["money"]); ?></td>
			<td class="rttd">罚分：<?php echo ($v["points"]); ?></td>
		</tr>
        <?php if(!empty($v['so_id'])): ?><tr>
        	<td colspan="2" align="center" onclick="scan_info('<?php echo ($v["id"]); ?>', '<?php echo ($license_number); ?>', '<?php echo ($v["so_id"]); ?>', '<?php echo ($user_id); ?>')" class="bottom_td">代缴：<?php echo ($v["so_money"]); ?>元</td>
        </tr><?php endif; ?>
	</table><br/><?php endforeach; endif; ?>
	<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>