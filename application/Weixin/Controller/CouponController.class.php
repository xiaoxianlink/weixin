<?php

namespace Weixin\Controller;

use Weixin\Controller\IndexController;
use Think\Log;
use Think\Model;
use Think\Template\Driver\Mobile;

class CouponController extends IndexController {
	
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
		
		$uclist = $this->get_ucoupon ( $user_id);
		
		$car_model = M ();
		$num = $car_model->table ( "cw_user_car as uc" )->join ( "cw_car as c on c.id = uc.car_id" )->field ( "c.*, uc.user_id, uc.id as uc_id" )->where ( "uc.user_id = '$user_id' and uc.is_sub = 0" )->count("uc.id");
		$showAddCarLink = "none";
		if($num == 0){
			$showAddCarLink = "block";
		}
		
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'uuc_list', $uclist );
		$this->assign ( 'showAddCarLink', $showAddCarLink );
		$this->display ( ":ucoupon" );
	}
	
	function get_ucoupon($user_id) {
		// 用户拥有优惠券信息
		$ucoupon_model = M ( "User_coupon" );
		$where = "cw_user_coupon.user_id='$user_id'";
		$where .= " and cw_user_coupon.is_used = 0";
		$where .= " and cw_coupon.start_time <= unix_timestamp(now()) and cw_coupon.expiration_time >= unix_timestamp(now())";
		$ucouponlist = $ucoupon_model->field ( "cw_coupon.*, cw_user_coupon.id as cuc_id" )->join ( "cw_coupon on cw_coupon.id = cw_user_coupon.coupon_id" )->where ( $where )->select ();
		return $ucouponlist;
	}
}