<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;
use Weixin\Controller\IndexController;
use Think\Log;

include_once 'application/Weixin/Conf/config.php';
class JiaoyiController extends AdminbaseController {
	protected $jiaoyi_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['fuwu'] = '';
		$_SESSION ['xitong'] = '';
		$this->jiaoyi_model = D ( "Common/coupon" );
	}
	function dingdan() {
		$type = $_POST ['che_type'];
		$time_start = strtotime ( $_POST ['che_time_start'] );
		$time_end = strtotime ( $_POST ['che_time_end'] );
		$number = $_POST ['che_number'];
		$water = $_POST ['che_water'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['che_type1'] = $type;
			$_SESSION ['jiao'] ['che_time_start1'] = $time_start;
			$_SESSION ['jiao'] ['che_time_end1'] = $time_end;
			$_SESSION ['jiao'] ['che_number1'] = $number;
			$_SESSION ['jiao'] ['che_water1'] = $water;
		} else {
			$type = $_SESSION ['jiao'] ['che_type1'];
			$time_start = $_SESSION ['jiao'] ['che_time_start1'];
			$time_end = $_SESSION ['jiao'] ['che_time_end1'];
			$number = $_SESSION ['jiao'] ['che_number1'];
			$water = $_SESSION ['jiao'] ['che_water1'];
		}
		$this->assign ( "time_start", (empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start )) );
		$this->assign ( "time_end", (empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end )) );
		$this->assign ( "number", $number );
		$this->assign ( "type", $type );
		$this->assign ( "water", $water );
		$where = "1=1";
		if (! empty ( $type )) {
			$where .= " and order_status=$type";
		} else {
			$where .= " and (order_status != 5 and order_status != 7 and order_status != 8)";
		}
		if (! empty ( $number )) {
			$where .= " and license_number like '%$number%'";
		}
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (a.last_time between $time_start and $time_end)";
		}
		if (! empty ( $water )) {
			$where .= " and a.pay_sn like '%$water%'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.last_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,a.id as order_id,a.pay_sn,a.order_sn,b.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.order_status,a.pay_type,d.id as user_id,d.phone" )->table ( "(SELECT @rownum:=0) r,cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->examine_order (); // 查询 订单是否超时
		foreach ( $roles as $k => $v ) {
			$true_order_model = M ();
			$to_list = $true_order_model->field ( "tos.id,so.id as so_id,s.id as s_id,s.phone,tos.c_time,tos.state,tos.l_time,s.services_sn" )->table ( "cw_turn_order as tos" )->join ( "cw_services_order as so on so.id=tos.sod_id", 'left' )->join ( "cw_services as s on s.id=so.services_id", 'left' )->where ( "tos.order_id = '{$v['order_id']}'" )->select ();
			foreach ( $to_list as $c => $p ) { // 推单状态处理
				$to_list [$c] ['so_id'] = $v ['order_sn'] . substr ( $p ['c_time'], - 2 ) . $p ['s_id'];
				$time = '--';
				if ($p ['state'] == 0) {
					$time = jishi1 + $p ['l_time'];
				} else if ($p ['state'] == 3) {
					$time = jishi2 + $p ['l_time'];
				}
				$to_list [$c] ['tmr'] = $time;
			}
			$roles [$k] ['to_list'] = $to_list;
		}
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function time_his($time) {
		$hour = $time / 3600;
		return 1;
	}
	// 检查点单接口
	public function examine_order() {
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,a.id as order_id,a.pay_sn,a.order_sn,b.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.order_status,a.pay_type,d.id as user_id,d.phone,a.services_id,a.money as order_money,a.endorsement_id" )->table ( "(SELECT @rownum:=0) r,cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "a.order_status = 1 or a.order_status = 2 or a.order_status = 3" )->select ();
		foreach ( $roles as $k => $v ) {
			$city = $v ['area'];
			$region_model = M ( "Region" );
			$where = array (
					"city" => $city,
					"level" => 2,
					"is_dredge" => 0 
			);
			$region = $region_model->where ( $where )->order ( 'id' )->find ();
			$city_id1 = $region ['id'];
			
			$where = array (
					"id" => $v ['car_id'] 
			);
			$car_model = M ( "Car" );
			$car = $car_model->where ( $where )->find ();
			$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
			$region_model = M ( "Region" );
			$region = $region_model->where ( "nums = '$l_nums'" )->find ();
			$region = $region_model->where ( "city = '{$region['city']}'" )->order ( "id" )->find ();
			if (empty ( $region )) {
				$city_id2 = 0;
			} else {
				$city_id2 = $region ['id'];
			}
			
			$true_order_model = M ( "turn_order" );
			$to_list = $true_order_model->field ( "tos.id,so.id as so_id,s.id as s_id,s.phone,tos.c_time,tos.state,tos.l_time" )->table ( "cw_turn_order as tos" )->join ( "cw_services_order as so on so.id=tos.sod_id", 'left' )->join ( "cw_services as s on s.id=so.services_id", 'left' )->where ( "tos.order_id = '{$v['order_id']}'" )->select ();
			$s_ids = "0";
			foreach ( $to_list as $c => $p ) {
				if ($p ['s_id'] != '' && $p ['s_id'] != null) {
					$s_ids .= ",{$p['s_id']}";
				}
			}
			$order_model = M ( "Order" );
			$so_model = M ( "Services_order" );
			$solist = $so_model->where ( "violation = '{$v['code']}' and services_id not in ($s_ids) and (code = '$city_id1' or code = '$city_id2')" )->order ( "money asc" )->group ( "services_id" )->find ();
			foreach ( $to_list as $c => $p ) { // 推单状态处理
				if ($p ['state'] == 0) {
					$time = jishi1 - (time () - $p ['l_time']);
					if ($time <= 0) { // 超时
						if (! empty ( $solist )) { // 转单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_id" => $v ['order_id'],
									"sod_id" => $solist ['id'],
									"state" => 0,
									"c_time" => time (),
									"l_time" => time () 
							);
							$to_model = M ( "Turn_order" );
							$to_model->add ( $data );
							$data = array (
									"services_id" => $solist ['services_id'],
									"so_id" => $solist ['id'] 
							);
							$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
							
							$services_model = M ( "services" );
							$services_info = $services_model->where ( "id='{$solist['services_id']}'" )->find ();
							if (! empty ( $services_info )) {
								$data = array (
										"all_nums" => $services_info ['all_nums'] + 1 
								);
								$services_model->where ( "id='{$solist['services_id']}'" )->save ();
							}
							// 转钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $v ['order_money']) > 0 ? ($bank_info_older ['money'] - $v ['order_money']) : 0,
										"balance_money" => ($bank_info_older ['balance_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['balance_money'] - $v ['order_money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['end_money'] - $v ['order_money']) : 0,
										"income_money" => ($bank_info_older ['income_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['income_money'] - $v ['order_money']) : 0 
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => $bank_info_older ['income_money'],
									"pay_money" => $bank_info_older ['pay_money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
							
							$bank_info = $bank_model->where ( "bank_id='{$solist['services_id']}'" )->find ();
							if (! empty ( $bank_info )) {
								$data = array (
										"money" => $bank_info ['money'] + $v ['order_money'],
										"balance_money" => $bank_info ['balance_money'] + $v ['order_money'],
										"end_money" => $bank_info ['end_money'] + $v ['order_money'],
										"income_money" => $bank_info ['income_money'] + $v ['order_money'] 
								);
								$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info = $bank_model->where ( "bank_id='{$solist['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info ['bank_id'],
									"income_money" => $bank_info ['income_money'],
									"pay_money" => $bank_info ['pay_money'],
									"end_money" => $bank_info ['end_money'],
									"user_money" => $bank_info ['user_money'],
									"money" => $bank_info ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
						} else { // 取消订单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_status" => 8 
							);
							$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
							$data = array (
									"state" => 6
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							// 修改违章状态
							$data = array (
									"is_manage" => 0,
									"manage_time" => time () 
							);
							$endorsement_model = M ( "Endorsement" );
							$endorsement_model->where ( "id={$v['endorsement_id']}" )->save ( $data );
							//扣钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $v ['order_money']) > 0 ? ($bank_info_older ['money'] - $v ['order_money']) : 0,
										"balance_money" => ($bank_info_older ['balance_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['balance_money'] - $v ['order_money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['end_money'] - $v ['order_money']) : 0,
										"income_money" => ($bank_info_older ['income_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['income_money'] - $v ['order_money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => $bank_info_older ['income_money'],
									"pay_money" => $bank_info_older ['pay_money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
						}
					}
				} else if ($p ['state'] == 3) {
					$time = jishi2 - (time () - $p ['l_time']);
					if ($time <= 0) { // 超时
						if (! empty ( $solist )) { // 转单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_id" => $v ['order_id'],
									"sod_id" => $solist ['id'],
									"state" => 0,
									"c_time" => time (),
									"l_time" => time () 
							);
							$to_model = M ( "Turn_order" );
							$to_model->add ( $data );
							$data = array (
									"services_id" => $solist ['services_id'],
									"so_id" => $solist ['id'] 
							);
							$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
							
							$services_model = M ( "services" );
							$services_info = $services_model->where ( "id='{$solist['services_id']}'" )->find ();
							if (! empty ( $services_info )) {
								$data = array (
										"all_nums" => $services_info ['all_nums'] + 1 
								);
								$services_model->where ( "id='{$solist['services_id']}'" )->save ();
							}
							// 转钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $v ['order_money']) > 0 ? ($bank_info_older ['money'] - $v ['order_money']) : 0,
										"balance_money" => ($bank_info_older ['balance_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['balance_money'] - $v ['order_money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['end_money'] - $v ['order_money']) : 0,
										"income_money" => ($bank_info_older ['income_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['income_money'] - $v ['order_money']) : 0 
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => $bank_info_older ['income_money'],
									"pay_money" => $bank_info_older ['pay_money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
							
							$bank_info = $bank_model->where ( "bank_id='{$solist['services_id']}'" )->find ();
							if (! empty ( $bank_info )) {
								$data = array (
										"money" => $bank_info ['money'] + $v ['order_money'],
										"balance_money" => $bank_info ['balance_money'] + $v ['order_money'],
										"end_money" => $bank_info ['end_money'] + $v ['order_money'],
										"income_money" => $bank_info ['income_money'] + $v ['order_money'] 
								);
								$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info = $bank_model->where ( "bank_id='{$solist['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info ['bank_id'],
									"income_money" => $bank_info ['income_money'],
									"pay_money" => $bank_info ['pay_money'],
									"end_money" => $bank_info ['end_money'],
									"user_money" => $bank_info ['user_money'],
									"money" => $bank_info ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
						} else { // 取消订单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_status" => 8 
							);
							$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
							$data = array (
									"state" => 6
							);
							$true_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							// 修改违章状态
							$data = array (
									"is_manage" => 0,
									"manage_time" => time () 
							);
							$endorsement_model = M ( "Endorsement" );
							$endorsement_model->where ( "id={$v['endorsement_id']}" )->save ( $data );
							//扣钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $v ['order_money']) > 0 ? ($bank_info_older ['money'] - $v ['order_money']) : 0,
										"balance_money" => ($bank_info_older ['balance_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['balance_money'] - $v ['order_money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['end_money'] - $v ['order_money']) : 0,
										"income_money" => ($bank_info_older ['income_money'] - $v ['order_money']) > 0 ? ($bank_info_older ['income_money'] - $v ['order_money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => $bank_info_older ['income_money'],
									"pay_money" => $bank_info_older ['pay_money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
						}
					}
				}
			}
		}
	}
	function fuwu() {
		$name = $_POST ['fu_name'];
		$number = $_POST ['fu_number'];
		$phone = $_POST ['fu_phone'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_name2'] = $name;
			$_SESSION ['jiao'] ['fu_number2'] = $number;
			$_SESSION ['jiao'] ['fu_phone2'] = $phone;
		} else {
			$name = $_SESSION ['jiao'] ['fu_name2'];
			$number = $_SESSION ['jiao'] ['fu_number2'];
			$phone = $_SESSION ['jiao'] ['fu_phone2'];
		}
		$this->assign ( "array_post", array (
				$name,
				$number,
				$phone 
		) );
		$where = "1=1";
		if (! empty ( $name )) {
			$where .= " and b.name like '%$name%'";
		}
		if (! empty ( $phone )) {
			$where .= " and a.phone like '%$phone%'";
		}
		if (! empty ( $number )) {
			$where .= " and a.services_sn like '%$number%'";
		}
		$state = $_GET ['state'];
		if (! empty ( $state )) {
			$where = " a.state='$state'-1";
			$this->assign ( "vstate", $state );
		}
		$count = $this->jiaoyi_model->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.phone,b.name,b.user_bank,b.user_number,b.user_money,b.end_money,b.money,a.state,b.bank_id,a.services_sn,b.type" )->table ( "(SELECT @rownum:=0) r,cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_state = $this->jiaoyi_model->field ( "count(a.state) as sum" )->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->group ( "state" )->select ();
		$num_0 = $roles_state [0] [sum];
		$num_1 = $roles_state [1] [sum];
		$num_2 = $num_0 + $num_1;
		$this->assign ( "state", array (
				$num_0,
				$num_1,
				$num_2 
		) );
		$roles_money = $this->jiaoyi_model->field ( "sum( b.money) as money,sum(b.user_money) as user_money,sum(end_money) as end_money" )->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->select ();
		$this->assign ( "money", $roles_money );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function fuwu_div() {
		$user_id = $_REQUEST ['id'];
		$time_long = $_REQUEST ['time'];
		$services_sn = $_REQUEST ['services_sn'];
		$where = "sjl.services_id = '$user_id'";
		$str = '';
		$date = $this->jiaoyi_model->field ( "sjl.*,o.order_sn" )->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->order ( "sjl.c_time desc limit 20" )->select ();
		foreach ( $date as $k => $v ) {
			$str .= "<tr>
                            <td>" . date ( "Y/m/d H:i:s", $v ['c_time'] ) . "</td>
                            <td>$v[income_money]</td>
                            <td>$v[pay_money]</td>
                            <td>$v[end_money]</td>
                            <td>$v[user_money]</td>
                            <td>$v[money]</td>
                            <td>$v[order_sn]</td>
                        </tr>";
		}
		$info = $this->jiaoyi_model->field ( "s.services_sn,s.phone,b.money,b.user_money" )->table ( "cw_services as s" )->join ( "cw_bank as b on b.bank_id = s.id" )->where ( "s.services_sn = '{$services_sn}'" )->find ();
		$astr = '<div class="count_txt" >供应商编号 &nbsp;&nbsp;' . $services_sn . ' </div><div class="count_txt">手机号&nbsp;&nbsp;' . $info ['phone'] . ' </div><div class="count_txt">可提现金额&nbsp;&nbsp; ' . $info ['user_money'] . ' </div><div class="count_txt"> 账户余额&nbsp;&nbsp;' . $info['money'] . '</div>';
		$data = array (
				0 => $str,
				1 => $astr,
				2 => $user_id 
		);
		$this->ajaxReturn ( $data );
	}
	function fuwu_div2() {
		$user_id = $_REQUEST ['id'];
		$time_long = $_REQUEST ['time'];
		$time_start = strtotime ( $_REQUEST ['time_start'] );
		$time_end = strtotime ( $_REQUEST ['time_end'] );
		$type = $_REQUEST ['type'];
		$state = $_REQUEST ['state'];
		$where = "sjl.services_id = '$user_id'";
		if (! empty ( $type )) {
			$where .= " and o.pay_type = $type";
		}
		if (! empty ( $time_start )) {
			$where .= "  and sjl.c_time>$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= "  and sjl.c_time<$time_end ";
		}
		switch ($state) {
			case 1 :
				$where .= " and to_days(FROM_UNIXTIME( sjl.c_time, '%Y%m%d' )) = to_days(now())";
				break;
			case 2 :
				$where .= " and to_days(now()) - to_days( FROM_UNIXTIME(sjl.c_time, '%Y%m%d') ) <= 1";
				break;
			case 3 :
				$where .= " and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(FROM_UNIXTIME( sjl.c_time, '%Y%m%d' ))";
				break;
			default :
				;
				break;
		}
		$str = '<tr><th>账户变动时间</th><th>收入金额</th><th>支出金额</th><th>未结算金额</th><th>可提现金额</th><th>账户余额</th><th>订单号</th><tr/>';
		$date = $this->jiaoyi_model->field ( "sjl.*,o.order_sn" )->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->order ( "sjl.c_time desc limit 20" )->select ();
		foreach ( $date as $k => $v ) {
			$str .= "<tr>
                            <td>" . date ( "Y/m/d H:i:s", $v ['c_time'] ) . "</td>
                            <td>$v[income_money]</td>
                            <td>$v[pay_money]</td>
                            <td>$v[end_money]</td>
                            <td>$v[user_money]</td>
                            <td>$v[money]</td>
                            <td>$v[order_sn]</td>
                        </tr>";
		}
		$astr = '<div class="count_txt" >供应商编号 &nbsp;&nbsp;' . $date [0] [id] . ' </div><div class="count_txt">手机号&nbsp;&nbsp;' . $date [0] [phone] . ' </div><div class="count_txt">可提现金额&nbsp;&nbsp; ' . $date [0] [user_money] . ' </div><div class="count_txt"> 账户余额&nbsp;&nbsp;' . $date [0] [balance_money] . '</div>';
		$data = array (
				0 => $str,
				1 => $astr,
				2 => $user_id 
		);
		$this->ajaxReturn ( $data );
	}
	function fuwu_update() {
		$id = $_GET ['id'];
		$state = $_GET ['state'];
		if ($state == 0) {
			$time = strtotime ( date ( "Y-m-d H:i:s" ) );
			if ($this->jiaoyi_model->execute ( "update cw_services set state=1,time=$time where id=$id" ) > 0) {
				$this->success ( "封存成功！" );
			} else {
				$this->error ( "封存失败！" );
			}
		} elseif ($state == 1) {
			if ($this->jiaoyi_model->execute ( "update cw_services set state=0  where id=$id" ) > 0) {
				$this->success ( "启封成功！" );
			} else {
				$this->error ( "启封失败！" );
			}
		}
	}
	function history() {
		$code = $_POST ['fu_code'];
		$time_start = strtotime ( $_POST ['fu_time_start'] );
		$time_end = strtotime ( $_POST ['fu_time_end'] );
		$number = $_POST ['fu_number'];
		$numbers = $_POST ['fu_numbers'];
		$order_status = $_GET ['order_status'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_code4'] = $code;
			$_SESSION ['jiao'] ['fu_time_start4'] = $time_start;
			$_SESSION ['jiao'] ['fu_time_end4'] = $time_end;
			$_SESSION ['jiao'] ['fu_number4'] = $number;
			$_SESSION ['jiao'] ['fu_numbers4'] = $numbers;
		} else {
			$code = $_SESSION ['jiao'] ['fu_code4'];
			$time_start = $_SESSION ['jiao'] ['fu_time_start4'];
			$time_end = $_SESSION ['jiao'] ['fu_time_end4'];
			$number = $_SESSION ['jiao'] ['fu_number4'];
			$numbers = $_SESSION ['jiao'] ['fu_numbers4'];
		}
		$where = "(order_status = 5 or order_status = 7 or order_status = 8)";
		if (! empty ( $order_status )) {
			$where = " order_status=$order_status";
		}
		if (! empty ( $code )) {
			$where .= " and b.license_number like '%$code%'";
		}
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (a.last_time between $time_start and $time_end)";
		}
		if (! empty ( $number )) {
			$where .= " and d.services_sn like '%$number%' ";
		}
		if (! empty ( $numbers )) {
			$where .= " and order_sn like '%$numbers%'";
		}
		$this->assign ( "array_post", array (
				$code,
				$time_start = empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start ),
				$time_end = empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end ),
				$number,
				$numbers 
		) );
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.last_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,a.id as order_id,a.pay_sn,a.order_sn,b.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.money as end_money,a.order_status,a.pay_type,d.id as user_id,d.phone,d.services_sn,a.so_id,a.c_time as o_time" )->table ( "(SELECT @rownum:=0) r,cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$true_order_model = M ();
			$to_list = $true_order_model->field ( "tos.id,so.id as so_id,s.id as s_id,s.phone,tos.c_time,tos.state,tos.l_time,s.services_sn" )->table ( "cw_turn_order as tos" )->join ( "cw_services_order as so on so.id=tos.sod_id", 'left' )->join ( "cw_services as s on s.id=so.services_id", 'left' )->where ( "tos.order_id = '{$v['order_id']}' and tos.sod_id = '{$v['so_id']}'" )->find ();
			$roles [$k] ['so_id'] = $v ['order_sn'] . substr ( $to_list ['c_time'], - 2 ) . $v ['user_id'];
		}
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_finish = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status=5" )->select ();
		$roles_undo = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status=7" )->select ();
		$roles_counts = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status = 5 or order_status = 7" )->select ();
		$this->assign ( "order_status", array (
				$roles_counts [0] [counts],
				$roles_finish [0] [counts],
				$roles_undo [0] [counts] 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function tixian() {
		$time_start = strtotime ( $_POST ['fu_time_start'] );
		$time_end = strtotime ( $_POST ['fu_time_end'] );
		$type = $_POST ['fu_type'];
		$state = $_GET ['state'];
		$long_time = $_GET ['time'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_time_start3'] = $time_start;
			$_SESSION ['jiao'] ['fu_time_end3'] = $time_end;
			$_SESSION ['jiao'] ['fu_type3'] = $type;
		} else {
			$time_start = $_SESSION ['jiao'] ['fu_time_start3'];
			$time_end = $_SESSION ['jiao'] ['fu_time_end3'];
			$type = $_SESSION ['jiao'] ['fu_type3'];
		}
		$this->assign ( "type", $type );
		$this->assign ( "time_start", $time_start );
		$this->assign ( "time_end", $time_end );
		$where = "1=1";
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (c.please_time between '$time_start' and '$time_end')";
		}
		if (! empty ( $type )) {
			$where .= " and c.bank_state='$type'";
		}
		if (! empty ( $state )) {
			$where = " bank_state='$state'";
		}
		switch ($long_time) {
			case 1 :
				$where .= " and to_days(FROM_UNIXTIME(now())) - to_days( FROM_UNIXTIME( c.please_time, '%Y%m%d' ) ) <= 1";
				break;
			case 2 :
				$where .= " and to_days(FROM_UNIXTIME( c.please_time, '%Y%m%d' )) = to_days(now())";
				break;
			
			case 8 :
				$where .= " and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(FROM_UNIXTIME( c.please_time, '%Y%m%d' ))";
				break;
			default :
				;
				break;
		}
		$this->assign ( "time", $long_time );
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "c.please_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,c.id as ex_id,a.id,c.please_money,c.please_time,c.operate,c.dispose_user,c.dispose_time,a.phone,c.type,c.bank_state,b.name,b.user_bank,b.user_number,b.user_money,b.end_money,b.money,a.services_sn,c.expend_sn" )->table ( "(SELECT @rownum:=0) r,cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_state = $this->jiaoyi_model->field ( "count(bank_state) as num" )->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->group ( "bank_state" )->select ();
		$state_1 = $roles_state [0] [num];
		$state_2 = $roles_state [1] [num];
		$state_3 = $state_1 + $state_2;
		$this->assign ( "array_state", array (
				$state_1,
				$state_2,
				$state_3 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function youhui() {
		$name = $_POST ['you_name'];
		$valid = $_POST ['you_valid'];
		$use = $_POST ['you_use'];
		$state = $_POST ['you_state'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['you_name5'] = $name;
			$_SESSION ['jiao'] ['you_valid5'] = $valid;
			$_SESSION ['jiao'] ['you_use5'] = $use;
			$_SESSION ['jiao'] ['you_state5'] = $state;
		} else {
			$name = $_SESSION ['jiao'] ['you_name5'];
			$valid = $_SESSION ['jiao'] ['you_valid5'];
			$use = $_SESSION ['jiao'] ['you_use5'];
			$state = $_SESSION ['jiao'] ['you_state5'];
		}
		$this->assign ( "name", $name );
		$this->assign ( "valid", $valid );
		$this->assign ( "use", $use );
		$this->assign ( "state", $state );
		$where = "1=1";
		if (! empty ( $name )) {
			$where .= " and a.name like '%$name%'";
		}
		if (! empty ( $use )) {
			$where .= " and is_used=$use-1";
		}
		if (! empty ( $state )) {
			$where .= " and state =$state";
		}
		$count = $this->jiaoyi_model->table ( "cw_user_coupon as b" )->join ( "cw_coupon as a on b.coupon_id=a.id" )->join ( "cw_user as c on b.user_id=c.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "@rownum:=@rownum+1 AS iid,a.state,a.id,a.name,a.condition,a.money,a.start_time,a.expiration_time,c.username,b.is_used,b.use_time,b.card" )->table ( "(SELECT @rownum:=0) r,cw_user_coupon as b" )->join ( "cw_coupon as a on b.coupon_id=a.id" )->join ( "cw_user as c on b.user_id=c.id" )->where ( $where )->order ( "b.id desc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function dispose() {
		$transfer_sn = isset ( $_REQUEST ['transfer_sn'] ) ? $_REQUEST ['transfer_sn'] : '';
		$pay_type = isset ( $_REQUEST ['pay_type'] ) ? $_REQUEST ['pay_type'] : '';
		$remark = isset ( $_REQUEST ['remark'] ) ? $_REQUEST ['remark'] : '';
		$id = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : '';
		$model = M ( "expend" );
		$data = array (
				"transfer_sn" => $transfer_sn,
				"pay_type" => $pay_type,
				"remark" => $remark,
				"bank_state" => 2 
		);
		$model->where ( "id='$id'" )->save ( $data );
		$this->success ();
	}
	function youhui_add() {
		$username = $_POST ['username'];
		$start_time = strtotime ( $_POST ['start_time'] );
		$expiration_time = strtotime ( $_POST ['expiration_time'] );
		$condition = $_POST ['condition'];
		$money = $_POST ['money'];
		$user = $_POST ['user_id'];
		$state = $_POST ['state'];
		$time = time ();
		$date = array (
				'name' => $username,
				'condition' => $condition,
				'money' => $money,
				'start_time' => $start_time,
				'expiration_time' => $expiration_time,
				'create_time' => $time,
				'state' => $state 
		);
		$model = M ( "coupon" );
		$model->add ( $date );
		$lastid = $model->getLastInsID ();
		if (! empty ( $money )) {
			$user_id = explode ( ',', $user );
			foreach ( $user_id as $k => $v ) {
				$model = M ( "user_coupon" );
				$date = array (
						'user_id' => $v,
						'coupon_id' => $lastid,
						'create_time' => $time,
						"card" => $v . time (),
						"is_used" => 0 
				);
				$roles = $model->add ( $date );
			}
			if ($roles > 0) {
				$this->success ( "添加成功" );
			}
		}
	}
	function zhuandan() {
		$_order = $_REQUEST ['order'];
		$td_number = $_REQUEST ['td_number'];
		$fu_number = $_REQUEST ['fu_number'];
		$order_sn = $_REQUEST ['order_sn'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['td_number6'] = $td_number;
			$_SESSION ['jiao'] ['fu_number6'] = $fu_number;
			$_SESSION ['jiao'] ['order_sn6'] = $order_sn;
		} else {
			$td_number = $_SESSION ['jiao'] ['td_number6'];
			$fu_number = $_SESSION ['jiao'] ['fu_number6'];
			$order_sn = $_SESSION ['jiao'] ['order_sn6'];
		}
		$this->assign ( "td_number", $td_number );
		$this->assign ( "fu_number", $fu_number );
		$this->assign ( "order_sn", $order_sn );
		$where = "(tod.state = 0 or tod.state = 3 or tod.state = 4 or tod.state = 6) and o.order_status != 7";
		if (! empty ( $td_number )) {
			$where .= " and concat(o.order_sn,right(convert(tod.c_time,char(11)),2),o.services_id) like '%$td_number%'";
		}
		if (! empty ( $fu_number )) {
			$where .= " and s.services_sn like '%$fu_number%'";
		}
		if (! empty ( $order_sn )) {
			$where .= " and o.order_sn like '%$order_sn%'";
		}
		if (empty ( $_order )) {
			$_order = 'asc';
		}
		$order = "tod.c_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as o" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services as s on s.id = o.services_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "o.order_sn,o.services_id,s.phone,tod.c_time,tod.order_id,tod.id,s.services_sn,tod.sod_id,o.order_status,c.license_number,e.time as e_time,e.area as e_area,e.code as e_code,e.money as e_money,e.points as e_points,o.pay_money,tod.l_time,tod.state" )->table ( "cw_order as o" )->join ( "cw_endorsement as e on e.id = o.endorsement_id" )->join ( "cw_car as c on c.id = o.car_id" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services as s on s.id = o.services_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$roles [$k] ['sod_id'] = $v ['order_sn'] . substr ( $v ['c_time'], - 2 ) . $v ['services_id'];
			$tod_model = M ( "turn_order" );
			$roles [$k] ['tod_count'] = $tod_model->where ( "order_id = '{$v['order_id']}' and sod_id <> '{$v['sod_id']}'" )->count ();
			$older_tod = $this->jiaoyi_model->field ( "o.order_sn,o.services_id,s.phone,tod.c_time,tod.order_id,tod.state,s.services_sn" )->table ( "cw_order as o" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services_order as so on so.id = tod.sod_id" )->join ( "cw_services as s on s.id = so.services_id" )->where ( "tod.order_id = '{$v['order_id']}' and so.id <> '{$v['sod_id']}'" )->order ( "tod.l_time desc" )->find ();
			if (! empty ( $older_tod )) {
				$roles [$k] ['older_sod_id'] = $v ['order_sn'] . substr ( $older_tod ['c_time'], - 2 ) . $older_tod ['services_id'];
				$roles [$k] ['older_s_id'] = $older_tod ['services_id'];
				$roles [$k] ['older_phone'] = $older_tod ['phone'];
				$roles [$k] ['older_s_sn'] = $older_tod ['services_sn'];
				$array = array (
						1 => '手动转单',
						2 => '超时转单' 
				);
				$roles [$k] ['older_state'] = $array [$older_tod ['state']];
			}
			$time = '--';
			if ($v ['state'] == 0) {
				$time = jishi1 + $v ['l_time'];
			} else if ($v ['state'] == 3) {
				$time = jishi2 + $v ['l_time'];
			}
			$roles [$k] ['tmr'] = $time;
		}
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->display ();
	}
	function refund() {
		$order_sn = $_REQUEST ['order_sn'];
		$model = M ( "order" );
		$info = $model->where ( "order_sn = '$order_sn'" )->find ();
		$data = array (
				"out_trade_no" => $order_sn,
				"total_fee" => intval($info["pay_money"]*100),
				"refund_fee" => intval($info["pay_money"]*100) 
		);
		$url = "http://" . $_SERVER ['SERVER_NAME'] . "/Wxpay/example/refund.php";
		$post_params = $data;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_params );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		$data = array (
				"order_status" => 7 
		);
		$model->where ( "order_sn = '$order_sn'" )->save ( $data );
		
		$turn_model = M ( 'turn_order' );
		
		// 服务商相关操作
		$turn_order_model = M ( "turn_order" );
		$data = array (
				"state" => 1 
		);
		$turn_order_model->where ( "sod_id='{$info['so_id']}' and order_id = '{$info['id']}'" )->save ( $data );
		$bank_model = M ( "bank" );
		$bank_info_older = $bank_model->where ( "bank_id='{$info['services_id']}'" )->find ();
		if (! empty ( $bank_info_older )) {
			$data = array (
					"money" => ($bank_info_older ['money'] - $info ['money']) > 0 ? ($bank_info_older ['money'] - $info ['money']) : 0,
					"balance_money" => ($bank_info_older ['balance_money'] - $info ['money']) > 0 ? ($bank_info_older ['balance_money'] - $info ['money']) : 0,
					"end_money" => ($bank_info_older ['end_money'] - $info ['money']) > 0 ? ($bank_info_older ['end_money'] - $info ['money']) : 0,
					"income_money" => ($bank_info_older ['income_money'] - $info ['money']) > 0 ? ($bank_info_older ['income_money'] - $info ['money']) : 0 
			);
			$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
		}
		// 记录
		$bank_info_older = $bank_model->where ( "bank_id='{$info['services_id']}'" )->find ();
		$data = array (
				"services_id" => $bank_info_older ['bank_id'],
				"income_money" => $bank_info_older ['income_money'],
				"pay_money" => $bank_info_older ['pay_money'],
				"end_money" => $bank_info_older ['end_money'],
				"user_money" => $bank_info_older ['user_money'],
				"money" => $bank_info_older ['money'],
				"order_id" => $info ['id'],
				"c_time" => time ()
		);
		$jl_model = M ( "services_jilu" );
		$jl_model->add ( $data );
		
		// 修改违章状态
		$data = array (
				"is_manage" => 0,
				"manage_time" => time () 
		);
		$endorsement_model = M ( "Endorsement" );
		$endorsement_model->where ( "id={$info['endorsement_id']}" )->save ( $data );
		// 推送消息
		$model = M ();
		$user = $model->table ( "cw_order as o" )->join ( "cw_user as u on u.id=o.user_id" )->join ( "cw_car as c on c.id=o.car_id" )->field ( "u.openid, o.order_sn, c.license_number" )->where ( "o.id = '{$info['id']}'" )->find ();
		if (! empty ( $user )) {
			$model = new IndexController ();
			$data = array (
					'first' => array (
							'value' => urlencode ( first_key ),
							'color' => "#000000" 
					),
					'keyword1' => array (
							'value' => urlencode ( "{$user ['order_sn']}" ),
							'color' => '#000000' 
					),
					'keyword2' => array (
							'value' => urlencode ( "{$user['license_number']}" ),
							'color' => '#000000' 
					),
					'keyword3' => array (
							'value' => urlencode ( status3 ),
							'color' => '#000000' 
					),
					'remark' => array (
							'value' => urlencode ( last_key ),
							'color' => '#000000' 
					) 
			);
			include_once 'application/Weixin/Conf/config.php';
			$model->doSend ( 0, '', $user ['openid'], MUBAN3, URL2, $data );
		}
		$this->ajaxReturn ( 1 );
		return true;
	}
}




