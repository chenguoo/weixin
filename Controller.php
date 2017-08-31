<?php
/**
 * 基础控制器
 * @authors cheney (2270292886@qq.com)
 * @date    2017-08-31 11:02:28
 * @version $Id$
 */

class Controller {

	private $dir = 'Storage';
	function __construct() {
		if (!is_dir($this->dir) && !mkdir($this->dir, 0755, true)) {
			throw new Exception("缓存目录创建失败!");
		}
	}

	/**
	 * 得到缓存文件
	 * @param  string $name 缓存文件名
	 * @return file       缓存文件
	 */
	private function getFile($name) {
		return $this->dir . '/' . md5($name) . 'php';
	}
	public function cacheSet($name, $data, $expire = 3600) {
		$file = $this->getFile($name);
		//缓存时间
		$expire = sprintf('%010d', $expire);
		$data = "<?php\n//" . $expire . serialize($data) . "\n?>";
		return file_put_contents($file, $data);
	}
	/**
	 * 获取缓存
	 * @param  String $name 缓存键
	 * @return String       缓存值
	 */
	public function cacheGet($name) {

		$file = $this->getFile($name);

		if (!is_file($file)) {
			return null;
		}

		$content = file_get_contents($file);
		$expire = intval(substr($content, 8, 10));
		//修改时间
		$mtime = filemtime($file);
		//缓存失效处理
		if ($expire > 0 && $mtime + $expire < time()) {
			return @unlink($file);
		}

		return unserialize(substr($content, 18, -3));
	}
	/**
	 * 模拟GET请求
	 * @param  String $url 请求地址
	 * @return Object      返回结果对象
	 */
	public function curlGet($url) {
		//1.初始化curl句柄
		$ch = curl_init();
		//2.设置curl
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//3.执行
		if (!curl_exec($ch)) {
			$data = '';
			//echo curl_error($ch);
		} else {
			//获取数据
			$data = curl_multi_getcontent($ch);
		}
		//4.关闭curl
		curl_close($ch);
		return $data;
	}

	public function curlPost($url, $postData) {
		//1.初始化curl句柄
		$ch = curl_init();
		//2.设置curl
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		//3.执行
		if (!curl_exec($ch)) {
			$data = '';
			//echo curl_error($ch);
		} else {
			//获取数据
			$data = curl_multi_getcontent($ch);
		}
		//4.关闭curl
		curl_close($ch);
		return $data;
	}
}
