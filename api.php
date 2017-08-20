<?php

//include_once "logger.php";

// 第三方发送消息给公众平台
$encodingAesKey = "IRIGghg6E4bWwq4ThpIjyfLoKMrX6XBUeIlYlnf6asY";
$token = "b4158c3e3d171a7e7fc8d6a45b38dc71";
$appId = "wx6ea007df3d7eac2c";

$signature = $_GET["signature"];
$timestamp = $_GET["timestamp"];
$nonce = $_GET["nonce"];
$echostr = $_GET["echostr"];

//$logger = new Logger;
//$logger->writefile($signature." ".$timestamp." ".$timestamp." ".$nonce." ".$echostr);

echo $echostr;
exit;
?>