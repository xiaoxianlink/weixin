<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;
use Weixin\Controller\IndexController;
use Think\Model;
use Think\Log;
use Weixin\Controller\ApiController;

include_once 'application/Weixin/Conf/config.php';
class XitongController extends AdminbaseController {
	protected $xitong_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['fuwu'] = '';
		$_SESSION ['jiao'] = '';
		$this->xitong_model = D ( "Common/users" );
	}
	function city() {
		$province = $_POST ["province_name"];
		$this->assign ( "cityname", $province );
		$area = $_POST ["wei_range"];
		$vcode = $_GET ['vcode'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['province_name1'] = $province;
			$_SESSION ['xitong'] ['wei_range1'] = $area;
		} else {
			$province = $_SESSION ['xitong'] ['province_name1'];
			$area = $_SESSION ['xitong'] ['wei_range1'];
		}
		if (empty ( $province )) {
			$where = "";
		} else {
			$where = " (province like '%$province%' or abbreviation like '%$province%')";
		}
		if (! empty ( $vcode )) {
			$where = " is_dredge=$vcode-1";
			$this->assign ( "vcode", $vcode );
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.sf_id $_order,a.level asc";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_region as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.sf_id,a.level,a.province,a.abbreviation,a.code,a.acode,a.city,a.nums,a.engine_nums,a.frame_nums,a.c_engine_nums,a.c_frame_nums,a.registno,a.vcode,a.is_dredge " )->table ( "(SELECT @rownum:=0) r,cw_region as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$is_dredge = $this->xitong_model->field ( "count(*) as num" )->table ( "cw_region" )->where ( " is_dredge=0" )->select ();
		$no_dredge = $this->xitong_model->field ( "count(*) as nums" )->table ( "cw_region" )->where ( " is_dredge=1" )->select ();
		$all_dregde = $this->xitong_model->field ( "count(*) as numer" )->table ( "cw_region" )->where ( "is_dredge=0 or is_dredge=1" )->select ();
		$this->assign ( "is_num", $is_dredge );
		$this->assign ( "no_num", $no_dredge );
		$this->assign ( "all_dregde", $all_dregde );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function city_add() {
		$sf_id = $_POST ['sf_id'];
		$acode = $_POST ['acode'];
		$code = $_POST ['code'];
		$level = 2;
		$province = $_POST ['province'];
		$is_dredge = isset ( $_POST ['is_dredge'] ) ? $_POST ['is_dredge'] : 0;
		$city = $_POST ['city'];
		$abbreviation = $_POST ['abbreviation'];
		$nums = $_POST ['nums'];
		$engine_nums = $_POST ['engine_nums'];
		$frame_nums = $_POST ['frame_nums'];
		$c_engine_nums = $_POST ['c_engine_nums'];
		$c_frame_nums = $_POST ['c_frame_nums'];
		$registno = $_POST ['registno'];
		$vcode = $_POST ['vcode'];
		if (! empty ( $province )) {
			$roles = $this->xitong_model->execute ( "insert into cw_region (code,level,province,city,abbreviation,nums,engine_nums,frame_nums,c_engine_nums,c_frame_nums,registno,vcode,is_dredge,orders,sf_id,acode)  values('$code','$level','$province','$city','$abbreviation','$nums','$engine_nums','$frame_nums','$c_engine_nums','$c_frame_nums','$registno','$vcode',$is_dredge,'50','$sf_id','$acode')" );
			if ($roles > 0) {
				$this->success ( "添加成功！" );
			} else {
				$this->error ( "添加失败！" );
			}
		} else {
			$this->error ( "添加失败！" );
		}
	}
	function city_update() {
		$id = $_REQUEST ['id'];
		$is_dredge = isset ( $_REQUEST ['is_dredge'] ) ? $_REQUEST ['is_dredge'] : 0;
		if ($is_dredge == 0) {
			if ($this->xitong_model->execute ( "update cw_region set is_dredge=1 where id=$id" ) > 0) {
				$this->ajaxReturn ( 1 );
			}
		} elseif ($is_dredge == 1) {
			if ($this->xitong_model->execute ( "update cw_region  set is_dredge=0 where id=$id" )) {
				$this->ajaxReturn ( 1 );
			}
		}
	}
	function daima() {
		$code = $_POST ['wei_code'];
		$range = $_POST ['wei_range'];
		$state = $_POST ['wei_state'];
		$type = $_POST ['wei_type'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['wei_code2'] = $code;
			$_SESSION ['xitong'] ['wei_range2'] = $range;
			$_SESSION ['xitong'] ['wei_state2'] = $state;
			$_SESSION ['xitong'] ['wei_type2'] = $type;
		} else {
			$code = $_SESSION ['xitong'] ['wei_code2'];
			$range = $_SESSION ['xitong'] ['wei_range2'];
			$state = $_SESSION ['xitong'] ['wei_state2'];
			$type = $_SESSION ['xitong'] ['wei_type2'];
		}
		$array = array (
				$code,
				$range,
				$state,
				$type 
		);
		$this->assign ( 'array', $array );
		$where = " 1=1 ";
		if (! empty ( $range )) {
			$where .= "";
		}
		if (! empty ( $state )) {
			$where .= " and points='$state'-1 ";
		}
		if (! empty ( $type )) {
			$where .= " and state='$type'-1 ";
		}
		if (! empty ( $code )) {
			$where .= " and code like '%$code%' ";
		}
		if (empty ( $_order )) {
			$_order = 'asc';
		}
		$order = "a.code $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_violation" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.code,a.money,a.points,a.content,a.explain,a.gist,a.state,a.area" )->table ( "(SELECT @rownum:=0) r,cw_violation as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$r_model = M ( "region" );
		foreach ( $roles as $k => $v ) {
			$areas = explode ( ',', $v ['area'] );
			if (in_array ( '0', $areas )) {
				$roles [$k] ['city'] = '全国';
			} else {
				$area_s = '';
				foreach ( $areas as $c ) {
					$area_s .= "'{$c}',";
				}
				$area_s = rtrim ( $area_s, ',' );
				$r_list = $r_model->where ( "id in ($area_s)" )->select ();
				$citys = '';
				foreach ( $r_list as $c ) {
					$citys .= "{$c['city']},";
				}
				$citys = rtrim ( $citys, "," );
				$roles [$k] ['city'] = $citys;
			}
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->field ( "count(state) as sums" )->table ( "cw_violation" )->group ( "state" )->select ();
		$state_0 = $roles_a [0] [sums];
		$state_1 = $roles_a [1] [sums];
		$state_num = $state_0 + $state_1;
		$this->assign ( "array_num", array (
				$state_0,
				$state_1,
				$state_num 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function dai_add() {
		$id = $_POST ['id'];
		$code = $_POST ['code'];
		$money = $_POST ['money'];
		$points = $_POST ['points'];
		$content = $_POST ['content'];
		$explain = $_POST ['explain'];
		$gist = $_POST ['gist'];
		$area = $_POST ['area'];
		$state = $_POST ['state'];
		if ($id == '0') {
			$data = array (
					"code" => $code,
					"money" => $money,
					"points" => $points,
					"content" => $content,
					"explain" => $explain,
					"gist" => $gist,
					"area" => $area,
					"state" => strtr ( $state, "，", "," ) 
			);
			$vio_model = M ( "violation" );
			
			if ($vio_model->add ( $data )) {
				$this->success ( "添加成功！" );
			} else {
				$this->error ( "添加失败！" );
			}
		} else {
			$data = array (
					"code" => $code,
					"money" => $money,
					"points" => $points,
					"content" => $content,
					"explain" => $explain,
					"gist" => $gist,
					"area" => str_replace ( "，", ",", $area ),
					"state" => $state 
			);
			$vio_model = M ( "violation" );
			
			if ($vio_model->where ( "id='$id'" )->save ( $data )) {
				$this->success ( "修改成功！" );
			} else {
				$this->error ( "修改失败！" );
			}
		}
	}
	function dai_update() {
		$id = intval ( I ( "get.id" ) );
		$roles = $this->xitong_model->query ( "select * from cw_violation where id=$id" );
		$this->assign ( 'roles', $roles );
		$code = $_POST ['code'];
		$money = $_POST ['money'];
		$points = $_POST ['points'];
		$content = $_POST ['content'];
		$explain = $_POST ['explain'];
		$gist = $_POST ['gist'];
		$state = $_POST ['state'];
		$dai_id = $_POST ['id'];
		$area = str_replace ( "，", ",", $_POST ['area'] );
		if (! empty ( $code )) {
			if ($this->xitong_model->execute ( "update cw_violation set code='$code',money='$money',points=$points,content='$content',gist='$gist',state='$state',`explain`=$explain,`area`=$area where id=$dai_id" ) > 0) {
				$this->success ( "修改成功！", U ( 'Xitong/daima' ) );
			} else {
				$this->error ( "修改失败！" );
			}
		}
		$this->display ();
	}
	function dai_delete() {
		$id = intval ( I ( "get.id" ) );
		if ($this->xitong_model->execute ( "delete from cw_violation where id=$id" ) > 0) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败！" );
		}
	}
	function jilu() {
		$number = $_POST ['number'];
		$city = $_POST ['city'];
		$state = $_POST ['state'];
		$time_start = strtotime ( $_POST ['time_start'] );
		$time_end = strtotime ( $_POST ['time_end'] );
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['number3'] = $number;
			$_SESSION ['xitong'] ['city3'] = $city;
			$_SESSION ['xitong'] ['state3'] = $state;
			$_SESSION ['xitong'] ['time_start3'] = $time_start;
			$_SESSION ['xitong'] ['time_end3'] = $time_end;
		} else {
			$number = $_SESSION ['xitong'] ['number3'];
			$city = $_SESSION ['xitong'] ['city3'];
			$state = $_SESSION ['xitong'] ['state3'];
			$time_start = $_SESSION ['xitong'] ['time_start3'];
			$time_end = $_SESSION ['xitong'] ['time_end3'];
		}
		$this->assign ( "array", array (
				$number,
				$city,
				$state,
				empty ( $time_start ) ? '' : date ( "Y/m/d H:i:s", $time_start ),
				empty ( $time_end ) ? '' : date ( "Y/m/d H:i:s", $time_end ) 
		) );
		$where = "1=1";
		if (! empty ( $number )) {
			$where .= " and  b.license_number like '%$number%' ";
		}
		if (! empty ( $city )) {
			$where .= "";
		}
		if (! empty ( $state )) {
			$where .= " and a.is_manage='$state'-1 ";
		}
		if (! empty ( $time_start )) {
			$where .= " and a.time >$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= " and a.time <$time_end ";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_endorsement as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_violation as v on v.code=a.code" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,b.license_number,a.time,a.area,a.code,a.money,a.points,a.query_no,a.certificate_no,a.address,a.content,a.office,a.is_manage,a.id,v.code as v_code,v.money as v_money,v.points as v_points,v.content as v_content" )->table ( "(SELECT @rownum:=0) r,cw_endorsement as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_violation as v on v.code=a.code" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->field ( "count(id) as numbers" )->table ( "cw_endorsement" )->select ();
		$this->assign ( 'sums', $roles_a );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function log() {
		$number = $_POST ['log_number'];
		$time_start = strtotime ( $_POST ['time_start'] );
		$time_end = strtotime ( $_POST ['time_end'] );
		$state = $_POST ['log_state'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['log_number4'] = $number;
			$_SESSION ['xitong'] ['time_start4'] = $time_start;
			$_SESSION ['xitong'] ['time_end4'] = $time_end;
			$_SESSION ['xitong'] ['log_state4'] = $state;
		} else {
			$number = $_SESSION ['xitong'] ['log_number4'];
			$time_start = $_SESSION ['xitong'] ['time_start4'];
			$time_end = $_SESSION ['xitong'] ['time_end4'];
			$state = $_SESSION ['xitong'] ['log_state4'];
		}
		$this->assign ( "array", array (
				$number,
				$state,
				empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start ),
				empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end ) 
		) );
		$where = "1=1";
		if (! empty ( $number )) {
			$where .= " and b.license_number like '%$number%'";
		}
		if ($state != null && $state != '') {
			if ($state == '0') {
				$where .= " and l.state = 1";
			} else {
				$where .= " and l.state = 2";
			}
		}
		if (! empty ( $time_start )) {
			$where .= " and l.c_time >$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= " and l.c_time <$time_end ";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "l.c_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_endorsement_log as l" )->join ( "cw_endorsement as a on a.id=l.end_id" )->join ( "cw_car as b on a.car_id=b.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,b.license_number,a.time,a.area,a.code,a.money,a.points,a.query_no,a.certificate_no,a.address,a.content,a.office,a.is_manage,a.manage_time,a.create_time,l.state as l_state,l.c_time as l_c_time,l.type as l_type" )->table ( "(SELECT @rownum:=0) r,cw_endorsement_log as l" )->join ( "cw_endorsement as a on a.id=l.end_id" )->join ( "cw_car as b on a.car_id=b.id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->query ( "select count(id) as numbers from cw_endorsement_log " );
		$this->assign ( 'sums', $roles_a );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function select() {
		$port = $_POST ['port'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['port5'] = $port;
		} else {
			$port = $_SESSION ['xitong'] ['port5'];
		}
		$this->assign ( "port", $port );
		if (empty ( $port )) {
			$where = '';
		} else {
			$where = "port like '%$port%'";
		}
		$count = $this->xitong_model->table ( "cw_code" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.port,a.code,a.content " )->table ( "(SELECT @rownum:=0) r,cw_code as  a" )->where ( $where )->order ( "a.port asc,a.code asc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function role() {
		$this->display ();
	}
	function user() {
		$name = $_POST ['province_name'];
		if (empty ( $name )) {
			$where = "";
		} else {
			$where = " user_login like '%$name%'";
		}
		$this->assign ( "name", $name );
		$count = $this->xitong_model->table ( "cw_users as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.user_login,a.create_time,a.last_login_time,a.user_role " )->table ( "(SELECT @rownum:=0) r,cw_users as a" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function user_add() {
		$username = $_POST ['username'];
		$password_one = md5 ( $_POST ['password_one'] );
		$password_two = md5 ( $_POST ['password_two'] );
		$type = $_POST ['type'];
		if (empty ( $username )) {
			$this->error ( "用户名不能为空" );
		} elseif (empty ( $password_one )) {
			$this->error ( "密码不能为空" );
		}
		$t_u_id = $this->xitong_model->field ( "a.id" )->table ( "cw_users as a" )->where ( "user_login='$username'" )->select ();
		if (! empty ( $t_u_id )) {
			$this->error ( "用户名已注册" );
		} elseif ($password_one !== $password_two) {
			$this->error ( "两次密码不一致" );
		}
		$create_time = date ( 'Y-m-d H:i:s' );
		/*
		 * if( $this->xitong_model->execute("insert into cw_users (user_login,user_pass,user_role,create_time) values('$username','$password_one','$type','$create_time')")>0){ $this->success("添加成功",U('Xitong/user')); }
		 */
		if ($this->xitong_model->add ( array (
				'user_login' => $username,
				'user_pass' => $password_one,
				'user_role' => $type,
				'create_time' => $create_time 
		) ) - count () > 0) {
			$this->success ( "添加成功", U ( 'Xitong/user' ) );
		}
	}
	function user_password() {
		$password_old = $_POST ['password_old'];
		$password_one = $_POST ['password_one'];
		$password_two = $_POST ['password_two'];
		echo $id = intval ( I ( 'get.id' ) );
	}
	function user_delete() {
		$id = intval ( I ( 'get.id' ) );
		if ($this->xitong_model->execute ( "delete from cw_users where id=$id" ) > 0) {
			$this->success ( "删除成功" );
		}
	}
	function user_role() {
	}
	function window() {
		$window_number = $_POST ['window_number'];
		$type = $_POST ['window_type'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['window_number6'] = $window_number;
			$_SESSION ['xitong'] ['window_type6'] = $type;
		} else {
			$window_number = $_SESSION ['xitong'] ['window_number6'];
			$type = $_SESSION ['xitong'] ['window_type6'];
		}
		$this->assign ( "type", $type );
		$this->assign ( "window_number", $window_number );
		$where = "(t.state = 0 or t.state = 3 or t.state = 4)";
		/*
		 * if (empty ( $window_number ) && empty ( $type )) { $where = ""; } elseif (! empty ( $window_number ) && empty ( $type )) { $where = " a.id='$window_number'"; } elseif (empty ( $window_number ) && ! empty ( $type )) { $where = " b.order_status='$type'"; } elseif (! empty ( $window_number ) && ! empty ( $type )) { $where = " a.id='$window_number' and b.order_status='$type'"; }
		 */
		if (! empty ( $window_number )) {
			$where .= " and s.services_sn like '%$window_number%' ";
		}
		if (! empty ( $type )) {
			if ($type == 1) {
				$type = 0;
			}
			$where .= " and t.state='$type' ";
		}
		$count = $this->xitong_model->table ( "cw_turn_order as t" )->join ( "cw_services_order as so on t.sod_id=so.id" )->join ( "cw_order as b on b.id=t.order_id" )->join ( "cw_car as c on b.car_id=c.id" )->join ( "cw_endorsement as d on b.endorsement_id=d.id" )->join ( "cw_services as s on s.id = so.services_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,t.id,b.order_sn,s.id as s_id,s.phone,t.c_time,b.last_time,t.state,so.money,b.pay_money,b.pay_sn,c.license_number,c.frame_number,c.engine_number,d.code,d.time,d.area,d.points,d.money,t.l_time,s.services_sn" )->table ( "(SELECT @rownum:=0) r,cw_turn_order as t" )->join ( "cw_services_order as so on t.sod_id=so.id" )->join ( "cw_order as b on b.id=t.order_id" )->join ( "cw_car as c on b.car_id=c.id" )->join ( "cw_endorsement as d on b.endorsement_id=d.id" )->join ( "cw_services as s on s.id = so.services_id" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$roles [$k] ['so_id'] = $v ['order_sn'] . substr ( $v ['c_time'], - 2 ) . $v ['s_id'];
			$time = '--';
			if ($v ['state'] == 0) {
				$time = jishi1 + $v ['l_time'];
			} else if ($v ['state'] == 3) {
				$time = jishi2 + $v ['l_time'];
			}
			$roles [$k] ['tmr'] = $time;
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function shuju() {
		$type = $_POST ['xi_type'];
		$this->assign ( "type", $type );
		$this->display ();
	}
	public function manage() {
		$state = $_REQUEST ['state'];
		$id = $_REQUEST ['id'];
		$model = M ( "turn_order" );
		$info = $model->where ( "id='$id'" )->find ();
		$to_list = $model->field ( "tos.id,so.id as so_id,s.id as s_id,s.phone,tos.c_time,tos.state,tos.l_time" )->table ( "cw_turn_order as tos" )->join ( "cw_services_order as so on so.id=tos.sod_id", 'left' )->join ( "cw_services as s on s.id=so.services_id", 'left' )->where ( "tos.order_id = '{$info['order_id']}'" )->select ();
		$s_ids = "0";
		foreach ( $to_list as $c => $p ) {
			$s_ids .= ",{$p['s_id']}";
		}
		$order_model = M ( "order" );
		$order_info = $order_model->where ( "id='{$info['order_id']}'" )->find ();
		$so_model = M ( "services_order" );
		$so_info = $so_model->where ( "id='{$info['sod_id']}'" )->find ();
		switch ($state) {
			case 1 : // 办不了
				$so_id = $this->screen ( $so_info ['violation'], $s_ids, $order_info ['endorsement_id'] );
				if (! empty ( $so_id )) {
					$data = array (
							"last_time" => time (),
							"so_id" => $so_id 
					);
					$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
					$data = array (
							"order_id" => $order_info ['id'],
							"sod_id" => $so_id,
							"state" => 0,
							"c_time" => time (),
							"l_time" => time () 
					);
					$model->add ( $data );
					$data = array (
							"state" => 1 
					);
					$model->where ( "id='{$info['id']}'" )->save ( $data );
					$so_info2 = $so_model->where ( "id='$so_id'" )->find ();
					$data = array (
							"services_id" => $so_info2 ['services_id'],
							"so_id" => $so_id 
					);
					$order_model->where ( "id='{$info['order_id']}'" )->save ( $data );
					
					$services_model = M ( "services" );
					$services_info = $services_model->where ( "id='{$so_info2['services_id']}'" )->find ();
					if (! empty ( $services_info )) {
						$data = array (
								"all_nums" => $services_info ['all_nums'] + 1 
						);
						$services_model->where ( "id='{$so_info2['services_id']}'" )->save ();
					}
					// 转钱
					$bank_model = M ( "bank" );
					$bank_info_older = $bank_model->where ( "bank_id='{$so_info['services_id']}'" )->find ();
					if (! empty ( $bank_info_older )) {
						$data = array (
								"money" => ($bank_info_older ['money'] - $order_info ['money']) > 0 ? ($bank_info_older ['money'] - $order_info ['money']) : 0,
								"balance_money" => ($bank_info_older ['balance_money'] - $order_info ['money']) > 0 ? ($bank_info_older ['balance_money'] - $order_info ['money']) : 0,
								"end_money" => ($bank_info_older ['end_money'] - $order_info ['money']) > 0 ? ($bank_info_older ['end_money'] - $order_info ['money']) : 0,
								"income_money" => ($bank_info_older ['income_money'] - $order_info ['money']) > 0 ? ($bank_info_older ['income_money'] - $order_info ['money']) : 0 
						);
						$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
					}
					// 记录
					$bank_info_older = $bank_model->where ( "bank_id='{$so_info['services_id']}'" )->find ();
					$data = array (
							"services_id" => $bank_info_older ['bank_id'],
							"income_money" => $bank_info_older ['income_money'],
							"pay_money" => $bank_info_older ['pay_money'],
							"end_money" => $bank_info_older ['end_money'],
							"user_money" => $bank_info_older ['user_money'],
							"money" => $bank_info_older ['money'],
							"order_id" => $info ['order_id'],
							"c_time" => time ()
					);
					$jl_model = M ( "services_jilu" );
					$jl_model->add ( $data );
					
					$bank_info = $bank_model->where ( "bank_id='{$so_info2['bank_id']}'" )->find ();
					if (! empty ( $bank_info )) {
						$data = array (
								"money" => $bank_info ['money'] + $order_info ['money'],
								"balance_money" => $bank_info ['balance_money'] + $order_info ['money'],
								"end_money" => $bank_info ['end_money'] + $order_info ['money'],
								"income_money" => $bank_info ['income_money'] + $order_info ['money'] 
						);
						$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
					}
					// 记录
					$bank_info = $bank_model->where ( "bank_id='{$so_info2['bank_id']}'" )->find ();
					$data = array (
							"services_id" => $bank_info ['bank_id'],
							"income_money" => $bank_info ['income_money'],
							"pay_money" => $bank_info ['pay_money'],
							"end_money" => $bank_info ['end_money'],
							"user_money" => $bank_info ['user_money'],
							"money" => $bank_info ['money'],
							"order_id" => $info ['order_id'],
							"c_time" => time ()
					);
					$jl_model = M ( "services_jilu" );
					$jl_model->add ( $data );
				} else {
					$data = array (
							"last_time" => time (),
							"order_status" => 8 
					);
					$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
					$data = array (
							"state" => 6 
					);
					$model->where ( "id='{$info['id']}'" )->save ( $data );
					
					// 修改违章状态
					$data = array (
							"is_manage" => 0,
							"manage_time" => time () 
					);
					$endorsement_model = M ( "Endorsement" );
					$endorsement_model->where ( "id={$order_info['endorsement_id']}" )->save ( $data );
				}
				break;
			case 3 : // 我来办理
				$data = array (
						"state" => 3,
						'l_time' => time (),
						'do_time' => time () 
				);
				$model->where ( "id='{$info['id']}'" )->save ( $data );
				$data = array (
						"last_time" => time (),
						"order_status" => 3 
				);
				$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
				// 评估
				$services_model = M ( "services" );
				$services_info = $services_model->where ( "id='{$order_info['services_id']}'" )->find ();
				if (! empty ( $services_info )) {
					$data = array (
							"nums" => $services_info ['nums'] + 1 
					);
					$services_model->where ( "id='{$order_info['services_id']}'" )->save ( $data );
				}
				// 推送消息
				$model = M ();
				$user = $model->table ( "cw_order as o" )->join ( "cw_user as u on u.id=o.user_id" )->join ( "cw_car as c on c.id=o.car_id" )->field ( "u.openid, o.order_sn, c.license_number" )->where ( "o.id = '{$info['order_id']}'" )->find ();
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
									'value' => urlencode ( status1 ),
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
				break;
			case 4 : // 办理完成
				$data = array (
						"state" => 4,
						'l_time' => time (),
						'finish_time' => time () 
				);
				$model->where ( "id='{$info['id']}'" )->save ( $data );
				break;
			default :
				;
				break;
		}
		$this->redirect ( "Xitong/window" );
	}
	function screen($code, $s_ids, $e_id) {
		$endorsement_model = M ( "Endorsement" );
		$endorsemen_info = $endorsement_model->where ( "id='$e_id'" )->find ();
		$city = $endorsemen_info ['area'];
		$region_model = M ( "Region" );
		$where = array (
				"city" => $city,
				"level" => 2,
				"is_dredge" => 0 
		);
		$region = $region_model->where ( $where )->order ( 'id' )->find ();
		$city_id1 = $region ['id'];
		
		$where = array (
				"id" => $endorsemen_info ['car_id'] 
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
		// 筛选服务商
		$so_model = M ( "Services_order" ); // 1
		$solist = $so_model->where ( "violation = '$code' and services_id not in ($s_ids) and (code = '$city_id1' or code = '$city_id2')" )->order ( "money asc" )->group ( "services_id" )->limit ( NUMS1 )->select ();
		$services_model = M ( "Services" ); // 2
		$where = "state = 0";
		if (! empty ( $solist )) {
			foreach ( $solist as $p => $c ) {
				if ($p == 0) {
					$where .= " and (id = '{$c['services_id']}'";
				} else {
					$where .= " or id = '{$c['services_id']}'";
				}
			}
			$where .= ")";
			$serviceslist = $services_model->field ( "id" )->where ( $where )->order ( "`grade` desc" )->limit ( NUMS2 )->select ();
			
			$order_model = new Model (); // 3
			$where = "(order_status = 2 or order_status = 3)";
			$services_id1 = array ();
			if (! empty ( $serviceslist )) {
				foreach ( $serviceslist as $p => $c ) {
					if ($p == 0) {
						$where .= " and (services_id = '{$c['id']}'";
					} else {
						$where .= " or services_id = '{$c['id']}'";
					}
					
					$services_id1 [] = $c ['id'];
				}
				$where .= ")";
				$sql = "SELECT COUNT(*) as nums, `services_id` FROM `cw_order` WHERE $where GROUP BY `services_id` ORDER BY nums";
				$orderlist = $order_model->query ( $sql );
				$services_id2 = array ();
				foreach ( $orderlist as $p => $c ) {
					$services_id2 [] = $c ['services_id'];
				}
				$services = array_diff ( $services_id1, $services_id2 );
				if (! empty ( $services )) {
					$services_id = $services [0];
				} else {
					$services_id = $orderlist [0] ['services_id'];
				}
				// 4
				$so = $so_model->where ( "violation = '$code' and services_id = '$services_id' and (code = '$city_id1' or code = '$city_id2')" )->order ( "money asc" )->find ();
				return $so ['id'];
			}
		}
		return false;
	}
	// 违章已处理
	function e_finish() {
		$e_id = isset ( $_REQUEST ['e_id'] ) ? $_REQUEST ['e_id'] : 0;
		$endorsement_model = M ( "Endorsement" );
		$log_model = M ( "Endorsement_log" );
		if ($e_id != 0) {
			$data = array (
					"manage_time" => time (),
					"is_manage" => 2 
			);
			$endorsement_model->where ( "id = '{$e_id}'" )->save ( $data );
			$api = new ApiController();
			$api->finish_order ( $e_id );
			$data = array (
					"end_id" => $e_id,
					"state" => 2,
					"c_time" => time (),
					"type" => 2 
			);
			$log_model->add ( $data );
		}
		$this->ajaxReturn ( 1 );
	}
}
