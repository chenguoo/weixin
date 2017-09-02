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
	private $appID = 'wxc2e10f577a7ba1bc';
	private $appsecret = '8da825670c67fa3c9a6a4912fe277faa';
	private $accessToken;
	function __construct() {
		parent::__construct(); // 子类构造方法不能自动调用父类的构造方法
	}

	public function index() {
		//绑定

		$this->verify();
		//获取用户消息对象
		$WxObj = $this->getWxObj();
		$this->WxObj = $WxObj;
		if (empty($WxObj)) {
			writeLog("wechat.log", "没有获取到参数.");
			echo "";
			exit;
		}

		//获取access_token
		$this->accessToken = $this->getAccessToken();

		//删除菜单
		//$this->deleteNav();
		//自定义菜单
		//$this->createNav();

		//回复消息
		$returnStr = '';
		switch ($WxObj->MsgType) {
		case 'text':
			$returnStr = $this->responseMsg();
			break;
		case 'event':
			$returnStr = $this->responseEvent();
			break;
		default:
			writeLog("wechat.log", "没有找到MsgType:" . $WxObj->MsgType . " Event:" . $WxObj->Event . PHP_EOL . $GLOBALS["HTTP_RAW_POST_DATA"]);
			$returnStr = $this->responseText("暂时不支持:" . $WxObj->Event . " " . $WxObj->MsgType);
			break;
		}

		echo $returnStr;
		exit;
	}
	/**
	 * 删除自定义菜单
	 * @return [type] [description]
	 */
	public function deleteNav() {
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $this->accessToken;
		$result = $this->curlGet($url);
		$data = json_encode($result);
		if ($data->errcode == 0) {
			return "删除菜单成功!";
		}
		return "删除菜单失败:" . $result;
	}

	private function createNav() {
		$data = array(
			"button" => array(
				array(
					"type" => "click",
					"name" => "布优新闻",
					"key" => "byxw",
				),
				array(
					"type" => "click",
					"name" => "求红包",
					"key" => "qhb",
				),
				array(
					"name" => "菜单栏",
					"sub_button" => array(
						array(
							"type" => "view",
							"name" => "布优网",
							"url" => "http://www.buuyou.com",
						),
						array(
							"type" => "view",
							"name" => "百度一下",
							"url" => "http://www.baidu.com",
						),
						array(
							"type" => "click",
							"name" => "公司地址",
							"key" => "address",
						),
					),
				),
			),
		);
		//转换为json
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);
		writeLog("wechat.log", $json);
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->accessToken;
		$result = $this->curlPost($url, $json);
		$data = json_encode($result);
		if ($data->errcode == 0) {
			return "创建菜单成功!";
		}
		return "创建菜单失败:" . $result;
	}

	/**
	 * 获取并缓存access_token
	 * @return String access_token
	 */
	public function getAccessToken() {
		writeLog("wechat.log", "进入getAccessToken.");

		//设置一个缓存文件名
		$cacheKey = 'access_token';
		//从缓存中去 access_token
		if ($accessToken = $this->cacheGet($cacheKey)) {
			return $accessToken;
		}
		writeLog("wechat.log", "缓存中没有获取到AccessToken");

		//$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appID . "&secret=" . $this->appsecret;
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxc2e10f577a7ba1bc&secret=8da825670c67fa3c9a6a4912fe277faa";
		//{"access_token":"ACCESS_TOKEN","expires_in":7200}
		//{"errcode":40013,"errmsg":"invalid appid"}
		print_r($url);
		$json = $this->curlGet($url);
		print_r($json);
		//直接转换为一个对象,如果要转换为数组,这传入第二个参数true.
		$data = json_decode($json, true);
		//判定返回码是否有错误.
		if (array_key_exists('errcode', $data) && $data['errcode'] != 0) {
			return false;
		}
		$accessToken = $data['access_token'];
		writeLog("wechat.log", "AccessToken:" . $accessToken);

		//缓存access_token
		$this->cacheSet($cacheKey, $accessToken, 7000);

		return $accessToken;
	}
	/**
	 * 事件推送处理
	 * @return String 返回的xml数据串.
	 */
	private function responseEvent() {
		$result = '';
		if ($this->WxObj->Event == 'subscribe') {
			$result = $this->responseText("欢迎关注我的微信公众号,有什么可以帮您的吗!");
		}
		if ($this->WxObj->Event == 'CLICK') {
			/*
				<xml>
				<ToUserName><![CDATA[toUser]]></ToUserName>
				<FromUserName><![CDATA[FromUser]]></FromUserName>
				<CreateTime>123456789</CreateTime>
				<MsgType><![CDATA[event]]></MsgType>
				<Event><![CDATA[CLICK]]></Event>
				<EventKey><![CDATA[EVENTKEY]]></EventKey>
				</xml>
			*/
			if ($this->WxObj->EventKey == 'byxw') {
				//布优新闻
				$result = $this->responseText("暂时还没有新闻哦!");
			} elseif ($this->WxObj->EventKey == 'qhb') {
				//求红包
				$result = $this->responseText("给你一个大红包!" . rand() . " 够花了吧!");
			} elseif ($this->WxObj->EventKey == 'address') {
				//地址
				$result = $this->responseText("我们的地址:广安市岳池县农村淘宝三楼!");
			}

		}
		return $result;
	}

	/**
	 * 文本类型的消息回复
	 * @return  string  返回的xml数据串.
	 */
	private function responseMsg() {
		$result = '';
		//这个地方暂时列举,后面可以重数据库中获取
		switch (trim($this->WxObj->Content)) {
		case '1':
			$result = $this->responseText("欢迎来到WeChat的世界!");
			break;
		case '2':
			$result = $this->responseText($this->deleteNav());
			break;
		case '3':
			$result = $this->responseText($this->createNav());
			break;
		case '4':
			$result = $this->responseText($this->accessToken);
			break;
		default:
			$result = $this->responseText("没有指定的回复消息!");
			break;
		}
		return $result;
	}
	/**
	 * 回复文本消息
	 * @return string  返回的xml数据串.
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
	 * @return string  xmlObj.
	 */
	private function getWxObj() {
		//获取xml参数
		$postXml = null;

		//这里在php7时不能获取数据，使用 php://input 代替
		if (!empty($GLOBALS["HTTP_RAW_POST_DATA"])) {
			$postXml = $GLOBALS["HTTP_RAW_POST_DATA"];
		} else {
			$postXml = file_get_contents("php://input");
		}
		//xml安全参数
		libxml_disable_entity_loader(true);
		$postObj = simplexml_load_string($postXml, 'SimpleXMLElement', LIBXML_NOCDATA);
		writeLog("wechat.log", "postXml:" . PHP_EOL . $postXml);
		return $postObj;
	}

	/**
	 * 微信绑定
	 * @return String 服务器发送的echoStr.
	 */
	private function verify() {
		if (!empty($_GET['echostr'])) {
			$echoStr = $_GET["echostr"];
			//valid signature , option
			if ($this->checkSignature()) {
				writeLog("wechat.log", "接口配置成功!");
				echo $echoStr;
				exit;
			}
		}
	}
	/**
	 * 绑定效验
	 * @return boolean 效验是否成功.
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
