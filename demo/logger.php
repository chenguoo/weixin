<?php 
/**
* 
*/
class Logger
{
	
	/**
	 * 输出到文件
	 * @param $token string 公众平台上，开发者设置的token
	 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
	 * @param $appId string 公众平台的appId
	 */
	public function writefile($msg)
	{ 
		$filename="/var/www/winxin/log.txt";

	  $file=fopen($filename,"a+");
		date_default_timezone_set("PRC");
    fwrite($file,"\r\n".date("Y-m-d H:i:s")."|".$msg);
 
		fwrite($file,"\r\n------------------------------------------------------------------------------------------------------------------------------------------------------------------");
		fclose($file);	
	}
}




?>