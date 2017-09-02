<?php
/**
 * 测试
 * @authors cheney (2270292886@qq.com)
 * @date    2017-09-01 15:59:01
 * @version $Id$
 */
include './functions.php';
include './Controller.php';
include './WeChatController.php';

//测试设置chche
//$wechatObj->cacheSet("abc", "123", 10);

//测试获取chche
//$abc = $wechatObj->cacheGet("abc");
//echo "abc:" . $abc;

//测试获取AccessToken
//{"access_token":"ACCESS_TOKEN","expires_in":7200}
//{"errcode":40013,"errmsg":"invalid appid"}
/*
$weChatObj = new WeChatController;
$json = $weChatObj->getAccessToken();
writeLog("wechat.log", "jsontest:" . $json);
echo "jsontest:" . $json;
 */

//测试curlGet方法
/*
$html = $wechatObj->curlGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxc2e10f577a7ba1bc&secret=8da825670c67fa3c9a6a4912fe277faa");
echo $html;
 */
