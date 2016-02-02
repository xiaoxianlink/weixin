<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;

class DingyueController extends AdminbaseController {
	protected $Dingyue_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['fuwu'] = '';
		$_SESSION ['jiao'] = '';
		$_SESSION ['xitong'] = '';
		$this->Dingyue_model = D ( "Common/car" );
	}
	function cheliang() {
		$che_num = $_GET ['user_is_sub'];
		$che_number = $_REQUEST ['che_number'];
		$channel = $_GET ['channel'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['che_number1'] = $che_number;
		} else {
			$che_number = $_SESSION ['dingyue'] ['che_number1'];
		}
		
		$where = '';
		if (empty ( $che_number )) {
			$where = '';
		} else {
			$where = " license_number like '%$che_number%'";
		}
		if (! empty ( $che_num )) {
			$where = " is_sub=$che_num-1";
		}
		if (! empty ( $channel )) {
			$where = " b.channel=$channel-1";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.create_time $_order";
		$this->assign ( 'order', $_order );
		$this->assign ( "number", $che_number );
		$count = $this->Dingyue_model->table ( "cw_user_car as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_user as c on c.id=a.user_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "@rownum:=@rownum+1 AS iid,b.id,b.license_number,a.c_time,a.create_time,a.is_sub,b.unsubscribe_time,b.channel,c.nickname,b.engine_number,b.frame_number,c.openid,c.city,c.username" )->table ( "(SELECT @rownum:=0) r,cw_user_car as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_user as c on c.id=a.user_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$d_number = $this->Dingyue_model->field ( "count(*)" )->table ( "cw_user_car as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_user as c on c.id=a.user_id" )->where ( "is_sub=0" )->select ();
		$this->assign ( 'd_number', $d_number );
		$z_number = $this->Dingyue_model->field ( "count(*)" )->table ( "cw_user_car as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_user as c on c.id=a.user_id" )->select ();
		$this->assign ( 'z_number', $z_number );
		$typ_channel = $this->Dingyue_model->field ( "count(b.channel) as nums" )->table ( "cw_user_car as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_user as c on c.id=a.user_id" )->group ( "b.channel" )->select ();
		$this->assign ( 'sum', $typ_channel );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	/*
	 * function cheliang_update(){ $id = intval(I("get.id")); $roles=$this->Dingyue_model->query("select a.id,a.license_number,a.create_time,a.is_sub,a.unsubscribe_time,a.channel,b.nickname,a.user_id from cw_car as a left join cw_user as b on a.user_id=b.id where a.id=$id "); $roles[0]['create_time']=empty($roles[0]['create_time'])?$roles[0]['create_time']:date('Y-m-d h:i:s',$roles[0]['create_time']); $roles[0]['unsubscribe_time']=empty($roles[0]['unsubscribe_time'])?$roles[0]['unsubscribe_time']:date('Y-m-d h:i:s',$roles[0]['unsubscribe_time']); $this->assign('roles',$roles); $car_id=$_POST['id']; $create_time=strtotime($_POST['create_time']); $is_sub=$_POST['is_sub']; $unsubscribe_time=strtotime($_POST['unsubscribe_time']); if(!empty($car_id)){ if ($this->Dingyue_model->execute("update cw_car set create_time='$create_time' ,is_sub='$is_sub',unsubscribe_time='$unsubscribe_time' where id=$car_id ")>0) { $this->success("修改成功！",U("Dingyue/cheliang")); } else { $this->error("修改失败！"); } } $this->display(); } function cheliang_delete(){ $id = intval(I("get.id")); if ($this->Dingyue_model->execute("delete from cw_car where id=$id")!==false) { $this->success("删除成功！"); } else { $this->error("删除失败！"); } //$this->display(); }
	 */
	function count() {
		$number = $_REQUEST ['che_number'];
		$time_start = $_REQUEST ['che_time_start'];
		$time_end = $_REQUEST ['che_time_end'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['che_number2'] = $number;
			$_SESSION ['dingyue'] ['che_time_start2'] = $time_start;
			$_SESSION ['dingyue'] ['che_time_end2'] = $time_end;
		} else {
			$number = $_SESSION ['dingyue'] ['che_number2'];
			$time_start = $_SESSION ['dingyue'] ['che_time_start2'];
			$time_end = $_SESSION ['dingyue'] ['che_time_end2'];
		}
		$this->assign ( "time_start", (empty ( $time_start ) ? '' : $time_start) );
		$this->assign ( "time_end", (empty ( $time_end ) ? '' : $time_end) );
		$this->assign ( "number", $number );
		$time_start = strtotime ( $time_start );
		$time_end = strtotime ( $time_end );
		$where = "a.msg_type=1";
		if (! empty ( $number )) {
			$where .= " and c.license_number like '%$number%'";
		}
		if (! empty ( $time_start )) {
			$where .= " and a.create_time > '$time_start'";
		}
		if (! empty ( $time_end )) {
			$where .= " and a.create_time < '$time_end'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.create_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->Dingyue_model->table ( "cw_message as a" )->join ( "cw_car as c on c.id=a.tar_id" )->join ( "cw_user as d  on a.openid=d.openid" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "@rownum:=@rownum+1 AS iid,c.channel,a.create_time,d.nickname,c.license_number,a.all_money as money,a.all_points as points,a.nums,d.openid,d.city,d.create_time as u_time" )->table ( "(SELECT @rownum:=0) r,cw_message as a" )->join ( "cw_car as c on c.id=a.tar_id" )->join ( "cw_user as d  on a.openid=d.openid" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$model = M ( "endorsement" );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$count = $this->Dingyue_model->table ( "cw_message" )->where ( "msg_type=1" )->count ();
		$count1 = $this->Dingyue_model->table ( "cw_message" )->where ( "YEARWEEK(FROM_UNIXTIME(create_time,'%Y-%m-%d')) = YEARWEEK(now()) and msg_type=1" )->count ();
		$count2 = $this->Dingyue_model->table ( "cw_message" )->where ( "FROM_UNIXTIME(create_time,'%Y-%m')=date_format(now(),'%Y-%m') and msg_type=1" )->count ();
		$count3 = $this->Dingyue_model->table ( "cw_message" )->where ( "FROM_UNIXTIME(create_time,'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m') and msg_type=1" )->count ();
		$this->assign ( 'count', $count );
		$this->assign ( 'count1', $count1 );
		$this->assign ( 'count2', $count2 );
		$this->assign ( 'count3', $count3 );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function select() {
		$number = $_POST ['che_number'];
		$time_start = $_POST ['che_time_start'];
		$time_end = $_POST ['che_time_end'];
		$port = $_POST ['che_port'];
		$state = $_POST ['che_state'];
		$_order = $_REQUEST ['order'];
		$query_no = $_REQUEST ['query_no'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['che_number3'] = $number;
			$_SESSION ['dingyue'] ['che_time_start3'] = $time_start;
			$_SESSION ['dingyue'] ['che_time_end3'] = $time_end;
			$_SESSION ['dingyue'] ['che_port3'] = $port;
			$_SESSION ['dingyue'] ['che_state3'] = $state;
			$_SESSION ['dingyue'] ['query_no'] = $query_no;
		} else {
			$number = $_SESSION ['dingyue'] ['che_number3'];
			$time_start = $_SESSION ['dingyue'] ['che_time_start3'];
			$time_end = $_SESSION ['dingyue'] ['che_time_end3'];
			$port = $_SESSION ['dingyue'] ['che_port3'];
			$state = $_SESSION ['dingyue'] ['che_state3'];
			$query_no = $_SESSION ['dingyue'] ['query_no'];
		}
		$this->assign ( "time_start", (empty ( $time_start ) ? '' : $time_start) );
		$this->assign ( "time_end", (empty ( $time_end ) ? '' : $time_end) );
		$this->assign ( "number", $number );
		$this->assign ( "port", $port );
		$this->assign ( "state", $state );
		$this->assign ( "query_no", $query_no );
		$time_start = strtotime ( $time_start );
		$time_end = strtotime ( $time_end );
		$where = "1=1";
		if (! empty ( $number )) {
			$where .= " AND b.license_number like '%$number%'";
		}
		if (! empty ( $port )) {
			$where .= " AND a.port like '%$port%'";
		}
		if (! empty ( $state )) {
			$where .= " AND a.code like '%$state%'";
		}
		if (! empty ( $time_start )) {
			$where .= " AND a.c_time > $time_start";
		}
		if (! empty ( $time_end )) {
			$where .= " AND a.c_time < $time_end";
		}
		if (! empty ( $query_no )) {
			$where .= " and a.id like '%$query_no%'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.c_time $_order";
		$this->assign ( 'order', $_order );
		// $roles=$this->Dingyue_model->field("a.query_no,b.id,b.license_number,b.frame_number,b.engine_number,a.area,a.time,a.query_port,c.code,a.points")->table("cw_endorsement as a")->join("cw_car as b on a.car_id=b.id")->join("cw_code as c on c.port=a.query_port")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$count = $this->Dingyue_model->table ( "cw_endorsement_jilu as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_code as c on a.code=c.code and a.port=c.port" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "a.*,b.license_number,b.frame_number,b.engine_number,c.content" )->table ( "cw_endorsement_jilu as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_code as c on a.code=c.code and a.port=c.port" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$code_mode = M ( "Code" );
		$endorsement_mode = M ( "endorsement" );
		$count = $this->Dingyue_model->table ( "cw_endorsement_jilu" )->count ();
		$count1 = $this->Dingyue_model->table ( "cw_endorsement_jilu" )->where ( "YEARWEEK(FROM_UNIXTIME(c_time,'%Y-%m-%d')) = YEARWEEK(now())" )->count ();
		$count2 = $this->Dingyue_model->table ( "cw_endorsement_jilu" )->where ( "FROM_UNIXTIME(c_time,'%Y-%m')=date_format(now(),'%Y-%m')" )->count ();
		$count3 = $this->Dingyue_model->table ( "cw_endorsement_jilu" )->where ( "FROM_UNIXTIME(c_time,'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m')" )->count ();
		$this->assign ( 'str', $roles );
		$this->assign ( 'count', $count );
		$this->assign ( 'count1', $count1 );
		$this->assign ( 'count2', $count2 );
		$this->assign ( 'count3', $count3 );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function shuju() {
		$number = $_POST ['che_number'];
		$engine = $_POST ['che_engine'];
		$table = $_POST ['che_table'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['che_number4'] = $number;
			$_SESSION ['dingyue'] ['che_engine4'] = $engine;
			$_SESSION ['dingyue'] ['che_table4'] = $table;
		} else {
			$number = $_SESSION ['dingyue'] ['che_number4'];
			$engine = $_SESSION ['dingyue'] ['che_engine4'];
			$table = $_SESSION ['dingyue'] ['che_table4'];
		}
		$this->assign ( "number", $number );
		$this->assign ( "engine", $engine );
		$this->assign ( "table", $table );
		if (empty ( $number ) && empty ( $engine ) && empty ( $table )) {
			$where = "";
		} elseif (! empty ( $number ) && empty ( $engine ) && empty ( $table )) {
			$where = "license_number like'%$number%'";
		} elseif (empty ( $number ) && ! empty ( $engine ) && empty ( $table )) {
			$where = "engine_number like '%$engine%'";
		} elseif (empty ( $number ) && empty ( $engine ) && ! empty ( $table )) {
			$where = "frame_number like '%$table%'";
		} elseif (! empty ( $number ) && ! empty ( $engine ) && empty ( $table )) {
			$where = "license_number like'%$number%' and engine_number like '%$engine%'";
		} elseif (! empty ( $number ) && empty ( $engine ) && ! empty ( $table )) {
			$where = "license_number like'%$number%' and frame_number like '%$table%'";
		} elseif (empty ( $number ) && ! empty ( $engine ) && ! empty ( $table )) {
			$where = "engine_number like '%$engine%' and frame_number like '%$table%'";
		} elseif (! empty ( $number ) && ! empty ( $engine ) && ! empty ( $table )) {
			$where = "license_number like'%$number%' and engine_number like '%$engine%' and frame_number like '%$table%'";
		}
		// $roles=$this->Dingyue_model->query("select a.id,a.license_number,a.frame_number,a.engine_number,a.create_time from cw_car as a ".$where );
		$count = $this->Dingyue_model->table ( "cw_car as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.license_number,a.frame_number,a.engine_number,a.create_time" )->table ( "(SELECT @rownum:=0) r,cw_car as a" )->where ( $where )->order ( 'a.create_time desc' )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$region_model = M ( "Region" );
		foreach ( $roles as $k => $v ) {
			$nums = mb_substr ( $v ['license_number'], 0, 2, 'utf-8' );
			$region_info = $region_model->where ( "nums = '$nums'" )->find ();
			$roles [$k] ['province'] = $region_info ['province'];
			$roles [$k] ['city'] = $region_info ['city'];
		}
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function shuju_delete() {
		$id = intval ( I ( "get.id" ) );
		if ($this->Dingyue_model->execute ( "delete from cw_car where id=$id" ) > 0) {
			$this->success ( "删除成功" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	function weizhang() {
		$number = $_REQUEST ['che_number'];
		$time_start = $_REQUEST ['che_time_start'];
		$time_end = $_REQUEST ['che_time_end'];
		$_order = $_REQUEST ['order'];
		$state = $_REQUEST ['state'];
		$query_no = $_REQUEST ['query_no'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['che_number5'] = $number;
			$_SESSION ['dingyue'] ['che_time_start5'] = $time_start;
			$_SESSION ['dingyue'] ['che_time_end5'] = $time_end;
			$_SESSION ['dingyue'] ['query_no'] = $query_no;
		} else {
			$number = $_SESSION ['dingyue'] ['che_number5'];
			$time_start = $_SESSION ['dingyue'] ['che_time_start5'];
			$time_end = $_SESSION ['dingyue'] ['che_time_end5'];
			$query_no = $_SESSION ['dingyue'] ['query_no'];
		}
		$this->assign ( "time_start", (empty ( $time_start ) ? '' : $time_start) );
		$this->assign ( "time_end", (empty ( $time_end ) ? '' : $time_end) );
		$this->assign ( "number", $number );
		$this->assign ( "query_no", $query_no );
		$time_start = strtotime ( $time_start );
		$time_end = strtotime ( $time_end );
		$where = "a.msg_type = 2";
		if (! empty ( $number )) {
			$where .= " and c.license_number like '%$number%'";
		}
		if (! empty ( $time_start )) {
			$where .= " and a.create_time > '$time_start'";
		}
		if (! empty ( $time_end )) {
			$where .= " and a.create_time < '$time_end'";
		}
		if (! empty ( $query_no )) {
			$where .= " and b.query_no like '%$query_no%'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
			$order = "a.create_time $_order, b.time $_order";
		} else {
			if ($state == 1) {
				$order = "a.create_time $_order, b.time desc";
			} else {
				$order = "b.time $_order, a.create_time desc";
			}
		}
		$this->assign ( 'order', $_order );
		$this->assign ( 'state', $state );
		$count = $this->Dingyue_model->table ( "cw_message as a" )->join ( "cw_endorsement as b on b.id=a.tar_id" )->join ( "cw_car as c on c.id=b.car_id" )->join ( "cw_user as d  on a.openid=d.openid" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "@rownum:=@rownum+1 AS iid,b.content,b.time,b.create_time as c_time,b.address,b.code,b.area,b.money,b.points,c.channel,a.create_time,d.nickname,c.license_number,a.nums,d.openid,d.city,d.create_time as u_time,b.query_no" )->table ( "(SELECT @rownum:=0) r,cw_message as a" )->join ( "cw_endorsement as b on b.id=a.tar_id" )->join ( "cw_car as c on c.id=b.car_id" )->join ( "cw_user as d  on a.openid=d.openid" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$model = M ( "endorsement" );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$roles_sums = $count;
		$this->assign ( "roles_sums", $roles_sums );
		$count1 = $this->Dingyue_model->table ( "cw_message" )->where ( "YEARWEEK(FROM_UNIXTIME(create_time,'%Y-%m-%d')) = YEARWEEK(now()) and msg_type=2" )->count ();
		$count2 = $this->Dingyue_model->table ( "cw_message" )->where ( "FROM_UNIXTIME(create_time,'%Y-%m')=date_format(now(),'%Y-%m') and msg_type=2" )->count ();
		$count3 = $this->Dingyue_model->table ( "cw_message" )->where ( "FROM_UNIXTIME(create_time,'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m') and msg_type=2" )->count ();
		$this->assign ( 'count1', $count1 );
		$this->assign ( 'count2', $count2 );
		$this->assign ( 'count3', $count3 );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function users() {
		$username = $_POST ['username'];
		$type = $_POST ['type'];
		$channel = $_GET ['channel'];
		$concern = $_GET ['concern'];
		if (IS_POST) {
			$_SESSION ['dingyue'] = '';
			$_SESSION ['dingyue'] ['username6'] = $username;
			$_SESSION ['dingyue'] ['type6'] = $type;
			$_SESSION ['dingyue'] ['channel6'] = $channel;
			$_SESSION ['dingyue'] ['concern6'] = $concern;
		} else {
			$username = $_SESSION ['dingyue'] ['username6'];
			$type = $_SESSION ['dingyue'] ['type6'];
			$channel = $_SESSION ['dingyue'] ['channel6'];
			$concern = $_SESSION ['dingyue'] ['concern6'];
		}
		$where = '1=1';
		if (! empty ( $username )) {
			$where .= " and username='$username'";
		}
		if (! empty ( $type )) {
			$where .= " and  channel='$type'-1 ";
		}
		if (! empty ( $channel )) {
			$where .= " and  channel='$channel'-1";
		}
		if (! empty ( $concern )) {
			$where .= " and is_att='$concern'-1";
		}
		$this->assign ( "input_sum", array (
				$username,
				$type 
		) );
		$count = $this->Dingyue_model->table ( "cw_user as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Dingyue_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.group_id,a.openid,a.username,a.nickname,is_att,a.create_time,a.city" )->table ( "(SELECT @rownum:=0) r,cw_user as a" )->where ( $where )->order ( "a.create_time desc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$typ_roles = $this->Dingyue_model->field ( "count(is_att) as nums" )->table ( "cw_user as a" )->group ( "is_att" )->select ();
		$type_one = $typ_roles [0] [nums];
		$type_two = $typ_roles [1] [nums] + $typ_roles [0] [nums];
		$this->assign ( "array_type", array (
				$type_one,
				$type_two 
		) );
		$this->display ();
	}
}
