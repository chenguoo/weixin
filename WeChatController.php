<?php
//include './functions.php';
/**
 * 微信控制器
 * @authors cheney (2270292886@qq.com)
 * @date    2017-08-31 12:11:36
 * @version $Id$
 */
//定义常量 TOKEM
define("TOKEN", "buuyoutest");

class WeChatController extends Controller {
	private $WxObj;
	function __construct() {}

	public function index() {
		//绑定
		//$this->verify();
		//获取用户消息对象
		$WxObj = $this->getWxObj();
		$this->WxObj = $WxObj;
		if (empty($WxObj)) {
			writeLog("wechat.log", "没有获取到参数.");
			echo "";
			exit;
		}
		//回复消息
		switch ($WxObj->MsgType) {
		case 'text':
			$this->responseMsg();
			break;

		default:
			// code...
			break;
		}

	}
	/**
	 * 文本类型的消息回复
	 * @return  string  返回的xml数据串.
	 */
	private function responseMsg() {
		$result = '';
		switch (trim($this->WxObj->Content)) {
		case '1':
			$result = $this->responseText("欢迎来到WeChat的世界!");
			break;
		default:
			$result = $this->responseText("没有指定的回复消息!");
			break;
		}
		echo $result;
	}
	/**
	 * 回复文本消息
	 * @return [type] [description]
	 */
	private function responseText($str) {
		$WxObj = $this->WxObj;
		$textTpl = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Content><![CDATA[%s]]></Content>
              </xml>";
		//$str = "欢迎来到WeChat的世界!您发来的消息是:" . $WxObj->Content;
		$resultStr = sprintf($textTpl, $WxObj->FromUserName, $WxObj->ToUserName, time(), 'text', $str);
		return $resultStr;
	}
	/**
	 * 得到xmltoObj对象
	 * @return [type] [description]
	 */
	private function getWxObj() {
		//获取xml参数
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		//xml安全参数
		libxml_disable_entity_loader(true);
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		return $postObj;
	}

	/**
	 * 微信绑定
	 * @return [type] [description]
	 */
	private function verify() {
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if ($this->checkSignature()) {
			echo $echoStr;
			exit;
		}
	}
	/**
	 * 绑定效验
	 * @return [type] [description]
	 */
	private function checkSignature() {
		if (!defined("TOKEN")) {
			throw new Exception("TOKEN is not defined!");
		}

		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		//通过字典法进行排序
		sort($tmpArr);
		//把排序后的数组转化为字符串
		$tmpStr = implode($tmpArr);
		//通过哈希算法对字符串进行加密操作
		$tmpStr = sha1($tmpStr);
		//
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}
