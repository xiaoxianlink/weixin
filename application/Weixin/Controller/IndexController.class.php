<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Weixin\Controller;

use Common\Controller\HomeBaseController;
use Think\Log;

class IndexController extends HomeBaseController {
	public function index() {
		if (! isset ( $_GET ['echostr'] )) {
			$this->responseMsg ();
		} else {
			$this->valid ();
		}
	}
	// 验证签名
	public function valid() {
		$echoStr = $_GET ["echostr"];
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		$token = TOKEN;
		$tmpArr = array (
				$token,
				$timestamp,
				$nonce 
		);
		sort ( $tmpArr, SORT_STRING );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		if ($tmpStr == $signature) {
			echo $echoStr;
			exit ();
		}
	}
	
	// 响应消息
	public function responseMsg() {
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		if (! empty ( $postStr )) {
			// $this->logger ( "R \r\n" . $postStr );
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			$RX_TYPE = trim ( $postObj->MsgType );
			
			if (($postObj->MsgType == "event") && ($postObj->Event == "subscribe" || $postObj->Event == "unsubscribe")) {
				// 过滤关注和取消关注事件
			} else {
			}
			
			// 消息类型分离
			switch ($RX_TYPE) {
				case "event" :
					$result = $this->receiveEvent ( $postObj );
					break;
				case "text" :
					/* if (strstr ( $postObj->Content, "第三方" )) {
						$result = $this->relayPart3 ( "http://www.fangbei.org/test.php" . '?' . $_SERVER ['QUERY_STRING'], $postStr );
					} else {
						$result = $this->receiveText ( $postObj );
					} */
					break;
				case "image" :
					// $result = $this->receiveImage ( $postObj );
					break;
				case "location" :
					// $result = $this->receiveLocation ( $postObj );
					break;
				case "voice" :
					// $result = $this->receiveVoice ( $postObj );
					break;
				case "video" :
					// $result = $this->receiveVideo ( $postObj );
					break;
				case "link" :
					// $result = $this->receiveLink ( $postObj );
					break;
				default :
					// $result = "unknown msg type: " . $RX_TYPE;
					break;
			}
			// $this->logger ( "T \r\n" . $result );
			echo $result;
		} else {
			echo "";
			exit ();
		}
	}
	
	// 接收事件消息
	private function receiveEvent($object) {
		$content = "";
		switch ($object->Event) {
			case "subscribe" :
				$content = "谢谢关注小仙车务 ";
				$this->register ( $object->FromUserName );
				// $content .= (! empty ( $object->EventKey )) ? ("\n来自二维码场景 " . str_replace ( "qrscene_", "", $object->EventKey )) : "";
				break;
			case "unsubscribe" :
				$content = "取消关注";
				$this->logout ( $object->FromUserName );
				break;
			case "CLICK" :
				switch ($object->EventKey) {
					case "scanning" : // 违章扫描
						$content = $this->scanning ( $object->FromUserName );
						if ($content == '1') {
							exit ();
						} else if ($content == '201') {
							$content = "请" . scan_time . "秒后重新查询";
						}
						break;
					default :
						$content = "点击菜单：" . $object->EventKey;
						break;
				}
				break;
			
			case "VIEW" :
				$content = "跳转链接 " . $object->EventKey;
				break;
			case "SCAN" :
				$content = "扫描场景 " . $object->EventKey;
				break;
			case "LOCATION" :
				$content = $this->insert_city ( $object );
				break;
			case "scancode_waitmsg" :
				if ($object->ScanCodeInfo->ScanType == "qrcode") {
					$content = "扫码带提示：类型 二维码 结果：" . $object->ScanCodeInfo->ScanResult;
				} else if ($object->ScanCodeInfo->ScanType == "barcode") {
					$codeinfo = explode ( ",", strval ( $object->ScanCodeInfo->ScanResult ) );
					$codeValue = $codeinfo [1];
					$content = "扫码带提示：类型 条形码 结果：" . $codeValue;
				} else {
					$content = "扫码带提示：类型 " . $object->ScanCodeInfo->ScanType . " 结果：" . $object->ScanCodeInfo->ScanResult;
				}
				break;
			case "scancode_push" :
				$content = "扫码推事件";
				break;
			case "pic_sysphoto" :
				$content = "系统拍照";
				break;
			case "pic_weixin" :
				$content = "相册发图：数量 " . $object->SendPicsInfo->Count;
				break;
			case "pic_photo_or_album" :
				$content = "拍照或者相册：数量 " . $object->SendPicsInfo->Count;
				break;
			case "location_select" :
				$content = "发送位置：标签 " . $object->SendLocationInfo->Label;
				break;
			
			default :
				$content = "receive a new event: " . $object->Event;
				break;
		}
		
		if (is_array ( $content )) {
			if (isset ( $content [0] ['PicUrl'] )) {
				$result = $this->transmitNews ( $object, $content );
			} else if (isset ( $content ['MusicUrl'] )) {
				$result = $this->transmitMusic ( $object, $content );
			}
		} else {
			$result = $this->transmitText ( $object, $content );
		}
		return $result;
	}
	
	// 接收文本消息
	private function receiveText($object) {
		$keyword = trim ( $object->Content );
		// 多客服人工回复模式
		if (strstr ( $keyword, "请问在吗" ) || strstr ( $keyword, "在线客服" )) {
			$result = $this->transmitService ( $object );
			return $result;
		}
		
		// 自动回复模式
		if (strstr ( $keyword, "文本" )) {
			$content = "这是个文本消息";
		} else if (strstr ( $keyword, "表情" )) {
			$content = "中国：" . $this->bytes_to_emoji ( 0x1F1E8 ) . $this->bytes_to_emoji ( 0x1F1F3 ) . "\n仙人掌：" . $this->bytes_to_emoji ( 0x1F335 );
		} else if (strstr ( $keyword, "单图文" )) {
			$content = array ();
			$content [] = array (
					"Title" => "单图文标题",
					"Description" => "单图文内容",
					"PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
					"Url" => "http://m.cnblogs.com/?u=txw1958" 
			);
		} else if (strstr ( $keyword, "图文" ) || strstr ( $keyword, "多图文" )) {
			$content = array ();
			$content [] = array (
					"Title" => "多图文1标题",
					"Description" => "",
					"PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
					"Url" => "http://m.cnblogs.com/?u=txw1958" 
			);
			$content [] = array (
					"Title" => "多图文2标题",
					"Description" => "",
					"PicUrl" => "http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg",
					"Url" => "http://m.cnblogs.com/?u=txw1958" 
			);
			$content [] = array (
					"Title" => "多图文3标题",
					"Description" => "",
					"PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg",
					"Url" => "http://m.cnblogs.com/?u=txw1958" 
			);
		} else if (strstr ( $keyword, "音乐" )) {
			$content = array ();
			$content = array (
					"Title" => "最炫民族风",
					"Description" => "歌手：凤凰传奇",
					"MusicUrl" => "http://121.199.4.61/music/zxmzf.mp3",
					"HQMusicUrl" => "http://121.199.4.61/music/zxmzf.mp3" 
			);
		} else {
			$content = date ( "Y-m-d H:i:s", time () ) . "\nOpenID：" . $object->FromUserName . "\n技术支持 睿风";
		}
		
		if (is_array ( $content )) {
			if (isset ( $content [0] )) {
				$result = $this->transmitNews ( $object, $content );
			} else if (isset ( $content ['MusicUrl'] )) {
				$result = $this->transmitMusic ( $object, $content );
			}
		} else {
			$result = $this->transmitText ( $object, $content );
		}
		return $result;
	}
	
	// 接收图片消息
	private function receiveImage($object) {
		$content = array (
				"MediaId" => $object->MediaId 
		);
		$result = $this->transmitImage ( $object, $content );
		return $result;
	}
	
	// 接收位置消息
	private function receiveLocation($object) {
		$content = "你发送的是位置，经度为：" . $object->Location_Y . "；纬度为：" . $object->Location_X . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
		$result = $this->transmitText ( $object, $content );
		return $result;
	}
	
	// 接收语音消息
	private function receiveVoice($object) {
		if (isset ( $object->Recognition ) && ! empty ( $object->Recognition )) {
			$content = "你刚才说的是：" . $object->Recognition;
			$result = $this->transmitText ( $object, $content );
		} else {
			$content = array (
					"MediaId" => $object->MediaId 
			);
			$result = $this->transmitVoice ( $object, $content );
		}
		return $result;
	}
	
	// 接收视频消息
	private function receiveVideo($object) {
		$content = array (
				"MediaId" => $object->MediaId,
				"ThumbMediaId" => $object->ThumbMediaId,
				"Title" => "",
				"Description" => "" 
		);
		$result = $this->transmitVideo ( $object, $content );
		return $result;
	}
	
	// 接收链接消息
	private function receiveLink($object) {
		$content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
		$result = $this->transmitText ( $object, $content );
		return $result;
	}
	
	// 回复文本消息
	private function transmitText($object, $content) {
		if (! isset ( $content ) || empty ( $content )) {
			return "";
		}
		
		$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time (), $content );
		
		return $result;
	}
	
	// 回复图文消息
	private function transmitNews($object, $newsArray) {
		if (! is_array ( $newsArray )) {
			return "";
		}
		$itemTpl = "        <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
        </item>
";
		$item_str = "";
		foreach ( $newsArray as $item ) {
			$item_str .= sprintf ( $itemTpl, $item ['Title'], $item ['Description'], $item ['PicUrl'], $item ['Url'] );
		}
		$xmlTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>%s</ArticleCount>
		<Articles>
		$item_str    </Articles>
		</xml>";
		
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time (), count ( $newsArray ) );
		return $result;
	}
	
	// 回复音乐消息
	private function transmitMusic($object, $musicArray) {
		if (! is_array ( $musicArray )) {
			return "";
		}
		$itemTpl = "<Music>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";
		
		$item_str = sprintf ( $itemTpl, $musicArray ['Title'], $musicArray ['Description'], $musicArray ['MusicUrl'], $musicArray ['HQMusicUrl'] );
		
		$xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        $item_str
        </xml>";
		
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
		return $result;
	}
	
	// 回复图片消息
	private function transmitImage($object, $imageArray) {
		$itemTpl = "<Image>
	<MediaId><![CDATA[%s]]></MediaId>
	</Image>";
		
		$item_str = sprintf ( $itemTpl, $imageArray ['MediaId'] );
		
		$xmlTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[image]]></MsgType>
	$item_str
	</xml>";
		
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
		return $result;
	}
	
	// 回复语音消息
	private function transmitVoice($object, $voiceArray) {
		$itemTpl = "<Voice>
	<MediaId><![CDATA[%s]]></MediaId>
	</Voice>";
		
		$item_str = sprintf ( $itemTpl, $voiceArray ['MediaId'] );
		$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
    </xml>";
		
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
		return $result;
	}
	
	// 回复视频消息
	private function transmitVideo($object, $videoArray) {
		$itemTpl = "<Video>
	<MediaId><![CDATA[%s]]></MediaId>
	<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
	<Title><![CDATA[%s]]></Title>
	<Description><![CDATA[%s]]></Description>
    </Video>";
		
		$item_str = sprintf ( $itemTpl, $videoArray ['MediaId'], $videoArray ['ThumbMediaId'], $videoArray ['Title'], $videoArray ['Description'] );
		
		$xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    		<CreateTime>%s</CreateTime>
    		<MsgType><![CDATA[video]]></MsgType>
    		$item_str
    		</xml>";
		
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
		return $result;
	}
	
	// 回复多客服消息
	private function transmitService($object) {
		$xmlTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[transfer_customer_service]]></MsgType>
		</xml>";
		$result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
		return $result;
	}
	
	// 回复第三方接口消息
	private function relayPart3($url, $rawData) {
		$headers = array (
				"Content-Type: text/xml; charset=utf-8" 
		);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $rawData );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
	}
	
	// 字节转Emoji表情
	function bytes_to_emoji($cp) {
		if ($cp > 0x10000) { // 4 bytes
			return chr ( 0xF0 | (($cp & 0x1C0000) >> 18) ) . chr ( 0x80 | (($cp & 0x3F000) >> 12) ) . chr ( 0x80 | (($cp & 0xFC0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
		} else if ($cp > 0x800) { // 3 bytes
			return chr ( 0xE0 | (($cp & 0xF000) >> 12) ) . chr ( 0x80 | (($cp & 0xFC0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
		} else if ($cp > 0x80) { // 2 bytes
			return chr ( 0xC0 | (($cp & 0x7C0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
		} else { // 1 byte
			return chr ( $cp );
		}
	}
	
	// 日志记录
	/*
	 * private function logger($log_content) { if (isset ( $_SERVER ['HTTP_APPNAME'] )) { // SAE sae_set_display_errors ( false ); sae_debug ( $log_content ); sae_set_display_errors ( true ); } else if ($_SERVER ['REMOTE_ADDR'] != "127.0.0.1") { // LOCAL $max_size = 1000000; $log_filename = "log.xml"; if (file_exists ( $log_filename ) and (abs ( filesize ( $log_filename ) ) > $max_size)) { unlink ( $log_filename ); } file_put_contents ( $log_filename, date ( 'Y-m-d H:i:s' ) . " " . $log_content . "\r\n", FILE_APPEND ); } }
	 */
	
	// 获取Access Token
	function get_access_token() {
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
					"from_user_id" => 0,
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
	
	/**
	 * 以下数据库操作 *
	 */
	// 关注
	function register($openid) {
		$user_model = D ( "User" );
		$data = array ();
		$data ['openid'] = ( string ) $openid;
		$user = $user_model->field ( 'id' )->where ( $data )->find ();
		
		if (! empty ( $user )) {
			$data1 ['is_att'] = 0;
			$user_model->where ( $data )->save ( $data1 );
		} else {
			$user_info = $this->get_user_info ( $openid );
			$data ['group_id'] = $user_info ['groupid'];
			$data ['unionid'] = ( string ) $user_info ['unionid'];
			$data ['username'] = $user_info ['nickname'];
			$data ['nickname'] = $user_info ['nickname'];
			$data ['is_att'] = 0;
			$data ['create_time'] = time ();
			$data ['channel'] = 0;
			$user_model->add ( $data );
		}
	}
	// 取消关注
	function logout($openid) {
		$user_model = M ( "User" );
		$data = array ();
		$data ['openid'] = ( string ) $openid;
		$user = $user_model->field ( 'id' )->where ( $data )->find ();
		$log = new Log ();
		$log->write ( $data ['open_id'], 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		if (! empty ( $user )) {
			$data1 ['is_att'] = 1;
			$user_model->where ( $data )->save ( $data1 );
		}
	}
	// 保存用户现在的地理位置
	function insert_city($object) {
		$open_id = $object->FromUserName;
		$lat = $object->Latitude;
		$lng = $object->Longitude;
		$url = "http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=$lng&y=$lat";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$lat = base64_decode ( $jsoninfo ['y'] );
		$lng = base64_decode ( $jsoninfo ['x'] );
		
		$url = "http://api.map.baidu.com/geocoder?location=$lat,$lng&output=json&key=" . bdkey;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$city = $jsoninfo ['result'] ['addressComponent'] ['city'];
		$user_model = M ( "User" );
		$data = array (
				"city" => substr ( $city, 0, - 1 ) 
		);
		$user_model->where ( "openid='$open_id'" )->save ( $data );
	}
	// 违章扫描
	function scanning($openid) {
		$user_model = M ( "User" );
		$data ['openid'] = ( string ) $openid;
		$user = $user_model->field ( 'id' )->where ( $data )->find ();
		$msg = "1";
		if (! empty ( $user )) {
			// 查询记录
			$sr_model = M ( "Scan_record" );
			$sr = $sr_model->where ( "user_id = {$user['id']}" )->find ();
			if (empty ( $sr )) {
				$data = array (
						"user_id" => $user ['id'],
						"create_time" => time (),
						"nums" => 1 
				);
				$sr_model->add ( $data );
			} else {
				if (time () > $sr ['create_time'] + scan_time) {
					$data = array (
							"create_time" => time (),
							"nums" => $sr ['nums'] + 1 
					);
					$sr_model->where ( "user_id = {$user['id']}" )->save ( $data );
				} else {
					return 201;
				}
			}
			
			$car_model = M ();
			$car = $car_model->table ( "cw_user_car as uc" )->join ( "cw_car as c on c.id = uc.car_id" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "c.*, uc.user_id, uc.car_id, u.city" )->where ( "uc.user_id = '{$user ['id']}' and uc.is_sub = 0" )->select ();
			foreach ( $car as $k => $v ) {
				// 查询违章保存信息
				if ($v ['city'] != null && $v ['city'] != '') {
					$this->scan_api ( $v ['car_id'], $v ['city'] );
				}
				$l_nums = mb_substr ( $v ['license_number'], 0, 2, 'utf-8' );
				$region_model = M ( "Region" );
				$region = $region_model->where ( "nums = '$l_nums'" )->find ();
				if (! empty ( $region )) {
					if ($v ['city'] != $region ['city']) {
						$this->scan_api ( $v ['car_id'], $region ['city'] );
					}
				}
				
				// 查询数据库违章信息
				$endorsement_model = M ( "Endorsement" );
				$where = array (
						"car_id" => $v ['car_id'],
						"is_manage" => 0 
				);
				$endorsement = $endorsement_model->field ( "count(*) as nums, sum(points) as all_points, sum(money) as all_money" )->where ( $where )->find ();
				$date = date ( 'Y-m-d' );
				if (! empty ( $endorsement )) {
					if ($endorsement ['nums'] != 0) {
						$data = array (
								'first' => array (
										'value' => urlencode ( "您好，{$v ['license_number']}近期违章统计信息如下：" ),
										'color' => "#000000" 
								),
								'keyword1' => array (
										'value' => urlencode ( "{$v ['license_number']}" ),
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
						$this->doSend ( $v ['car_id'], $endorsement, ( string ) $openid, MUBAN1, URL3 . "&openid=" . ( string ) $openid . "&carid=" . $v ['car_id'], $data );
					} else {
						$data = array (
								'first' => array (
										'value' => urlencode ( "您好，{$v ['license_number']}近期违章统计信息如下：" ),
										'color' => "#000000" 
								),
								'keyword1' => array (
										'value' => urlencode ( "{$v ['license_number']}" ),
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
						$this->doSend ( $v ['car_id'], $endorsement, ( string ) $openid, MUBAN1, "", $data );
					}
				}
			}
			if (count ( $car ) == 0) {
				$url = URL1;
				$msg = "您还没有订阅车辆，请点击（<a href='$url'>违章订阅</a>），订阅车辆后，我们会定期自动帮您扫描交警数据库的违章信息，及时发送给你。";
			}
			return $msg;
		}
		
		return $msg;
	}
	// 车首页接口
	function scan_api($car_id, $city, $type = 1) {
		$log = new Log ();
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		$region_model = M ( "Region" );
		if ($type == 1) {
			$region = $region_model->where ( "city = '$city'" )->find ();
		} else {
			$region = $region_model->where ( "province = '$city' and level = 1" )->find ();
		}
		
		// 车首页查询
		$app_id = app_id;
		$app_key = app_key;
		$engineLen = $region ['c_engine_nums'];
		if ($engineLen > 0) {
			$engine_number = substr ( $car ['engine_number'], - $engineLen );
		} else {
			$engine_number = $car ['engine_number'];
		}
		$frameLen = $region ['c_frame_nums'];
		if ($frameLen > 0) {
			$frame_number = substr ( $car ['frame_number'], - $frameLen );
		} else {
			$frame_number = $car ['frame_number'];
		}
		$car = "{hphm={$car['license_number']}&classno={$frame_number}&engineno={$engine_number}&city_id={$region['code']}&car_type=02}";
		$car_info = urlencode ( $car );
		$time = time ();
		$sign = md5 ( $app_id . $car . $time . $app_key );
		$url = "http://www.cheshouye.com/api/weizhang/query_task?car_info=$car_info&sign=$sign&timestamp=$time&app_id=$app_id";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$log->write ( "请求参数：" . $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "返回参数：" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$endorsement_model = M ( "Endorsement" );
		$log_model = M ( "Endorsement_log" );
		// 保存车首页查询信息
		$ids = "0";
		$jilu_model = M ( "endorsement_jilu" );
		$jilu_data = array (
				"car_id" => $car_id,
				"city" => $city,
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => 'cheshouye.com',
				"code" => $jsoninfo ['status'],
				"state" => 1 
		);
		$jilu_id = $jilu_model->add ( $jilu_data );
		if ($jsoninfo ['status'] == 2001) {
			foreach ( $jsoninfo ['historys'] as $v ) {
				$jilu_data ['all_nums'] ++;
				$jilu_data ['money'] += $v ['money'];
				$jilu_data ['points'] += $v ['fen'];
				$time = strtotime ( $v ['occur_date'] );
				$endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time'" )->find ();
				if (empty ( $endorsement )) {
					$city = isset ( $v ['city_name'] ) ? $v ['city_name'] : $city;
					$data = array (
							"car_id" => $car_id,
							"area" => $city,
							"query_port" => csyapi,
							"code" => $v ['code'],
							"time" => $time,
							"money" => $v ['money'],
							"points" => $v ['fen'],
							"address" => $v ['occur_area'],
							"content" => $v ['info'],
							"create_time" => time (),
							"manage_time" => time (),
							"query_no" => $jilu_id,
							// "certificate_no" => $v ['archive'],
							"office" => $v ['officer'] 
					);
					$endorsement_model->add ( $data );
					$jilu_data ['add_nums'] ++;
					$data = array (
							"end_id" => $endorsement_model->getLastInsID (),
							"state" => 1,
							"c_time" => time (),
							"type" => 0 
					);
					$log_model->add ( $data );
				}
			}
			$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
		} elseif ($jsoninfo ['status'] == 2000) {
		} else {
			$jilu_data = array (
					"car_id" => $car_id,
					"city" => $city,
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => 'cheshouye.com',
					"code" => $jsoninfo ['status'],
					"state" => 1
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			
			$jsoninfo = $this->get_endorsement ( $car_id, $city );
			$jilu_data ['code'] = $jsoninfo ['code'];
			$jilu_data ['port'] = "http://120.26.57.239/api/";
			if ($jsoninfo ['code'] == '0') {
				foreach ( $jsoninfo ['data'] [0] ['result'] as $v ) {
					$v ['violationPrice'] = isset($v ['violationPrice']) ? $v ['violationPrice'] : 0;
					$v ['violationMark'] = isset($v ['violationMark']) ? $v ['violationMark'] : '-1';
					$v ['violationTime'] = isset($v ['violationTime']) ? $v ['violationTime'] : '-1';
					$v ['violationCode'] = isset($v ['violationCode']) ? $v ['violationCode'] : 0;
					$v ['violationAddress'] = isset($v ['violationAddress']) ? $v ['violationAddress'] : '-1';
					$v ['violationDesc'] = isset($v ['violationDesc']) ? $v ['violationDesc'] : '-1';
					if ($v ['violationPrice'] != 0 && $v ['violationMark'] != '-1' && $v ['violationTime'] != '-1' && $v ['violationCode'] != 0 && $v ['violationAddress'] != '-1' && $v ['violationDesc'] != '-1') {
						$v ['violationPrice'] = $v ['violationPrice'] / 100;
						$jilu_data ['all_nums'] ++;
						$jilu_data ['money'] += $v ['violationPrice'];
						$jilu_data ['points'] += $v ['violationMark'];
						$time = strtotime ( $v ['violationTime'] );
						$endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time'" )->find ();
						if (empty ( $endorsement )) {
							$city = isset ( $v ['violationCity'] ) ? $v ['violationCity'] : $city;
							$data = array (
									"car_id" => $car_id,
									"area" => $city,
									"query_port" => acfapi,
									"code" => $v ['violationCode'],
									"time" => $time,
									"money" => $v ['violationPrice'],
									"points" => $v ['violationMark'],
									"address" => $v ['violationAddress'],
									"content" => $v ['violationDesc'],
									"create_time" => time (),
									"manage_time" => time (),
									"query_no" => $jilu_id,
									// "certificate_no" => $v ['archive'],
									"office" => $v ['officeName']
							);
							$endorsement_model->add ( $data );
							$jilu_data ['add_nums'] ++;
							$data = array (
									"end_id" => $endorsement_model->getLastInsID (),
									"state" => 1,
									"c_time" => time (),
									"type" => 0
							);
							$log_model->add ( $data );
						}
					}
				}
			}
			$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
		}
		$data = array (
				"last_time" => time () 
		);
		$car_model->where ( "id = '$car_id'" )->save ( $data );
	}
	// 爱车坊接口
	function get_endorsement($car_id, $city) {
		$log = new Log ();
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		$region_model = M ( "Region" );
		$region = $region_model->where ( "city = '$city'" )->find ();
		/*
		 * $acf_model = M ( "Acf_token" ); $acf = $acf_model->find (); if (empty ( $acf )) { $token = $this->get_acf_token (); $data = array ( "token" => $token, "c_time" => time () ); $acf_model->add ( $data ); } else {
		 */
		// if ($acf ['token'] == '' || $acf ['token'] == null || $acf ['c_time'] < (time () - 3600 * 23)) {
		$token = $this->get_acf_token ();
		/*
		 * $data = array ( "token" => $token, "c_time" => time () ); $acf_model->where ( "id={$acf['id']}" )->save ( $data );
		 */
			/* } else {
				$token = $acf ['token'];
			} */
		/* } */
		if ($token != '' && $token != null) {
			$license_nums = $car ['license_number'];
			$provinceCode = urlencode ( mb_substr ( $license_nums, 0, 1, 'utf-8' ) );
			$carNumber = mb_substr ( $license_nums, 1, strlen ( $license_nums ), 'utf-8' );
			$engineLen = $region ['engine_nums'];
			$frameLen = $region ['frame_nums'];
			$engine_number = substr ( $car ['engine_number'], - $engineLen );
			$frame_number = substr ( $car ['frame_number'], - $frameLen );
			$url = "http://120.26.57.239/api/queryCarViolateInfo?provinceCode=$provinceCode&carNumber=$carNumber&vioCityCode={$region['acode']}&carType=0&carFrame={$frame_number}&carEngine={$engine_number}";
			$log->write ( $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
			$ch = curl_init ();
			$header = array (
					"token: $token" 
			);
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
			$output = curl_exec ( $ch );
			curl_close ( $ch );
			$jsoninfo = json_decode ( $output, true );
			$log->write ( "aichefang:" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		} else {
			$jsoninfo = array ();
			$jsoninfo ['code'] = 1;
		}
		return $jsoninfo;
	}
	// 获取爱车坊token
	function get_acf_token() {
		$url = "http://120.26.57.239/api/getAccessToken?merKey=" . merKey . "&merCode=" . merCode;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$log = new Log ();
		$log->write ( "get_token:" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$jsoninfo = json_decode ( $output, true );
		$token = $jsoninfo ['data'] [0] ['accessToken'];
		return $token;
	}
}
