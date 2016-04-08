<?php

namespace Weixin\Controller;
// use Common\Controller\HomeBaseController;
use Weixin\Controller\IndexController;
use Think\Log;

class SubController extends IndexController {
	public function index() {
		if (! isset ( $_GET ['code'] )) {
			$redirect_uri = URL1;
			$scope = 'snsapi_base';
			$log = new Log ();
			$log->write ( "sub请求", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y/m/d' ) . '.log' );
			$this->oauth ( $redirect_uri, $scope );
		} else {
			$code = ( string ) $_GET ['code'];
			$open_id = $this->get_oauth_openid ( $code );
			$log = new Log ();
			$log->write ( "sub微信回调", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y/m/d' ) . '.log' );
		}
		$user_model = M ( "User" );
		$where = array (
				'openid' => ( string ) $open_id 
		);
		$user = $user_model->where ( $where )->find ();
		$user_id = $user ['id'];
		
		$carlist = $this->get_user_car ( $user_id );
		$this->assign ( 'versions', versions );
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'carlist', $carlist );
		$this->display ( ":index" );
	}
	
	// 获取用户车辆信息
	function get_user_car($user_id) {
		$car_model = M ();
		$list = $car_model->table ( "cw_user_car as uc" )->join ( "cw_car as c on c.id = uc.car_id" )->field ( "c.*, uc.user_id, uc.id as uc_id" )->where ( "uc.user_id = '$user_id' and uc.is_sub = 0" )->select ();
		foreach ( $list as $k => $v ) {
			// 车牌号
			$l_num1 = mb_substr ( $v ['license_number'], 0, 2, 'utf-8' );
			$l_num2 = mb_substr ( $v ['license_number'], 2, strlen ( $v ['license_number'] ), 'utf-8' );
			$list [$k] ['license_number'] = $l_num1 . "·" . $l_num2;
			// 车架号
			$f_num1 = substr ( $v ['frame_number'], 0, 3 );
			$f_num2 = substr ( $v ['frame_number'], - 6 );
			$f_count = strlen ( $v ['frame_number'] ) - 9;
			$frame_number = $f_num1;
			for($i = 0; $i < $f_count; $i ++) {
				$frame_number .= '*';
			}
			$frame_number .= $f_num2;
			$list [$k] ['frame_number'] = $frame_number;
			// 发动机号
			$e_num2 = substr ( $v ['engine_number'], - 4 );
			if (strlen ( $v ['engine_number'] ) <= 6) {
				$e_count = strlen ( $v ['engine_number'] ) - 4;
				$engine_number = '';
			} else {
				$e_count = strlen ( $v ['engine_number'] ) - 6;
				$engine_number = substr ( $v ['engine_number'], 0, 2 );
			}
			for($i = 0; $i < $e_count; $i ++) {
				$engine_number .= '*';
			}
			$engine_number .= $e_num2;
			$list [$k] ['engine_number'] = $engine_number;
			if(mb_strlen($v ['scan_state_desc'],'utf-8') > 19){
				$list [$k] ['scan_state_desc'] = mb_substr ( $v ['scan_state_desc'], 0, 16, 'utf-8') . "...";
			}
		}
		return $list;
	}
	/**
	 * 添加车辆
	 */
	public function add_car() {
		$user_id = $_REQUEST ['id'];
		$code = isset ( $_REQUEST ['code'] ) ? $_REQUEST ['code'] : '';
		$license_number = isset ( $_REQUEST ['license_number'] ) ? $_REQUEST ['license_number'] : '';
		$frame_number = isset ( $_REQUEST ['frame_number'] ) ? $_REQUEST ['frame_number'] : '';
		$engine_number = isset ( $_REQUEST ['engine_number'] ) ? $_REQUEST ['engine_number'] : '';
		$abbreviation = isset ( $_REQUEST ['name'] ) ? $_REQUEST ['name'] : '沪A';
		
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'code', $code );
		$this->assign ( 'abbreviation', $abbreviation );
		$this->assign ( 'frame_number', $frame_number );
		$this->assign ( 'engine_number', $engine_number );
		$this->assign ( 'user_id', $user_id );
		$this->display ( ":add_car" );
	}
	public function insert_car() {
		if (IS_POST) {
			$data = array ();
			$data ['license_number'] = $_POST ['license'] . strtoupper ( $_POST ['license_number'] );
			$data ['frame_number'] = strtoupper ( str_replace ( ' ', '', $_POST ['frame_number'] ) );
			$data ['engine_number'] = strtoupper ( $_POST ['engine_number'] );
			$car_model = M ( "Car" );
			$car = $car_model->where ( $data )->find ();
			$uc_model = M ( "User_car" );
			if (! empty ( $car )) {
				$data = array (
						"user_id" => $_POST ['user_id'],
						"car_id" => $car ['id'] 
				);
				$uc = $uc_model->where ( $data )->find ();
				$data ['is_sub'] = 0;
				$data ['create_time'] = time ();
				if (empty ( $uc )) {
					$uc_model->add ( $data );
				} else {
					$uc_model->where ( "id='{$uc['id']}'" )->save ( $data );
				}
			} else {
				$data ['create_time'] = time ();
				$data ['channel'] = 0;
				$car_model->add ( $data );
				$car_id = $car_model->getLastInsID ();
				$data = array (
						"user_id" => $_POST ['user_id'],
						"car_id" => $car_id,
						"is_sub" => 0,
						'create_time' => time () 
				);
				$uc_model->add ( $data );
			}
			$msg ['user_id'] = $_POST ['user_id'];
			$msg ['car_id'] = $data ['car_id'];
			$msg ['license_number'] = $data ['license_number'];
			$this->redirect ( "Sub/take_success", $msg );
		}
	}
	public function take_success() {
		$user_id = $_REQUEST ['user_id'];
		$car_id = $_REQUEST ['car_id'];
		$car_model = M ( "car" );
		$car_info = $car_model->where ( "id='$car_id'" )->find ();
		$license_number = $car_info ['license_number'];
		$user_model = M ( "User" );
		$user = $user_model->where ( "id='$user_id'" )->find ();
		// 查询违章保存信息
		if (! empty ( $user ['city'] )) {
			$this->scan_api ( $car_id, $user ['city'] );
		}
		$l_nums = mb_substr ( $license_number, 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			if ($region ['city'] != $user ['city']) {
				$this->scan_api ( $car_id, $region ['city'] );
			}
		}
		// 查询数据库违章信息
		$endorsement_model = M ( "Endorsement" );
		$where = array (
				"car_id" => $car_id,
				"is_manage" => 0 
		);
		$endorsement = $endorsement_model->field ( "count(*) as nums, sum(points) as all_points, sum(money) as all_money" )->where ( $where )->find ();
		$date = date ( 'Y-m-d' );
		if (! empty ( $endorsement )) {
			if ($endorsement ['nums'] != 0) {
				$data = array (
						'first' => array (
								'value' => urlencode ( "您好，{$license_number}近期违章统计信息如下：" ),
								'color' => "#000000" 
						),
						'keyword1' => array (
								'value' => urlencode ( $license_number ),
								'color' => '#000000' 
						),
						'keyword2' => array (
								'value' => urlencode ( "{$endorsement['nums']}" ),
								'color' => '#000000' 
						),
						'keyword3' => array (
								'value' => urlencode ( "{$endorsement['all_points']}" ),
								'color' => '#000000' 
						),
						'keyword4' => array (
								'value' => urlencode ( "{$endorsement['all_money']}" ),
								'color' => '#000000' 
						),
						'keyword5' => array (
								'value' => urlencode ( $date ),
								'color' => '#000000' 
						),
						'remark' => array (
								'value' => urlencode ( "" ),
								'color' => '#000000' 
						) 
				);
				$this->doSend ( $car_id, $endorsement, $user ['openid'], MUBAN1, URL3 . "&openid=" . $user ['openid'] . "&carid=" . $car_id, $data );
			} else {
				$data = array (
						'first' => array (
								'value' => urlencode ( "您好，{$license_number}近期违章统计信息如下：" ),
								'color' => "#000000" 
						),
						'keyword1' => array (
								'value' => urlencode ( $license_number ),
								'color' => '#000000' 
						),
						'keyword2' => array (
								'value' => urlencode ( "{$endorsement['nums']}" ),
								'color' => '#000000' 
						),
						'keyword3' => array (
								'value' => urlencode ( "0" ),
								'color' => '#000000' 
						),
						'keyword4' => array (
								'value' => urlencode ( "0" ),
								'color' => '#000000' 
						),
						'keyword5' => array (
								'value' => urlencode ( $date ),
								'color' => '#000000' 
						),
						'remark' => array (
								'value' => urlencode ( "" ),
								'color' => '#000000' 
						) 
				);
				$this->doSend ( $car_id, $endorsement, $user ['openid'], MUBAN1, "", $data );
			}
		}
		
		$this->assign ( 'user_id', $user_id );
		$this->display ( ":take_success" );
	}
	public function select_license() {
		$user_id = $_REQUEST ['user_id'];
		$license_number = $_REQUEST ['license_number'];
		$frame_number = $_REQUEST ['frame_number'];
		$engine_number = $_REQUEST ['engine_number'];
		$region_model = M ( "Region" );
		$where = array (
				"level" => 1,
				"is_dredge" => 0 
		);
		$region_list = $region_model->where ( $where )->order ( 'orders' )->select ();
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'frame_number', $frame_number );
		$this->assign ( 'engine_number', $engine_number );
		$this->assign ( 'region_list', $region_list );
		$this->display ( ":select_license" );
	}
	public function select_nums() {
		$name = $_REQUEST ['name'];
		$user_id = $_REQUEST ['user_id'];
		$license_number = $_REQUEST ['license_number'];
		$frame_number = $_REQUEST ['frame_number'];
		$engine_number = $_REQUEST ['engine_number'];
		$region_model = M ( "Region" );
		$where = "level = 2 and is_dredge = 0 and province = '$name' and LENGTH(nums) = 3";
		$region_list = $region_model->where ( $where )->order ( "nums" )->select ();
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'license_number', $license_number );
		$this->assign ( 'province', $name );
		$this->assign ( 'frame_number', $frame_number );
		$this->assign ( 'engine_number', $engine_number );
		$this->assign ( 'region_list', $region_list );
		$this->display ( ":select_nums" );
	}
	// 取消订阅
	public function cancel_car() {
		$id = $_REQUEST ['id'];
		$uc_id = $_REQUEST ['uc_id'];
		$where = array (
				"id" => $uc_id 
		);
		$data = array ();
		$data ['is_sub'] = 1;
		$data ['c_time'] = time ();
		$car_model = M ( "User_car" );
		$car_model->where ( $where )->save ( $data );
		$data = array (
				"id" => $id 
		);
		$this->ajaxReturn ( $data );
	}
}