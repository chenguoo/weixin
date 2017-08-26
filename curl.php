<?php
//curl 四步骤操作流程
//1.初始化curl句柄
$ch = curl_init();
//2.设置curl
$url="http://www.baidu.com";
curl_setopt($ch,CURLOPT_URL,$url);
//3.执行curl
curl_exec($ch);
//4.关闭curl
curl_close($ch);

?>