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
	function get_coupon(user_id, id, license_number, so_id, order_id, money) {
		window.location.href = "<?php echo u('Scan/ucoupon_list');?>" + "&user_id="
				+ user_id + "&id=" + id + "&license_number=" + license_number
				+ "&so_id=" + so_id + "&order_id=" + order_id + "&money="
				+ money;
	}
	function pay(order_id, cuc_id) {
		$.post("<?php echo u('Scan/wxpay');?>", {
			'order_id' : order_id,
			'cuc_id' : cuc_id
		}, function(data) {
			if (data.url != 1) {
				window.location.href = data.url;
			} else {
				alert("订单已支付");
				WeixinJSBridge.call('closeWindow');
			}
		});
	}
</script>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" class="pad_l">
		<tr>
			<td colspan="2" class="td" style="padding-top: 20px;">违章地区：<?php echo ($endorsement["area"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章时间：<?php echo date('Y-m-d H:i:s', $endorsement['time']); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章地点：<?php echo ($endorsement["address"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章内容：<?php echo ($endorsement["content"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章代码：<?php echo ($endorsement["code"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">罚款：<?php echo ($endorsement["money"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">罚分：<?php echo ($endorsement["points"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">处理编号：<?php echo ($order["order_sn"]); ?></td>
		</tr>
        <tr>
			<td colspan="2" class="td dy_td" style="padding-right: 20px;"><hr /></td>
		</tr>
		<tr>
			<td colspan="2" class="td dy_td" style="padding-bottom: 10px;">代缴价：<?php echo ($so["money"]); ?>元</td>
		</tr>
		<?php if($ucoupon_count != 0): ?><tr onclick="get_coupon('<?php echo ($order["user_id"]); ?>', '<?php echo ($endorsement["id"]); ?>', '<?php echo ($license_number); ?>', '<?php echo ($so["id"]); ?>', '<?php echo ($order["id"]); ?>', '<?php echo ($so["money"]); ?>')" style="background-color: #f77462;">
        <?php if(empty($coupon)): ?><td class="bottom_td td" style="border-bottom-right-radius:0;background-color: #f77462;">优惠：您有<?php echo ($ucoupon_count); ?>张可用优惠券
		<?php else: ?><td class="bottom_td td" style="border-bottom-right-radius:0;background-color: #f77462;"><?php echo ($coupon["name"]); ?>&nbsp;&nbsp;<?php echo ($coupon["money"]); ?>元<?php endif; ?></td>
        <td style="text-align: right; padding-right: 20px;border-bottom-right-radius:5px;"><img src="/tpl/simplebootx/Public/images/weixin/ic_zd.png" width="10px" /></td>
        </tr><?php endif; ?>
	</table>
    <br/><br/>
	<div class="addcar_btn" style="border:#66d99f 2px solid;" onclick="pay('<?php echo ($order["id"]); ?>', '<?php echo ($coupon["cuc_id"]); ?>')">
		<span>付款：<?php echo $so['money']-$coupon_money; ?>元</span>
	</div>
	<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>