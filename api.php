<?php

//include_once "logger.php";

// 第三方发送消息给公众平台
$encodingAesKey = "IRIGghg6E4bWwq4ThpIjyfLoKMrX6XBUeIlYlnf6asY";
$appId = "wx6ea007df3d7eac2c";

//$logger = new Logger;
//$logger->writefile($signature." ".$timestamp." ".$timestamp." ".$nonce." ".$echostr);

//定义常量 TOKEM
define("TOKEN", "buuyou");
//实例化微信处理对象
$wechatObj = new WechatCallbackapiTest();
//验证微信服务器,成功后注释掉
//$wechatObj->valid();
//开启自动回复功能
#$wechatObj->responseMsg();
if ($_GET["echostr"]) {
	$wechatObj->valid();
} else {
	$wechatObj->responseMsg();
}
/**
 *
 */
class WechatCallbackapiTest {
	public function valid() {
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if ($this->checkSignature()) {
			//if(true){
			echo $echoStr;
			exit;
		}
	}
	public function checkSignature() {
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

	public function responseMsg() {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		//file_put_contents("wechat.log", "come in!");
		//extract post data
		if (!empty($postStr)) {
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			//file_put_contents("wechat.log", "postObj =".$postObj);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$msgType = $postObj->MsgType;
			$result = "";
			#判断消息类型
			switch ($msgType) {
			case 'text':
				//APPEND
				$result = $this->responseText($fromUsername, $toUsername, $postObj->Content);
				#file_put_contents("wechat.log", "result =".$result);
				break;
			case 'location':

				$result = $this->responseLocation($postObj, $fromUsername, $toUsername);
				break;
				#default:
				# code...
				# break;
			}

			//发送给微信服务器消息内容
			echo $result;
			exit;

		} else {
			echo "";
			exit;
		}
	}
	/**
	 * 回复文本消息请求
	 * @param  [type] $toUsername [description]
	 * @param  [type] $fromUsername   [description]
	 * @param  [type] $content      [description]
	 * @return [type]               [description]
	 */
	private function responseText($toUsername, $fromUsername, $keyword) {
		if (!empty($keyword)) {
			$resultStr = '';

			if ($keyword == '音乐') {
				$resultStr = $this->responseMusic($toUsername, $fromUsername);
			} elseif ($keyword == '图文') {
				$resultStr = $this->responsePicText($toUsername, $fromUsername);
			} else {
				$str = "欢迎来到WeChat的世界!您发来的消息是:" . $keyword;
				$resultStr = $this->responseTextOther($toUsername, $fromUsername, $str);
			}

			return $resultStr;
		} else {
			return "Input something...";
		}
	}

	private function responseTextOther($toUsername, $fromUsername, $str) {
		$textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
		$resultStr = sprintf($textTpl, $toUsername, $fromUsername, time(), 'text', $str);
		return $resultStr;
	}

	/**
	 * 回复音乐内容
	 * @param  [type] $toUsername   [description]
	 * @param  [type] $fromUsername [description]
	 * @param  [type] $content      [description]
	 * @return [type]               [description]
	 */
	private function responseMusic($toUsername, $fromUsername) {
		$textTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[%s]]></MsgType>
                  <Music>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <MusicUrl><![CDATA[%s]]></MusicUrl>
                  <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                  </Music>
                  </xml>";
		$msgType = "music";
		$title = "冰雪奇缘";
		$desc = "原声大碟...";
		$musicUrl = "http://119.23.224.5/music.mp3";
		$HQMusicUrl = $musicUrl;

		$resultStr = sprintf($textTpl, $toUsername, $fromUsername, time(), $msgType, $title, $desc, $musicUrl, $HQMusicUrl);

		return $resultStr;
	}
	/**
	 * 回复图文内容
	 * @param  [type] $toUsername   [description]
	 * @param  [type] $fromUsername [description]
	 * @return [type]               [description]
	 */
	private function responsePicText($toUsername, $fromUsername) {
		$textTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[%s]]></MsgType>
                  <ArticleCount>%s</ArticleCount>
                  <Articles>
                  %s
                  </Articles>
                  </xml>";
		$msgType = "news";

		$count = 4;
		$items = "<item>
              <Title><![CDATA[Python最厉害的还是后台开发]]></Title>
              <Description><![CDATA[Python最厉害的还是后台开发]]></Description>
              <PicUrl><![CDATA[http://119.23.224.5/images/img1.jpg]]></PicUrl>
              <Url><![CDATA[http://www.toutiao.com/a6456716998154912270]]></Url>
              </item>";
		$items .= "<item>
              <Title><![CDATA[感觉很阳光美丽的女生]]></Title>
              <Description><![CDATA[感觉很阳光美丽的女生]]></Description>
              <PicUrl><![CDATA[http://119.23.224.5/images/img3.jpg]]></PicUrl>
              <Url><![CDATA[http://www.toutiao.com/a6454137380667392526/#p=1]]></Url>
              </item>";
		$items .= "<item>
              <Title><![CDATA[web程序员，该掌握的linux命令有哪些，稍微高级点的？]]></Title>
              <Description><![CDATA[web程序员，该掌握的linux命令有哪些，稍微高级点的？]]></Description>
              <PicUrl><![CDATA[http://119.23.224.5/images/img2.jpg]]></PicUrl>
              <Url><![CDATA[http://www.toutiao.com/a6456614651965735438]]></Url>
              </item>";
		$items .= "<item>
              <Title><![CDATA[世界不缺少美 缺的是发现美的眼睛]]></Title>
              <Description><![CDATA[世界不缺少美 缺的是发现美的眼睛]]></Description>
              <PicUrl><![CDATA[http://119.23.224.5/images/img4.jpg]]></PicUrl>
              <Url><![CDATA[http://www.toutiao.com/a6452876786689638926/#p=1]></Url>
              </item>";

		$resultStr = sprintf($textTpl, $toUsername, $fromUsername, time(), $msgType, $count, $items);

		return $resultStr;
	}

	/**
	 * 回复地理位置
	 * @param  [type] $postObj      [description]
	 * @param  [type] $toUsername   [description]
	 * @param  [type] $fromUsername [description]
	 * @return [type]               [description]
	 */
	private function responseLocation($postObj, $toUsername, $fromUsername) {
		$x = $postObj->Location_X;
		$y = $postObj->Location_Y;
		$label = $postObj->Label;
		$str = "你发送的是地理位置消息,维度:{$x}经度:{$y}地理位置信息:{$label}";
		return $this->responseTextOther($toUsername, $fromUsername, $str);
	}
}
?>
