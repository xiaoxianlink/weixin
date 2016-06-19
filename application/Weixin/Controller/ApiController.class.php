<?php

namespace Weixin\Controller;
use Common\Controller\HomeBaseController;
//use Weixin\Controller\IndexController;
use Think\Log;
require_once "application/Common/getui/IGt.Push.php";

class ApiController extends HomeBaseController {
	
	function index() {
		$this->display();
	}
	
	// 获取Access Token
	function get_access_token() {
		$log = new Log ();
		$wxt_model = M ( "wx_token" ); 
		$wxt = $wxt_model->find (); 
		if (empty ( $wxt )) { 
			$token = $this->__get_access_token (); 
			$log->write ( "token: " . $token, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
			$data = array ( 
				"token" => $token, 
				"c_time" => time () 
			); 
			$wxt_model->add ( $data ); 
			return $token;
		} else {
			if ($wxt ['token'] == '' || $wxt ['token'] == null || $wxt ['c_time'] < (time () - 3600 * 2)) {
				$token = $this->__get_access_token ();
				$log->write ( "token: " . $token, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
				$data = array ( 
					"token" => $token, 
					"c_time" => time () 
				); 
				$wxt_model->where ( "id={$wxt['id']}" )->save ( $data );
			}else {
				$token = $wxt['token'];
				$log->write ( "wxtoken: " . $token, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
			}
			return $token;
		}
	}
	
	function __get_access_token() {
		$appid = APPID;
		$appsecret = APPSECRET;
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$access_token = $jsoninfo ["access_token"];
		return $access_token;
	}
	
	// 获取用户信息
	function get_user_info($openid) {
		$appid = APPID;
		$appsecret = APPSECRET;
		$access_token = $this->get_access_token ();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		return $jsoninfo;
	}
	
	// 发送自定义的模板消息
	public function doSend($id, $endorsement, $touser, $template_id, $url, $data, $topcolor = '#7B68EE') {
		/*
		 * $data = array ( 'first' => array ( 'value' => urlencode ( "您好,您已购买成功" ), 'color' => "#743A3A" ), 'name' => array ( 'value' => urlencode ( "商品信息:微时代电影票" ), 'color' => '#EEEEEE' ), 'remark' => array ( 'value' => urlencode ( '永久有效!密码为:1231313' ), 'color' => '#FFFFFF' ) );
		 */
		$log = new Log ();
		$template = array (
				'touser' => $touser,
				'template_id' => $template_id,
				'url' => $url,
				'topcolor' => $topcolor,
				'data' => $data 
		);
		$json_template = json_encode ( $template );
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->get_access_token ();
		$dataRes = $this->request_post ( $url, urldecode ( $json_template ) );
		if ($id != 0) {
			$data = array (
					"from_userid" => 0,
					"openid" => $touser,
					"msg_type" => 1,
					"tar_id" => $id,
					"create_time" => time (),
					"nums" => $endorsement ['nums'],
					"all_points" => $endorsement ['all_points'],
					"all_money" => $endorsement ['all_money'] 
			);
			$model = M ( "Message" );
			$model->add ( $data );
		}
		$log->write ( serialize ( $dataRes ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		if ($dataRes ['errcode'] == 0) {
			return true;
		} else {
			return false;
		}
	}
	// 网页授权
	function oauth($redirect_uri, $scope, $state = '') {
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APPID . '&redirect_uri=' . urlencode ( $redirect_uri ) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
		header ( "Location:" . $url );
	}
	// 网页授权获取openid
	function get_oauth_openid($code) {
		$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . APPID . '&secret=' . APPSECRET . '&code=' . $code . '&grant_type=authorization_code';
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $get_token_url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$res = curl_exec ( $ch );
		curl_close ( $ch );
		$json_obj = json_decode ( $res, true );
		
		return $json_obj ['openid'];
	}
	/**
	 * 发送post请求
	 *
	 * @param string $url        	
	 * @param string $param        	
	 * @return bool mixed
	 */
	function request_post($url = '', $param = '') {
		if (empty ( $url ) || empty ( $param )) {
			return false;
		}
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init (); // 初始化curl
		curl_setopt ( $ch, CURLOPT_URL, $postUrl ); // 抓取指定网页
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 设置header
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_POST, 1 ); // post提交方式
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curlPost );
		$data = curl_exec ( $ch ); // 运行curl
		curl_close ( $ch );
		return $data;
	}
	
	// 发送自定义的模板消息
	public function sendWeiXin($touser, $template_id, $url, $data, $topcolor = '#7B68EE') {
		$log = new Log ();
		$template = array (
				'touser' => $touser,
				'template_id' => $template_id,
				'url' => $url,
				'topcolor' => $topcolor,
				'data' => $data 
		);
		$json_template = json_encode ( $template );
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->get_access_token ();
		$dataRes = $this->request_post ( $url, urldecode ( $json_template ) );
		$log->write ( "sendWeixin: " . $json_template, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "sendWeixin: " . serialize ( $dataRes ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		if ($dataRes ['errcode'] == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function push_weizhang(){
		$car_id = $_REQUEST['car_id'];
		$end_id = $_REQUEST['end_id'];
		$this->push($car_id, $end_id);
	}
	
	// 推送
	function push($car_id, $end_id) {
		$log = new Log ();
		$log->write ( "send---------------------------$end_id", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$end_model = M ( "endorsement" );
		$end_info = $end_model->where ( "id='$end_id'" )->find ();
		if (empty ( $end_info )) {
			return false;
		}
		$log->write ( "senddata---------------------------" . json_encode ( $end_info ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$car_model = M ( "Car" );
		$car_info = $car_model->where ( "id='$car_id'" )->find ();
		$user_model = M ();
		$user = $user_model->table ( "cw_user as u" )->join ( "cw_user_car as uc on uc.user_id = u.id" )->field ( "u.id, u.openid, u.nickname, u.channel, u.channel_key" )->where ( "uc.car_id='$car_id' and uc.is_sub = 0 and u.is_att = 0" )->select ();
		$date = date ( "Y年m月d日 H:i", $end_info ['time'] );
		foreach ( $user as $p ) {
			if($p['channel'] == 0){
				$data = array (
					'first' => array (
							'value' => urlencode ( "{$p['nickname']}，{$car_info ['license_number']}有一条新违章" ),
							'color' => "#000000" 
					),
					'violationTime' => array (
							'value' => urlencode ( $date ),
							'color' => '#000000' 
					),
					'violationAddress' => array (
							'value' => urlencode ( "{$end_info['address']}" ),
							'color' => '#000000' 
					),
					'violationType' => array (
							'value' => urlencode ( "{$end_info['content']}" ),
							'color' => '#000000' 
					),
					'violationFine' => array (
							'value' => urlencode ( "{$end_info['money']}" ),
							'color' => '#000000' 
					),
					'violationPoints' => array (
							'value' => urlencode ( "{$end_info['points']}" ),
							'color' => '#000000' 
					),
					'remark' => array (
							'value' => urlencode ( "" ),
							'color' => '#000000' 
					) 
				);
				$this->sendWeiXin ($p ['openid'], MUBAN2, URL3 . "&openid=" . $p ['openid'] . "&carid=" . $car_id . "&end_id=" . $end_id, $data);
				
				$data = array (
					"from_userid" => 0,
					"openid" => $p ['openid'],
					"tar_id" => $end_info ['id'],
					"create_time" => time (),
					"msg_type" => 2,
					"nums" => 1,
					"all_points" => $end_info ['points'],
					"all_money" => $end_info ['money'] 
				);
				$model = M ( "Message" );
				$model->add ( $data );
			}
		}
	}
	
	function __push_wzstats($user, $car_id, $license_number, $endorsement){
		if (! empty ( $endorsement )) {
			$date = date ( 'Y-m-d' );
			$url = "";
			if ($endorsement ['nums'] != 0) {
				$url = URL3 . "&openid=" . $user ['openid'] . "&carid=" . $car_id;
			}
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
			$this->doSend ( $car_id, $endorsement, $user ['openid'], MUBAN1, $url, $data );
		}
	}
	
	function __push_error($user, $car_id, $license_number, $error){
		$date = date ( 'Y-m-d' );
		$data = array (
			'first' => array (
					'value' => urlencode ( "您好，{$license_number}的车辆信息有误，请检查。" ),
					'color' => "#000000" 
			),
			'keyword1' => array (
					'value' => urlencode ( $license_number ),
					'color' => '#000000' 
			),
			'keyword2' => array (
					'value' => urlencode ( "0" ),
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
					'value' => urlencode ( "停查原因：{$error}" ),
					'color' => '#FF0000' 
			) 
		);
		$this->doSend ( $v ['car_id'], null, $user ['openid'], MUBAN1, URL1, $data );
	}
	
	function push_confirm(){
		$end_id = $_REQUEST['end_id'];
		$this->__push_confirm($end_id);
	}
	
	// 推送
	function __push_confirm($end_id) {
		// 推送消息
		$model = M ();
		$end = $model->table ( "cw_endorsement as e" )->join ( "cw_car as c on c.id = e.car_id" )->field("e.*, c.license_number")->where("e.id = '$end_id'")->find();
		$car_id = $end["car_id"];
		$user_model = M ();
		$users = $user_model->table ( "cw_user as u" )->join ( "cw_user_car as uc on uc.user_id = u.id" )->field ( "u.id, u.openid, u.nickname, u.channel, u.channel_key" )->where ( "uc.car_id='$car_id' and uc.is_sub = 0 and u.is_att = 0" )->select ();
		foreach ( $users as $u ) {
			if($u["channel"] == 0){
				$end_model = M ("endorsement");
				$unman_cnt = $end_model->where("car_id = $car_id and is_manage = 0")->count();
				
				$date = date ( "Y年m月d日 H:i", $end ['time'] );
				$data = array (
						'first' => array (
								'value' => urlencode ( "{$u['nickname']}，你有一条违章处罚已处理完成：" ),
								'color' => "#000000" 
						),
						'keyword1' => array (
								'value' => urlencode ( "{$end['license_number']}"),
								'color' => '#000000' 
						),
						'keyword2' => array (
								'value' => urlencode ( $unman_cnt ),
								'color' => '#000000' 
						),
						'remark' => array (
								'value' => urlencode ( 
									  "违法地点 : {$end['city']}{$end['address']}\\n" 
									. "违法时间：$date \\n"
									. "违法扣分：{$end['points']} \\n"
									. "罚款金额：{$end['money']}元\\n"
									. "该处罚已被当地交管局受理完成。如有疑问，请咨询当地公安机关交通管理窗口。"
									),
								'color' => '#000000' 
						) 
				);
				$this->sendWeiXin ($u['openid'], MUBAN4, "", $data );
				
			}
		}
	}
	
	function push_order(){
		$end_id = $_REQUEST['end_id'];
		$this->__push_order($end_id);
	}
	
	// 推送
	function __push_order($end_id) {
		// 推送消息
		$model = M ();
		$result = $model->table ( "cw_order as o" )->join ( "cw_user as u on u.id=o.user_id" )->join ( "cw_car as c on c.id=o.car_id" )->field ( "u.openid, u.channel, o.order_sn, o.services_id, c.license_number" )->where ( "o.endorsement_id = '$end_id' and u.is_att = 0" )->find ();
		if (! empty ( $result )) {
			if($result["channel"] == 0){
				$data = array (
						'first' => array (
								'value' => urlencode ( first_key ),
								'color' => "#000000" 
						),
						'keyword1' => array (
								'value' => urlencode ( "{$result ['order_sn']}" ),
								'color' => '#000000' 
						),
						'keyword2' => array (
								'value' => urlencode ( "{$result['license_number']}" ),
								'color' => '#000000' 
						),
						'keyword3' => array (
								'value' => urlencode ( status2 ),
								'color' => '#000000' 
						),
						'remark' => array (
								'value' => urlencode ( last_key ),
								'color' => '#000000' 
						) 
				);
				$this->sendWeiXin ($result['openid'], MUBAN3, URL2, $data );
				/*
				$data = array (
					"from_userid" => 0,
					"openid" => $p ['openid'],
					"tar_id" => $end_info ['id'],
					"create_time" => time (),
					"msg_type" => 2,
					"nums" => 1,
					"all_points" => 0,
					"all_money" => $end_info ['money'] 
				);
				$model = M ( "Message" );
				$model->add ( $data );
				*/
			}
		}
		
		/*start 增加订单结算提醒推送 */
		$model_services = M ("services");
		$services = $model_services->where ("id = '{$result['services_id']}'")->find();
		$content = sprintf(content7, $result['order_sn']);
		$title = title7;
		$tz_content = sprintf(content7, $result['order_sn']);
		$this->pushMessageToSingle($content, $title,$tz_content,$services['phone']);
		//插入消息表
		$this->add_message($services['id'], 3, 7, '', $content);
		/* end */
	}
	
	/* start 增加推送方法*/
	
	//单推接口案例（个推）
	function pushMessageToSingle($content,$title,$tz_content,$alias){
	
	    $igt = new \IGeTui(NULL,APPKEY,MASTERSECRET,false);
	
	    $template = $this->IGtNotificationTemplateDemo($content,$title,$tz_content);
	
	    //个推信息体
	    $message = new \IGtSingleMessage();
	
	    $message->set_isOffline(true);//是否离线
	    $message->set_offlineExpireTime(3600*12*1000);//离线时间
	    $message->set_data($template);//设置推送消息类型
	    //接收方
	    $target = new \IGtTarget();
	    $target->set_appId(appid);
	    $target->set_alias($alias);
	
	    try {
	        $rep = $igt->pushMessageToSingle($message, $target);
	
	    }catch(\RequestException $e){
	        $requstId =e.getRequestId();
	        $rep = $igt->pushMessageToSingle($message, $target,$requstId);
	    }
	
	}
	//群推接口案例
	function pushMessageToApp($content, $title,$tz_content){
	    $igt = new \IGeTui(HOST,APPKEY,MASTERSECRET);
	    $template = $this->IGtNotificationTemplateDemo($content,$title,$tz_content);
	    //个推信息体
	    //基于应用消息体
	    $message = new \IGtAppMessage();
	    $message->set_isOffline(true);
	    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
	    $message->set_data($template);
	
	    $appIdList=array(appid);
	    $phoneTypeList=array('ANDROID');
	
	    $message->set_appIdList($appIdList);
	
	    $igt->pushMessageToApp($message);
	
	}
	
	function IGtNotificationTemplateDemo($content,$title,$tz_content){
	    $template =  new \IGtNotificationTemplate();
	    $template->set_appId(appid);//应用appid
	    $template->set_appkey(APPKEY);//应用appkey
	    $template->set_transmissionType(1);//透传消息类型
	    $template->set_transmissionContent($content);//透传内容
	    $template->set_title($title);//通知栏标题
	    $template->set_text($tz_content);//通知栏内容
	    $template->set_logo("");//通知栏logo
	    $template->set_isRing(true);//是否响铃
	    $template->set_isVibrate(true);//是否震动
	    $template->set_isClearable(true);//通知栏是否可清除
	    return $template;
	}
	/**
	 * 插入消息表
	 */
	public function add_message($services_id, $msg_type, $tixing_type, $zhangwu_type, $content){
	    $model_msg = M ("message");
	    $data = array(
	        'from_userid'=>0,
	        'openid'=>$services_id,
	        'msg_type'=>$msg_type,
	        'tixing_type'=>$tixing_type,
	        'zhangwu_type'=>$zhangwu_type,
	        'create_time'=>time(),
	        'content'=>$content,
	        'is_readed'=>0
	    );
	    $model_msg->add($data);
	}
	/* end */
	
}