<?php
/* require_once ("alipay/alipay.config.php");
define ( "partner", $alipay_config['partner'] );
define ( "seller_id", $alipay_config['partner'] );
define ( "private_key_path", $alipay_config['private_key_path'] );
define ( "ali_public_key_path", $alipay_config['ali_public_key_path'] );
define ( "sign_type", $alipay_config['sign_type'] );
define ( "input_charset", $alipay_config['input_charset'] );
define ( "cacert", $alipay_config['cacert'] );
define ( "transport", $alipay_config['transport'] ); */
define ( "TOKEN", "weixin" );
define ( "APPID", "wxc4fd21f2f47f1fee" );
define ( "APPSECRET", "0e4a02e380503002556103387864b736" );
define ( "MCHID", "1293235801" ); // 商户id
define ( "KEY", "54cbf62f9e8ef2da4b9df54ca8d3920e" ); // 微信支付秘钥
define ( "MUBAN1", "Xk80Rn1LquxsfIbdSh08IEgD4L-q3sqVZZqZ448aEtA" ); // 违章扫描目标消息id
define ( "MUBAN2", "8vDv2XRSA8ZEQEyrs6pdfFvKg5bubVJH-WjM5i7vOzw" ); // 违章扫描目标新消息id
define ( "MUBAN3", "0oBmGMz5a2FUnuwDEFoInB55KkEiYwaeT3G0ItwV9XY" ); // 订单状态更新消息id
define ( "URL1", "http://" . $_SERVER ['SERVER_NAME'] . "/index.php?g=weixin&m=sub&a=index" ); // 违章订阅地址
define ( "URL2", "http://" . $_SERVER ['SERVER_NAME'] . "/index.php?g=weixin&m=order&a=index" ); // 我的订单地址
define ( "URL3", "http://" . $_SERVER ['SERVER_NAME'] . "/index.php?g=weixin&m=scan&a=index" ); // 违章扫描地址
define ( "APIURL", "http://" . $_SERVER ['SERVER_NAME'] . "/" ); // 根目录
define ( "NUMS1", "10" ); // 筛选服务商数量(第一条件：定价)
define ( "NUMS2", "5" ); // 筛选服务商数量(第二条件：评分)
define ( "app_id", "928" ); // 车首页appid
define ( "app_key", "edd9312406d6ed867262f0d50a49029c" ); // 车首页appkey
define ( "scan_time", "10" ); // 扫描间隔时间
define ( "merKey", "9a5eae4723e87befc85459d5b0c585dc" ); // 爱车坊merKey
define ( "merCode", "2500000002" ); // 爱车坊merCode
define ( "acfapi", "120.26.57.239" ); // 爱车坊端口
define ( 'csyapi', "cheshouye.com" ); // 车首页端口
define ( 'bdkey', "53cb9129fd47522b71620094c6f06020" ); // 车首页端口
define ( "timing_count1", "20" ); // 每周定时查询数量
define ( "timing_count2", "4" ); // 每月定时查询数量
define ( "jishi1", 3600 * 24 ); // 推送计时
define ( "jishi2", 3600 * 72 ); // 办理计时

define ( "versions", 'v1.0' ); // 版本号

/* 订单状态更改, 处理中/处理完成/已退款状态更改没有对应的推送消息，文字提示信息 */
define ( "first_key", '尊敬的用户，您的违章代缴办理结果如下' );
define ( "last_key", '感谢您使用我们的服务，如有疑问可直接回复文字联系客服。' );
define ( "status1", '处理中' );
define ( "status2", '处理完成' );
define ( "status3", '已退款' );