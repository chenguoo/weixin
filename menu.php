<?php
//curl 四步骤操作流程
//1.初始化curl句柄
$ch = curl_init();
//2.设置curl
$access_token="_GNg6BzNhBE8XRwsKvVcImFW_IK9s8xpS-9Mp-HKPOUlUEDNkx1Wr-8pXgw92FCPKynLzb83oBPYikbQpCUkIFnbzvIdAy83bY9l08Hf9EWJBcMAarObnD8LFNoNoDl1JSDdAJATCN";

$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POST,true);

$data =  '{
  "button":[
    {
      "type":"click",
      "name":"今日歌曲",
      "key":"V1001_TODAY_MUSIC"
    },
    {
      "name":"菜单",
      "sub_button":[
        {
           "type":"view",
           "name":"搜索",
           "url":"http://www.soso.com/"
        },
        {
           "type":"click",
           "name":"赞一下我们0",
           "key":"V1001_GOOD"
        }
      ]
    }
  ]
}';
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
//3.执行curl
$output = curl_multi_getcontent($ch);
if($output === false){
  echo curl_error($ch);
  echo "出错了";
}else {
  echo $output;
}
//echo $output;
//4.关闭curl
curl_close($ch);

?>