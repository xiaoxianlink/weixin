<?php

namespace Weixin\Controller;

use Weixin\Controller\IndexController;
use Think\Log;
use Think\Model;

class OrderController extends IndexController {
	public function index() {
		if (! isset ( $_GET ['code'] )) {
			$redirect_uri = URL2;
			$scope = 'snsapi_base';
			$log = new Log();
			$log->write ( "order请求", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y/m/d' ) . '.log' );
			$this->oauth ( $redirect_uri, $scope );
		} else {
			$code = ( string ) $_GET ['code'];
			$open_id = $this->get_oauth_openid ( $code );
			$log = new Log();
			$log->write ( "order微信回调", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y/m/d' ) . '.log' );
		}
		$user_model = M ( "User" );
		$where = array (
				'openid' => ( string ) $open_id 
		);
		$user = $user_model->where ( $where )->find ();
		$user_id = $user ['id'];
		$orderlist = $this->get_user_order ( $user_id );
		/*
		 * $msg = $this->get_endorsement (); print_r ( $msg );
		 */
		$order_status = array (
				1 => '未支付',
				2 => '确认中',
				3 => '处理中',
				5 => '已处理',
				6 => '退款中',
				7 => '已退款',
				8 => '已取消' 
		);
		
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'order_status', $order_status );
		$this->assign ( 'orderlist', $orderlist );
		$this->display ( ":order" );
	}
	public function get_order() {
		$user_id = $_REQUEST ['user_id'];
		$pageIndex = $_REQUEST ['pageIndex'];
		$order_list = $this->get_user_order ( $user_id, $pageIndex );
		$i = 0;
		$list = array ();
		$order_status = array (
				1 => '未支付',
				2 => '确认中',
				3 => '处理中',
				5 => '已处理',
				6 => '退款中',
				7 => '已退款',
				8 => '已取消' 
		);
		foreach ( $order_list as $v ) {
			$endorsement_id = "'{$v['endorsement_id']}'";
			$car_id = "'{$v['car_id']}'";
			$license_number = "'{$v['license_number']}'";
			$so_id = "'{$v['so_id']}'";
			$user_id = "'{$user_id}'";
			$table = '<table border="0" cellpadding="0" cellspacing="0" class="pad_l">';
			$table .= '<thead><th colspan="2" class="th td">' . $v ['license_number'] . '</th></thead>';
			$table .= '<tr><td colspan="2" class="td"  style="padding-top:15px;">处理编号：' . $v ['order_sn'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">违章地区：' . $v ['area'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">违章代码：' . $v ['code'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">违章时间：' . date ( 'Y-m-d H:i:s', $v ['time'] ) . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">违章地点：' . $v ['address'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">违章内容：' . $v ['content'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td">罚&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;款：' . $v ['money'] . '</td></tr>';
			$table .= '<tr><td colspan="2" class="td" style="padding-bottom:15px;">罚&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分：' . $v ['points'] . '</td></tr>';
			$table .= '<tr><td class="l_pay td" style="font-size:20px;padding-top:10px;">' . $v ['money'] . '元</td>';
			if ($v ['order_status'] == 1) {
				$table .= '<td rowspan="2" class="r_pay" onclick="scan_info(' . $endorsement_id . ', ' . $car_id . ', ' . $license_number . ', ' . $so_id . ', '  . $so_type . ', '. $user_id . ')">';
			} else {
				$table .= '<td rowspan="2" class="r_pay">';
			}
			$table .= $order_status [$v ['order_status']] . '</td></tr>';
			$table .= '<tr><td class="l_pay td" style="font-size:12px;padding-bottom:10px;">' . date ( 'Y/m/d H:i', $v ['last_time'] ) . '</td></tr></table>';
			$list [$i] = $table;
			$i ++;
		}
		$data = array (
				0 => $list 
		);
		$this->ajaxReturn ( $data );
	}
	function get_user_order($user_id, $pageIndex = 0, $pageSize = 5) {
		$order_model = M ( "Order" );
		$field = "o.id,o.order_sn,o.money as o_money,o.order_status,o.last_time,o.endorsement_id,o.car_id,o.so_id,o.so_type";
		$field .= ",endor.area,endor.code,endor.time,endor.address,endor.content,endor.money,endor.points,endor.license_number";
		$join1 = "cw_endorsement as endor on endor.id = o.endorsement_id";
		$where = "o.user_id = '$user_id'";
		$pageIndex = $pageIndex * $pageSize;
		$limit = "limit $pageIndex, $pageSize";
		$sql = "select $field from __TABLE__ as o left join $join1 where $where order by o.last_time desc $limit";
		$order_list = $order_model->query ( $sql );
		foreach ( $order_list as $k => $v ) {
			$l_num1 = mb_substr ( $v ['license_number'], 0, 2, 'utf-8' );
			$l_num2 = mb_substr ( $v ['license_number'], 2, strlen ( $v ['license_number'] ), 'utf-8' );
			$order_list [$k] ['license_number'] = $l_num1 . "·" . $l_num2;
		}
		return $order_list;
	}
}