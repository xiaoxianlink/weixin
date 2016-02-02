<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;

class FuwuController extends AdminbaseController {
	protected $Fuwu_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['jiao'] = '';
		$_SESSION ['xitong'] = '';
		$this->Fuwu_model = D ( "Common/users" );
	}
	function fuwu_list() {
		$phone = $_POST ['fuwu_phone'];
		$state = $_GET ['state'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['fuwu'] = '';
			$_SESSION ['fuwu'] ['fuwu_phone1'] = $phone;
		} else {
			$phone = $_SESSION ['fuwu'] ['fuwu_phone1'];
		}
		$this->assign ( "phone", $phone );
		if (empty ( $phone )) {
			$where = '';
		} else {
			$where = " phone like '%$phone%'";
		}
		if (! empty ( $state )) {
			$where = " state=$state-1";
			$this->assign ( 'vstate', $state );
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.services_sn $_order";
		$this->assign ( 'order', $_order );
		$count = $this->Fuwu_model->table ( "cw_services as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Fuwu_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.phone,a. create_time,a.state,a.time,a.services_sn" )->table ( "(SELECT @rownum:=0) r,cw_services as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$model = M ( "services_city" );
		$region_model = M ( "region" );
		foreach ( $roles as $k => $v ) {
			$list = $model->where ( "services_id='{$v['id']}' and state = 0" )->select ();
			$city_ids = "0";
			foreach ( $list as $p ) {
				$city_ids .= ",'{$p['code']}'";
			}
			$region_list = $region_model->where ( "id in ($city_ids)" )->select ();
			$citys = '';
			foreach ( $region_list as $p ) {
				$citys .= "{$p['city']},";
			}
			$roles [$k] ['citys'] = rtrim ( $citys, ',' );
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		/*
		 * $a_roles=$this->Fuwu_model->query("select count(state) as a_state from cw_services "); $this->assign('a_roles',$a_roles); $b_roles=$this->Fuwu_model->query("select count(state) as b_state from cw_services where state=0" ); $this->assign('b_roles',$b_roles); $c_roles=$a_roles[0][a_state]-$b_roles[0][b_state]; $this->assign('c_roles',$c_roles);
		 */
		$roless = $this->Fuwu_model->field ( "count(state) as state" )->table ( "cw_services" )->group ( "state" )->select ();
		$nums = $roless [0] [state] + $roless [1] [state];
		$num_0 = $roless [0] [state];
		$num_1 = $roless [1] [state];
		$this->assign ( 'state', array (
				$num_0,
				$num_1,
				$nums 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function fengcun() {
		$id = $_REQUEST ['id'];
		$state = $_REQUEST ['state'];
		if ($state == 0) {
			$time = strtotime ( date ( "Y-m-d H:i:s" ) );
			if ($this->Fuwu_model->execute ( "update cw_services set state=1,time=$time where id=$id" ) > 0) {
				$this->ajaxReturn ( 1 );
			} else {
				$this->ajaxReturn ( 2 );
			}
		} elseif ($state == 1) {
			if ($this->Fuwu_model->execute ( "update cw_services set state=0  where id=$id" ) > 0) {
				$this->ajaxReturn ( 1 );
			} else {
				$this->ajaxReturn ( 2 );
			}
		}
	}
	function city() {
		$where = '1=1';
		$count = $this->Fuwu_model->table ( "cw_services as s" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Fuwu_model->field ( "@rownum:=@rownum+1 AS iid,s.*" )->table ( "(SELECT @rownum:=0) r,cw_services as s" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "roles", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function select_services() {
		$id = $_REQUEST ['id'];
		$region = M ( "region" );
		$province = $region->where ( "is_dredge=0 and level=1" )->select ();
		$sc_model = M ();
		$sc_list = $sc_model->table ( "cw_services_city as sc" )->join ( "cw_region as r on r.id=sc.code" )->field ( "r.sf_id" )->where ( "sc.services_id='$id' and sc.state = 0" )->select ();
		$array = array ();
		foreach ( $sc_list as $v ) {
			$array [] = $v ['sf_id'];
		}
		$table = '<tr><th class="th2"></th><th class="th2">省份编码</th><th class="th2">省份名称</th><th class="th2">简称</th></tr>';
		foreach ( $province as $v ) {
			$table .= "<tr onclick='select_city(" . '"' . $v ['province'] . '"' . ")'>";
			if (in_array ( $v ['id'], $array )) {
				$checked = "checked='checked' ";
			} else {
				$checked = "";
			}
			$table .= "<td><input name='province_radio' type='checkbox' value='' $checked /></td>";
			$table .= "<td>{$v['id']}</td>";
			$table .= "<td>{$v['province']}</td>";
			$table .= "<td>{$v['abbreviation']}</td>";
			$table .= "</tr>";
		}
		$data = array (
				0 => $table,
				1 => $id 
		);
		$this->ajaxReturn ( $data );
	}
	function select_city() {
		$id = $_REQUEST ['id'];
		$province = $_REQUEST ['province'];
		$region = M ( "region" );
		$city = $region->where ( "is_dredge=0 and level=2 and province = '$province'" )->order ( "id" )->group ( "city" )->select ();
		$table = '<tr><th class="th3"></th><th class="th3">城市编码</th><th class="th3">城市名称</th><th class="th3">简称</th></tr>';
		foreach ( $city as $v ) {
			$table .= "<tr>";
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code = '{$v[id]}' and state = 0" )->find ();
			if (! empty ( $sc_info )) {
				$checked = "checked='checked' ";
			} else {
				$checked = "";
			}
			$table .= "<td><input name='city_box' type='checkbox' $checked value='' onclick='insert_city({$v['id']})' /></td>";
			$table .= "<td>{$v['id']}</td>";
			$table .= "<td>{$v['city']}</td>";
			$table .= "<td>{$v['abbreviation']}</td>";
			$table .= "</tr>";
		}
		$data = array (
				0 => $table,
				1 => $province 
		);
		$this->ajaxReturn ( $data );
	}
	function insert_city() {
		$city_id = $_REQUEST ['city_id'];
		$id = $_REQUEST ['id'];
		$sc_model = M ( "services_city" );
		$sc_info = $sc_model->where ( "services_id='$id' and code = '$city_id'" )->find ();
		if (empty ( $sc_info )) {
			$data = array (
					"services_id" => $id,
					"code" => $city_id,
					"state" => 0,
					"time" => time () 
			);
			$sc_model->add ( $data );
		} else {
			if ($sc_info ['state'] == 1) {
				$data = array (
						"state" => 0,
						"time" => time () 
				);
				$sc_model->where ( "id='{$sc_info['id']}'" )->save ( $data );
			} else {
				$data = array (
						"state" => 1,
						"time" => time () 
				);
				$sc_model->where ( "id='{$sc_info['id']}'" )->save ( $data );
			}
		}
		return true;
	}
	function project() {
		$where = '1=1';
		$count = $this->Fuwu_model->table ( "cw_services as s" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Fuwu_model->field ( "@rownum:=@rownum+1 AS iid,s.*" )->table ( "(SELECT @rownum:=0) r,cw_services as s" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "roles", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function select_services2() {
		$id = $_REQUEST ['id'];
		// 0
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 0 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_0 = '<tr><th class="th2" colspan="4">0分处罚</th></tr><tr><td><input name="box_0" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			$sc_model = M ( "services_city" );
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_0 .= '<tr>';
				$table_0 .= '<td><input name="box_0" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_0 .= "<td>{$v['code']}</td>";
				$table_0 .= "<td>{$v['points']}</td>";
				$table_0 .= "<td>{$v['money']}</td>";
				$table_0 .= "</tr>";
			}
		}
		// 1
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 1 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_1 = '<tr><th class="th2" colspan="4">1分处罚</th></tr><tr><td><input name="box_1" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_1 .= "<tr>";
				$table_1 .= '<td><input name="box_1" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_1 .= "<td>{$v['code']}</td>";
				$table_1 .= "<td>{$v['points']}</td>";
				$table_1 .= "<td>{$v['money']}</td>";
				$table_1 .= "</tr>";
			}
		}
		// 2
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 2 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_2 = '<tr><th class="th2" colspan="4">2分处罚</th></tr><tr><td><input name="box_2" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_2 .= "<tr>";
				$table_2 .= '<td><input name="box_2" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_2 .= "<td>{$v['code']}</td>";
				$table_2 .= "<td>{$v['points']}</td>";
				$table_2 .= "<td>{$v['money']}</td>";
				$table_2 .= "</tr>";
			}
		}
		// 3
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 3 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_3 = '<tr><th class="th2" colspan="4">3分处罚</th></tr><tr><td><input name="box_3" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_3 .= "<tr>";
				$table_3 .= '<td><input name="box_3" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_3 .= "<td>{$v['code']}</td>";
				$table_3 .= "<td>{$v['points']}</td>";
				$table_3 .= "<td>{$v['money']}</td>";
				$table_3 .= "</tr>";
			}
		}
		// 6
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 6 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_6 = '<tr><th class="th2" colspan="4">6分处罚</th></tr><tr><td><input name="box_6" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_6 .= "<tr>";
				$table_6 .= '<td><input name="box_6" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_6 .= "<td>{$v['code']}</td>";
				$table_6 .= "<td>{$v['points']}</td>";
				$table_6 .= "<td>{$v['money']}</td>";
				$table_6 .= "</tr>";
			}
		}
		// 12
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points = 12 and state = 0 and CHAR_LENGTH(`code`) = 4" )->order ( "code" )->select ();
		$table_12 = '<tr><th class="th2" colspan="4">12分处罚</th></tr><tr><td><input name="box_12" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_12 .= "<tr>";
				$table_12 .= '<td><input name="box_12" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_12 .= "<td>{$v['code']}</td>";
				$table_12 .= "<td>{$v['points']}</td>";
				$table_12 .= "<td>{$v['money']}</td>";
				$table_12 .= "</tr>";
			}
		}
		// 其他
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "points not in ('0','1','2','3','6','12') and state = 0" )->order ( "code" )->select ();
		$table_o = '<tr><th class="th2" colspan="4">其他分处罚</th></tr><tr><td><input name="box_o" type="checkbox" value="" /></td><td>违章代码</td><td>积分</td><td>罚款</td></tr>';
		foreach ( $violation_list as $v ) {
			if ($v ['area'] == null || $v ['area'] == '') {
				$v ['area'] = '0';
			}
			$sc_model = M ( "services_city" );
			$sc_info = $sc_model->where ( "services_id='$id' and code in ({$v['area']}) and state = 0" )->find ();
			if (! empty ( $sc_info ) || $v ['area'] == '0') {
				$scode_model = M ( "services_code" );
				$scode_info = $scode_model->where ( "services_id = '$id' and code = '{$v['code']}' and state = 0" )->find ();
				if (! empty ( $scode_info )) {
					$checked = "checked='checked' ";
				} else {
					$checked = "";
				}
				$table_o .= "<tr>";
				$table_o .= '<td><input name="box_o" type="checkbox" value="" ' . $checked . ' onclick="insert_code(' . "'" . $v ['code'] . "'" . ')" /></td>';
				$table_o .= "<td>{$v['code']}</td>";
				$table_o .= "<td>{$v['points']}</td>";
				$table_o .= "<td>{$v['money']}</td>";
				$table_o .= "</tr>";
			}
		}
		$data = array (
				0 => $table_0,
				1 => $table_1,
				2 => $table_2,
				3 => $table_3,
				4 => $table_6,
				5 => $table_12,
				6 => $table_o 
		);
		$this->ajaxReturn ( $data );
	}
	function insert_code() {
		$code = $_REQUEST ['code'];
		$id = $_REQUEST ['id'];
		$sc_model = M ( "services_code" );
		$violation_mode = M ( "violation" );
		$violation_list = $violation_mode->where ( "code like '$code%' and state = 0" )->select ();
		foreach ( $violation_list as $v ) {
			$sc_info = $sc_model->where ( "services_id='$id' and code = '{$v['code']}'" )->find ();
			if (empty ( $sc_info )) {
				$data = array (
						"services_id" => $id,
						"code" => $v ['code'],
						"state" => 0,
						"c_time" => time () 
				);
				$sc_model->add ( $data );
			} else {
				if ($sc_info ['state'] == 1) {
					$data = array (
							"state" => 0,
							"c_time" => time () 
					);
					$sc_model->where ( "id='{$sc_info['id']}'" )->save ( $data );
				} else {
					$data = array (
							"state" => 1,
							"c_time" => time () 
					);
					$sc_model->where ( "id='{$sc_info['id']}'" )->save ( $data );
				}
			}
		}
		return true;
	}
	function dingjia() {
		$where = '1=1';
		$count = $this->Fuwu_model->table ( "cw_services as s" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Fuwu_model->field ( "@rownum:=@rownum+1 AS iid,s.*" )->table ( "(SELECT @rownum:=0) r,cw_services as s" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "roles", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function select_services3() {
		$id = $_REQUEST ['id'];
		$roles = $this->Fuwu_model->field ( "r.province" )->table ( "cw_services_city as sc" )->join ( "cw_region as r on r.id=sc.code" )->where ( "sc.services_id = '$id' and sc.state = 0" )->select ();
		$provinces = "'0'";
		foreach ( $roles as $v ) {
			$provinces .= ",'{$v['province']}'";
		}
		$region = M ( "region" );
		$province = $region->where ( "is_dredge=0 and level=1 and province in ($provinces)" )->select ();
		$table = '<tr><th class="th2">省份编码</th><th class="th2">省份名称</th><th class="th2">简称</th></tr>';
		foreach ( $province as $v ) {
			$table .= "<tr onclick='select_city(" . '"' . $v ['province'] . '"' . ")'>";
			$table .= "<td>{$v['id']}</td>";
			$table .= "<td>{$v['province']}</td>";
			$table .= "<td>{$v['abbreviation']}</td>";
			$table .= "</tr>";
		}
		$data = array (
				0 => $table,
				1 => $id 
		);
		$this->ajaxReturn ( $data );
	}
	function select_city2() {
		$id = $_REQUEST ['id'];
		$province = $_REQUEST ['province'];
		$city = $this->Fuwu_model->field ( "r.*" )->table ( "cw_services_city as sc" )->join ( "cw_region as r on r.id=sc.code" )->where ( "r.is_dredge=0 and r.level=2 and sc.services_id = '$id' and r.province = '$province'" )->group ( "city" )->select ();
		$table = '<tr><th class="th4">城市编码</th><th class="th4">城市名称</th><th class="th4">简称</th></tr>';
		foreach ( $city as $v ) {
			$table .= "<tr onclick='select_scode({$v['id']})'>";
			$table .= "<td>{$v['id']}</td>";
			$table .= "<td>{$v['city']}</td>";
			$table .= "<td>{$v['abbreviation']}</td>";
			$table .= "</tr>";
		}
		$data = array (
				0 => $table,
				1 => $province 
		);
		$this->ajaxReturn ( $data );
	}
	function select_scode() {
		$id = $_REQUEST ['id'];
		$city_id = $_REQUEST ['city_id'];
		$sc_model = M ( "services_code" );
		$sc_list = $sc_model->where ( "services_id='$id' and state = '0'" )->select ();
		$codes = "'0'";
		foreach ( $sc_list as $v ) {
			$codes .= ",'{$v['code']}'";
		}
		$violation_model = M ( "violation" );
		$violation = $violation_model->where ( "code in ($codes) and state = 0" )->order ( "code" )->select ();
		$so_model = M ( "services_order" );
		$table = '<tr><th class="th3">违章代码</th><th class="th3">罚款</th><th class="th3">罚分</th><th class="th3">定价（元）</th><th class="th3">操作</th></tr>';
		foreach ( $violation as $v ) {
			$so_info = $so_model->where ( "services_id = '$id' and violation = '{$v['code']}' and code = '$city_id'" )->find ();
			$table .= "<tr>";
			$table .= "<td>{$v['code']}</td>";
			$table .= "<td>{$v['money']}</td>";
			$table .= "<td>{$v['points']}</td>";
			if (empty ( $so_info )) {
				$money = $v['money'] + $v['points'] * 100 + 30;
				$table .= "<td><input type='text' style='width: 50px; color: #BFBFBF' id='money_" . $v ['code'] . "' value='" . $money . "'/></td>";
			} else {
				if ($so_info ['money'] > 0) {
					$table .= "<td><input type='text' style='width: 50px;' id='money_" . $v ['code'] . "' value='" . $so_info ['money'] . "'/></td>";
				}
			}
			$table .= '<td><input type="button" onclick="insert_sod(' . "'" . $v ['code'] . "'," . "'" . $city_id . "'" . ')" style="background:#ffa600; float: initial; margin: 0;" class="query_btn edit" value="定价保存" /></td>';
			$table .= "</tr>";
		}
		$data = array (
				0 => $table 
		);
		$this->ajaxReturn ( $data );
	}
	function insert_sod() {
		$id = $_REQUEST ['id'];
		$money = $_REQUEST ['money'];
		$code = $_REQUEST ['code'];
		$city_id = $_REQUEST ['city_id'];
		$region = M ( "region" );
		$model = M ( "services_order" );
		$so_info = $model->where ( "services_id = '$id' and violation = '$code' and code = '$city_id'" )->find ();
		$data = array (
				"services_id" => $id,
				"code" => $city_id,
				"violation" => $code,
				"money" => $money,
				"create_time" => time () 
		);
		if (empty ( $so_info )) {
			$model->add ( $data );
		} else {
			$model->where ( "id='{$so_info['id']}'" )->save ( $data );
		}
		
		$this->ajaxReturn ( 1 );
	}
	function pinggu() {
		$phone = $_POST ['user_number'];
		$order_start = $_POST ['user_order_start'];
		$order_end = $_POST ['user_order_end'];
		$make_start = $_POST ['user_make_start'];
		$make_end = $_POST ['user_make_end'];
		$mark_start = $_POST ['user_mark_start'];
		$mark_end = $_POST ['user_mark_end'];
		if (IS_POST) {
			$_SESSION ['fuwu'] = '';
			$_SESSION ['fuwu'] ['user_number2'] = $phone;
			$_SESSION ['fuwu'] ['user_order_start2'] = $order_start;
			$_SESSION ['fuwu'] ['user_order_end2'] = $order_end;
			$_SESSION ['fuwu'] ['user_make_start2'] = $make_start;
			$_SESSION ['fuwu'] ['user_make_end2'] = $make_end;
			$_SESSION ['fuwu'] ['user_mark_start2'] = $mark_start;
			$_SESSION ['fuwu'] ['user_mark_end2'] = $mark_end;
		} else {
			$phone = $_SESSION ['fuwu'] ['user_number2'];
			$order_start = $_SESSION ['fuwu'] ['user_order_start2'];
			$order_end = $_SESSION ['fuwu'] ['user_order_end2'];
			$make_start = $_SESSION ['fuwu'] ['user_make_start2'];
			$make_end = $_SESSION ['fuwu'] ['user_make_end2'];
			$mark_start = $_SESSION ['fuwu'] ['user_mark_start2'];
			$mark_end = $_SESSION ['fuwu'] ['user_mark_end2'];
		}
		$this->assign ( "array_post", array (
				$phone,
				$order_start,
				$order_end,
				$make_start,
				$make_end,
				$mark_start,
				$mark_end 
		) );
		$where = "1=1";
		if (! empty ( $phone )) {
			$where .= " and phone like '%$phone%'";
		}
		if (! empty ( $mark_start )) {
			$where .= " and (5-(a.all_nums-a.nums)*0.1 + a.nums*0.1) >= '$mark_start'";
		}
		if (! empty ( $mark_end )) {
			$where .= " and (5-(a.all_nums-a.nums)*0.1 + a.nums*0.1) <= '$mark_end'";
		}
		if (! empty ( $order_start )) {
			$order_start = sprintf ( '%.2f%%', $order_start * 100 );
			$where .= " and concat ( left (a.nums/a.all_nums *100,5),'%') >= '$order_start'";
		}
		if (! empty ( $order_end )) {
			$order_end = sprintf ( '%.2f%%', $order_end * 100 );
			$where .= " and concat ( left (a.nums/a.all_nums *100,5),'%') <= '$order_end'";
		}
		if (! empty ( $make_start )) {
			$where .= " and income_money >= '$make_start'";
		}
		if (! empty ( $make_end )) {
			$where .= " and income_money <= '$make_end'";
		}
		$count = $this->Fuwu_model->table ( "cw_services as a" )->join ( "cw_bank as b on b.bank_id=a.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->Fuwu_model->field ( "@rownum:=@rownum+1 AS iid,b.income_money,a.id,a.phone,a.state,a.create_time,a.all_nums,a.nums,(5-(a.all_nums-a.nums)*0.1 + a.nums*0.1) as grade,concat ( left (a.nums/a.all_nums *100,5),'%') as mod_one,format(a.all_nums/((unix_timestamp(now()) -a.create_time)/24/3600),2) as tuidan,format(a.nums/((unix_timestamp(now()) -a.create_time)/24/3600),2) as jiedan,format(b.income_money/((unix_timestamp(now()) -a.create_time)/24/3600),2) as turnover,a.services_sn" )->table ( "(SELECT @rownum:=0) r,cw_services as a" )->join ( "cw_bank as b on b.bank_id=a.id" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$roles [$k] ['create_time'] = ceil ( (time () - $v ['create_time']) / 3600 / 24 );
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function help() {
		$model = M ( "help" );
		if ($_POST) {
			$info = $model->find ();
			$data = array (
					"url" => $_POST ['url'],
					"c_time" => time () 
			);
			if (empty ( $info )) {
				$model->add ( $data );
			} else {
				$model->where ( "id='{$info['id']}'" )->save ( $data );
			}
		}
		$info = $model->find ();
		$this->assign ( 'info', $info );
		$this->display ();
	}
}
