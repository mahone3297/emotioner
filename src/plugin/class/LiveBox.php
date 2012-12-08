<?php

// @date: 2011-11-03
// @author: mahone

class LiveBox
{
	private $last = null; // 上次返回的服务器
	private $host = array(); // 可用服务器列表
	private $off = array(); // 不可用服务器
	
	// 构造函数
	public function __construct()
	{
		// 通过 cache 获取服务器列表
		// todo
	}
	
	// 注册一台服务器，加入可用列表
	public function register($host, $weight=1)
	{
		$sign = self::sign($host);
		
		$this->host[$sign] = array(
			'host'=>$host,
			'weight'=>max(1, (int)$weight),// 权重暂时无用
			'times'=>0
		);
		
		if (isset($this->off[$sign])){
			unset($this->off[$sign]);
		}
	}
	
	// 注销一台服务器，$this->host 和 $this->off 中都删除
	public function unregister($host)
	{
		$sign = self::sign($host);
		
		if (isset($this->host[$sign])) {
			unset($this->host[$sign]);
		}
		
		if (isset($this->off[$sign])){
			unset($this->off[$sign]);
		}
	}
	
	// 随机获取一台可用服务器
	public function fetch()
	{
		// 没有可用服务器
		if (empty($this->host)){
			return false;
		}
		
		$keys = array_keys($this->host);
		$this->last = $keys[array_rand($keys)];
		
		// 这里传引用，后面 times++
		$server =& $this->host[$this->last];
		$server['times']++;
		
		return $server['host'];
	}
	
	// 标记一台服务器不可用, 将指定服务器从 $this->host 移到 $this->off
	public function set_off($host)
	{
		$sign = self::sign($host);
		
		if (isset($this->host[$sign])){
			$this->off[$sign] = $this->host[$sign];
			unset($this->host[$sign]);
		}
	}
	
	// 清扫所有数据
	public function clear()
	{
		$this->last = null;
		$this->host = array();
		$this->off = array();
	}
	
	// 服务器签名
	private static function sign($host)
	{
		if (is_scalar($host)){
			return strtolower(trim($host));
		}
		
		return json_encode($host);
	}
	
	// 获取可用服务器列表
	public function get_host()
	{
		$rt = array();
		
		foreach (array_values($this->host) as $item){
			$rt[] = $item['host'];
		}
		
		return $rt;
	}
	
	// 获取不可用服务器列表
	public function get_off()
	{
		$rt = array();
		
		foreach (array_values($this->off) as $item){
			$rt[] = $item['host'];
		}
		
		return $rt;
	}
}