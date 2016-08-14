<?php

namespace Weixin\Controller;

use Weixin\Controller\IndexController;
use Think\Log;
use Think\Model;
use Think\Template\Driver\Mobile;

class ScanController extends IndexController {
	
	function is_weixin(){
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			return true;
		}	
		return false;
	}
	
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} 
		else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	public function index() {
		$open_id = $_REQUEST ['openid'];
		$car_id = $_REQUEST ['carid'];
		$end_id = isset ( $_REQUEST ['end_id'] ) ? $_REQUEST ['end_id'] : 0;
		$user_model = M ( "User" );
		$where = array (
				'openid' => ( string ) $open_id 
		);
		$user = $user_model->where ( $where )->find ();
		if(empty($user) ){
			$user = $user_model->where ( "bizid = '$open_id'" )->find ();
		}
		if(!empty($user)){
			if($user['openid'] == $user['bizid'] && $this->is_weixin()){
				if (! isset ( $_GET ['code'] )) {
					$redirect_uri = $this->curPageURL();
					$scope = 'snsapi_base';
					$log = new Log ();
					$log->write ( "sub请求", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
					$this->oauth ( $redirect_uri, $scope );
				}
				else {
					$code = ( string ) $_GET ['code'];
					$wx_open_id = $this->get_oauth_openid ( $code );
					$log = new Log ();
					$log->write ( "sub微信回调", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
					$userid = $user['id'];
					$data = array (
							"openid" => $wx_open_id
						);
					$user_model->where("id=$userid")->save($data);
				}
			}
		}
		$user_id = $user ['id'];
		$car_model = M ( "Car" );
		$where = array (
				'id' => ( string ) $car_id 
		);
		$car = $car_model->where ( $where )->find ();
		$l_num1 = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$l_num2 = mb_substr ( $car ['license_number'], 2, strlen ( $car ['license_number'] ), 'utf-8' );
		$license_number = $l_num1 . "·" . $l_num2;
		$endorsement = $this->get_endorsement ( $car ['license_number']);
		$endorsement_list = $this->get_endorsement_list ( $car_id, $car ['license_number'], $user_id, $end_id );
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'car_id', $car_id );
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'endorsement', $endorsement );
		$this->assign ( 'endorsement_list', $endorsement_list );
		$this->display ( ":scan" );
	}
	
	function get_endorsement($license_number) {
		// 查询数据库违章信息
		$endorsement_model = M ( "Endorsement" );
		$where = array (
				"license_number" => $license_number,
				"is_manage" => 0 
		);
		$endorsement = $endorsement_model->field ( "count(*) as nums, sum(points) as all_points, sum(money) as all_money" )->where ( $where )->find ();
		if ($endorsement ['nums'] != 0) {
			return $endorsement;
		}
		return false;
	}
	
	function get_endorsement_list($car_id, $license_number, $user_id, $end_id = 0) {
		// 查询数据库违章信息列表
		$endorsement_model = M ( "Endorsement" );
		$where = array (
				"license_number" => $license_number,
				"is_manage" => 0 
		);
		if ($end_id != 0) {
			$where ['id'] = $end_id;
		}
		$endorsementlist = $endorsement_model->where ( $where )->order ( "`time` desc" )->select ();
		$log = new Log ();
		foreach ( $endorsementlist as $k => $v ) {
			$fuwu = $this->find_fuwu($car_id, $v["code"], $v["money"],$v["points"], $v["area"]);
			if(!empty($fuwu)){
				$endorsementlist [$k] ['so_id'] = $fuwu["so_id"];
				$endorsementlist [$k] ['so_type'] = $fuwu["so_type"];
				$endorsementlist [$k] ['so_money'] = $fuwu["so_money"];
			}
		}
		return $endorsementlist;
	}
	
	function find_fuwu($car_id, $code, $money, $points, $area, $exclude_list = null){
		$log = new Log ();
		$fuwu = Array();
		$region_model = M ( "Region" );
		$where = array (
				"city" => $area,
				"level" => 2,
				"is_dredge" => 0 
		);
		$region = $region_model->where ( $where )->order ( 'id' )->find ();
		if (empty ( $region )) {
			$city_id1 = 0;
		}
		else{
			$city_id1 = $region ['id'];
		}
		
		$where = array (
				"id" => $car_id
		);
		$car_model = M ( "Car" );
		$car = $car_model->where ( $where )->find ();
		
		$a_class = array("京", "沪", "津", "渝");
		$l_nums = "";
		$l_nums_a = mb_substr ( $car ['license_number'], 0, 1, 'utf-8' );
		if(in_array($l_nums_a, $a_class)){
			$l_nums = $l_nums_a;
		}
		else{
			$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		}
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		$region = $region_model->where ( "city = '{$region['city']}'" )->order ( "id" )->find ();
		if (empty ( $region )) {
			$city_id2 = 0;
		} else {
			$city_id2 = $region ['id'];
		}
		
		$violation_model = M("violation");
		$violation = $violation_model->field("money, points")->where("code = '$code'")->find();
		if(empty($violation) || $violation['state'] == 1){
			return $fuwu;
		}
		
		$where = "";
		if(!empty($exclude_list)){
			$where = "srv.id not in (" . implode(",", $exclude_list) . ") and ";
		}
		$s_code = substr($code, 0, 4);
		
		$so_model = M(''); // 1.a
		$so_sql = "select srv.id as services_id, so.id as so_id, so.money from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_order as so where $where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = so.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and ((scode.code = '$code' or scode.code = '$s_code') and scode.state = 0 ) and so.violation = '$code' and (so.code = $city_id1 or so.code = $city_id2) group by srv.id order by money asc ";
		$log->write ( $so_sql );
		$solist = $so_model->query($so_sql);
		
		$sd_model = M(''); // 1.b
		$sd_sql = "select * from (select dyna.services_id, dyna.id as so_id, ($money + dyna.fee + dyna.point_fee * $points) dyna_fee from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_dyna as dyna where   $where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = dyna.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and (scode.code = '$code' or scode.code = '$s_code') and scode.state = 0 and (dyna.code = $city_id1 or dyna.code = $city_id2) ORDER BY dyna_fee ASC) as service_dyna group by services_id order by dyna_fee asc";
		$log->write ( $sd_sql );
		$sdlist = $sd_model->query($sd_sql);
		
		// we now get the lowest price
		$lowest_price = -1;
		$so_id = -1;
		$so_type = -1;
		if( ! empty($solist)){
			$lowest_price = $solist[0]['money'];
			$so_id = $solist[0]['so_id'];
			$so_type = 1;
		}
		if( ! empty($sdlist)){
			if($lowest_price > -1 ){
				if($lowest_price > $sdlist[0]['dyna_fee']){
					$lowest_price = $sdlist[0]['dyna_fee'];
					$so_id = $sdlist[0]['so_id'];
					$so_type = 2;
				}
			}
			else{
				$lowest_price = $sdlist[0]['dyna_fee'];
				$so_id = $sdlist[0]['so_id'];
				$so_type = 2;
			}
		}
		//$log->write ( "lowest_price=". $lowest_price );
		if($lowest_price == -1){
			return $fuwu;
		}
		
		$where = "";
		$firstCondition = false;
		$services_id_by_money = array ();
		if( ! empty($solist)){
			foreach ( $solist as $p => $c ) {
				if($c['money'] == $lowest_price){
					if ($firstCondition == false) {
						$where .= " services_id = {$c['services_id']}";
						$firstCondition = true;
					} else {
						$where .= " or services_id = {$c['services_id']}";
					}
					$services_id_by_money[] = $c['services_id'];
				}
				else{
					break;
				}
			}
		}
		if( ! empty($sdlist)){
			foreach ( $sdlist as $p => $c ) {
				if($c['dyna_fee'] == $lowest_price){
					if ($firstCondition == false) {
						$where .= " services_id = '{$c['services_id']}'";
						$firstCondition = true;
					} else {
						$where .= " or services_id = '{$c['services_id']}'";
					}
					$services_id_by_money[] = $c['services_id'];
				}
				else{
					break;
				}
			}
		}
		$order_model = M(''); // 2
		$sql = "SELECT COUNT(*) as nums, `services_id` FROM `cw_order` WHERE $where GROUP BY `services_id` ORDER BY nums";
		//$log->write ( $sql);
		$orderlist = $order_model->query ( $sql );
		$services_id_by_ordernum = array ();
		foreach ( $orderlist as $p => $c ) {
			$services_id_by_ordernum [] = $c ['services_id'];
		}
		$services = array_diff ( $services_id_by_money, $services_id_by_ordernum );
		if (! empty ( $services )) {
			foreach ( $services as $r ) {
				$services_id = $r;
				break;
			}
		} else {
			$services_id = $orderlist [0] ['services_id'];
		}
		//$log->write ( "services_id=". $services_id );
		// 3
		$fuwu['s_id'] = $services_id;
		$fuwu['so_id'] = $so_id;
		$fuwu['so_type'] = $so_type;
		$fuwu['so_money'] = $lowest_price;
		
		return $fuwu;
	}
	
	// 详情
	public function scan_info() {
		$user_id = $_REQUEST ['user_id'];
		if(empty($user_id)){
			if (! isset ( $_GET ['code'] )) {
				$redirect_uri = $this->curPageURL();
				$scope = 'snsapi_base';
				$log = new Log ();
				$log->write ( "sub请求", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
				$this->oauth ( $redirect_uri, $scope );
			}
			else {
				$code = ( string ) $_GET ['code'];
				$wx_open_id = $this->get_oauth_openid ( $code );
				$log = new Log ();
				$log->write ( "sub微信回调:" . $wx_open_id, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
				$user_model = M ( "User" );
				$where = array (
						'openid' => ( string ) $wx_open_id 
				);
				$user = $user_model->where ( $where )->find ();
				if(empty($user)){
					exit("can't find user");
				}
				$user_id = $user["id"];
			}
		}
		if(empty($user_id)){
			exit("can't find user");
		}
		$id = $_REQUEST ['id'];
		$car_id = $_REQUEST ['car_id'];
		$license_number = $_REQUEST ['license_number'];
		$so_id = $_REQUEST ['so_id'];
		$so_type = $_REQUEST ['so_type'];
		$state = isset ( $_REQUEST ['state'] ) ? $_REQUEST ['state'] : '';
		
		$endorsement = $this->get_endorsement_info ( $id );
		if($so_type == 1){
			$so = $this->get_so_info ( $so_id);
		}
		if($so_type == 2){
			$so = $this->get_sd_info ($so_id);
			$so['money'] = $endorsement['money'] + $so['fee'] + $so['point_fee'] * $endorsement['points'];
		}
		
		$coupon_money = 0;
		// 创建订单
		if (empty ( $state )) {
			$order_id = $this->create_order ( $car_id, $endorsement, $so, $so_type, $user_id );
		} else {
			$order_id = $_REQUEST ['order_id'];
			$cuc_id = isset ( $_REQUEST ['cuc_id'] ) ? $_REQUEST ['cuc_id'] : 0;
			$coupon = $this->get_coupon_info ( $cuc_id );
			if (! empty ( $coupon )) {
				$coupon_money = $coupon ['money'] > 0 ? $coupon ['money'] : 0;
			}
			$this->assign ( 'coupon', $coupon );
		}
		$order = $this->get_order ( $order_id );
		$ucouponlist = $this->get_ucoupon ( $user_id, $so ['money'] );
		
		$this->assign ( 'endorsement', $endorsement );
		$this->assign ( 'coupon_money', $coupon_money );
		$this->assign ( 'so', $so );
		$this->assign ( 'so_type', $so_type );
		$this->assign ( 'order', $order );
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'ucoupon_count', count ( $ucouponlist ) );
		$this->display ( ":scan_info" );
	}
	function get_endorsement_info($id) {
		// 查询数据库违章信息
		$endorsement_model = M ( "Endorsement" );
		$where = array (
				"id" => $id 
		);
		$endorsement = $endorsement_model->where ( $where )->find ();
		return $endorsement;
	}
	function get_so_info($id) {
		// 定价表信息
		$so_model = M ( "Services_order" );
		$where = array (
				"id" => $id 
		);
		$so = $so_model->where ( $where )->find ();
		return $so;
	}
	function get_sd_info($id) {
		// 定价表信息
		$sd_model = M ( "Services_dyna" );
		$where = array (
				"id" => $id 
		);
		$sd = $sd_model->where ( $where )->find ();
		return $sd;
	}
	
	function create_order($car_id, $endorsement, $so, $so_type, $user_id) {
		$order_model = M ( "Order" );
		$order = $order_model->where ( "endorsement_id = '{$endorsement ['id']}' and order_status = 1" )->find ();
		if (! empty ( $order )) {
			// re-generate order
			// there are maybe another servie provider with a lower price after the order created
			$data = array (
				"money" => $so ['money'],
				"last_time" => time (),
				"services_id" => $so ['services_id'],
				"so_id" => $so ['id'],
				"so_type" => $so_type
			);
			$order_model->where("id = {$order['id']}" )->save ( $data );
			return $order ['id'];
		}
		$order = $order_model->where ( "endorsement_id = '{$endorsement ['id']}' and (order_status = 2 or order_status = 3 or order_status = 4 or order_status = 5)" )->find ();
		if (! empty ( $order )) {
			return 0;
		}
		$data = array (
				"user_id" => $user_id,
				"car_id" => $car_id,
				"order_sn" => $user_id . $car_id . time (),
				"endorsement_id" => $endorsement ['id'],
				"order_status" => 1,
				"money" => $so ['money'],
				"last_time" => time (),
				"services_id" => $so ['services_id'],
				"so_id" => $so ['id'],
				"so_type" => $so_type,
				"c_time" => time () 
		);
		return $order_model->add ( $data );
	}
	function get_order($id) {
		// 订单表信息
		$order_model = M ( "Order" );
		$where = array (
				"id" => $id 
		);
		$order = $order_model->where ( $where )->find ();
		return $order;
	}
	function get_ucoupon($user_id, $money) {
		// 用户拥有优惠券信息
		$ucoupon_model = M ( "User_coupon" );
		$where = "cw_user_coupon.user_id='$user_id'";
		$where .= " and cw_user_coupon.is_used = 0";
		$where .= " and cw_coupon.start_time <= unix_timestamp(now()) and cw_coupon.expiration_time >= unix_timestamp(now())";
		$where .= " and cw_coupon.condition <= '$money'";
		$ucouponlist = $ucoupon_model->field ( "cw_coupon.*, cw_user_coupon.id as cuc_id" )->join ( "cw_coupon on cw_coupon.id = cw_user_coupon.coupon_id" )->where ( $where )->select ();
		return $ucouponlist;
	}
	function get_coupon_info($cuc_id) {
		$ucoupon_model = M ( "User_coupon" );
		$where = "cw_user_coupon.id='$cuc_id'";
		$ucoupon = $ucoupon_model->field ( "cw_coupon.*, cw_user_coupon.id as cuc_id" )->join ( "cw_coupon on cw_coupon.id = cw_user_coupon.coupon_id" )->where ( $where )->find ();
		return $ucoupon;
	}
	public function ucoupon_list() {
		$id = $_REQUEST ['id'];
		$car_id = $_REQUEST ['car_id'];
		$license_number = $_REQUEST ['license_number'];
		$so_id = $_REQUEST ['so_id'];
		$so_type = $_REQUEST ['so_type'];
		$user_id = $_REQUEST ['user_id'];
		$order_id = $_REQUEST ['order_id'];
		$money = $_REQUEST ['money'];
		
		$uclist = $this->get_ucoupon ( $user_id, $money );
		$this->assign ( 'id', $id );
		$this->assign ( 'car_id', $car_id );
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'so_id', $so_id );
		$this->assign ( 'so_type', $so_type );
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'order_id', $order_id );
		$this->assign ( 'uuc_list', $uclist );
		$this->display ( ":ucoupon_list" );
	}
	public function wxpay() {
		$order_id = $_REQUEST ['order_id'];
		$cuc_id = $_REQUEST ['cuc_id'];
		$order_model = M ( "Order" );
		$data = array ();
		$order = $this->get_order ( $order_id );
		if ($order ['order_status'] != 1) {
			$url ['url'] = 1;
			$this->ajaxReturn ( $url );
		}
		if (! empty ( $cuc_id )) {
			$ucoupon = $this->get_coupon_info ( $cuc_id );
			if (! empty ( $ucoupon )) {
				$data ["ucoupon_id"] = $cuc_id;
				$data ["pay_money"] = $order ['money'] - $ucoupon ['money'];
			} else {
				$data ["pay_money"] = $order ['money'];
			}
		} else {
			$data ["pay_money"] = $order ['money'];
		}
		// re-generate order-sn
		// for the same order, the money MUST be the same in wxpay side
		// so if user apply an coupon to the order or choose another service provider with an lower price after an un-finish pay action, user will get an error with the un-changed order-sn when user try to pay again
		$data["order_sn"] = $order['user_id'] . $order['car_id'] . time ();
		$order_model->where ( "id='$order_id'" )->save ( $data );
		$order = $this->get_order ( $order_id );
		$data ["pay_money"]=intval($data ["pay_money"]*100);
		if(runEnv == 'production'){
			$url ['url'] = APIURL . "Wxpay/example/jsapi.php??money={$data['pay_money']}&order_sn={$order ['order_sn']}&body=违章缴费&url=" . APIURL;
		}
		else{
			$url ['url'] = "http://weixin.xiaoxianlink.com/Wxpay/example/jsapi.php??money=1&order_sn={$order ['order_sn']}&body=违章缴费&url=" . APIURL;
		}
		$log = new Log ();
		$log->write ( $url ['url'] );
		
		$this->ajaxReturn ( $url );
	}
	public function notify() {
		$order_sn = $_REQUEST ['out_trade_no'];
		$log = new Log ();
		$log->write ( json_encode ( $_REQUEST ) );
		$order_model = M ( "Order" );
		$order = $order_model->where ( "order_sn='$order_sn'" )->find ();
		if($order ['order_status'] != 1){
		    return true;
		}
		// 修改订单状态
		$data = array (
				"order_status" => 2,
				"last_time" => time (),
				"pay_type" => 2,
				"pay_sn" => $_REQUEST ['transaction_id'] 
		);
		
		$order_model->where ( "order_sn='$order_sn'" )->save ( $data );
		$order = $order_model->where ( "order_sn='$order_sn'" )->find ();
		$data = array (
				"order_id" => $order ['id'],
				"services_id" => $order ['services_id'],
				"sod_id" => $order ['so_id'],
				"so_type" => $order ['so_type'],
				"money" => $order ['money'],
				"state" => '0',
				"c_time" => time (),
				"l_time" => time () 
		);
		$to_model = M ( "Turn_order" );
		$to_model->add ( $data );
		
		// 修改违章状态
		$data = array (
				"is_manage" => 1,
				"manage_time" => time () 
		);
		$endorsement_model = M ( "Endorsement" );
		$endorsement_model->where ( "id={$order['endorsement_id']}" )->save ( $data );
		$log_model = M ( "Endorsement_log" );
		$data = array (
				"end_id" => $order['endorsement_id'],
				"state" => 2,
				"c_time" => time (),
				"type" => 1
		);
		$log_model->add ( $data );
		// 使用优惠券
		if ($order ['ucoupon_id'] > 0) {
			$data = array (
					"is_used" => 1,
					"use_time" => time () 
			);
			$uc_model = M ( "User_coupon" );
			$uc_model->where ( "id={$order ['ucoupon_id']}" )->save ( $data );
		}
		
		$services_model = M ( "services" );
		$services_info = $services_model->where ( "id='{$order['services_id']}'" )->find ();
		if (! empty ( $services_info )) {
			$data = array (
					"all_nums" => $services_info ['all_nums'] + 1 
			);
			$services_model->where ( "id='{$order['services_id']}'" )->save ($data);
		}
		
		/*
		// 服务商收入
		$s_model = M ( "bank" );
		$s_info = $s_model->where ( "bank_id = '{$order['services_id']}'" )->find ();
		if (! empty ( $s_info )) {
			$data = array (
					"money" => $s_info ['money'] + $order ['money'],
					"end_money" => $s_info ['end_money'] + $order ['money']
			);
			$re = $s_model->where ( "id='{$s_info['id']}'" )->save ( $data );
		} else {
			$data = array (
					"bank_id" => $order ['services_id'],
					"name" => 0,
					"user_bank" => 0,
					"user_number" => 0,
					"money" => $order ['money'],
					"end_money" => $order ['money'],
					"user_money" => 0,
					"create_time" => time ()
			);
			$s_model->add ( $data );
		}
		*/
		/* start 增加新订单提醒推送*/
		$turn_order_model = M ('turn_order');
		$t_info = $turn_order_model->field('tos.c_time')->table ( "cw_turn_order as tos" )->join ( "cw_order as o on o.id=tos.order_id", 'left' )->join ( "cw_services as s on s.id=o.services_id", 'left' )->where ( "o.order_sn='$order_sn'" )->find ();
		$order_sn_to = $order_sn . substr ( $t_info ['c_time'], - 2 ) . $order ['services_id'];
		$content = sprintf(content4, $order_sn_to);
		$title = title4;
		$tz_content = sprintf(content4, $order_sn_to);
		$model_mes = M ("message");
		$info = $model_mes->where("content = '$content'")->find();
		if(empty($info)){
		    $this->pushMessageToSingle($content, $title,$tz_content,$services_info['phone']);
		    //插入消息表
		    $this->add_message($services_info['id'], 3, 4, '', $content);
		}
		/* end */
		
		/*
		// 记录
		$data = array (
				"services_id" => $s_info ['bank_id'],
				"income_money" => $order ['money'],
				"pay_money" => 0,
				"end_money" => $s_info ['end_money'] + $order ['money'],
				"user_money" => $s_info ['user_money'],
				"money" => $s_info ['money'] + $order ['money'],
				"order_id" => $order ['id'],
				"c_time" => time () 
		);
		$jl_model = M ( "services_jilu" );
		$jl_model->add ( $data );
		*/
		return true;
	}
	
	public function success_pay() {
		$order_sn = $_REQUEST ['order_sn'];
		$order_model = M ( "Order" );
		$order = $order_model->field ( "cw_order.id,u.openid, cw_order.car_id, cw_order.services_id, cw_order.money" )->join ( "cw_user as u on u.id = cw_order.user_id" )->where ( "cw_order.order_sn='$order_sn'" )->find ();
		
		$car_model = M ( "Car" );
		$car = $car_model->field ( "license_number" )->where ( "id = '{$order['car_id']}'" )->find ();
		$this->assign ( "license_number", $car ['license_number'] );
		$this->assign ( "order", $order );
		$this->display ( ":success_pay" );
	}
}