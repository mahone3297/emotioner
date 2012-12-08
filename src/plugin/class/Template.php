<?php

// @date: 2011-10-31
// @author: mahone

// eg:
	// 1.<foreach list="$user_list" key="$user_key" item="$user">
		// default: key is {$user_key}, name = {$user["user_name"]}, age = {$user['user_age']} <br/>
	// </foreach>
	// 2.<if condition="$val == 3">
		// 3 key is {$key}, val = {$val}<br/>
	// <elseif condition="$val == 4"/>
		// 4 key is {$key}, val = {$val}<br/>
	// <else/>
		// default key is {$key}, val = {$val}<br/>
	// </if>
	// 3.<include file="inc/inc.php"/>
	// 4.{$name}

class Template{
	
	private $tpl_path = '';
	private $view_path = '';
	private $tpl_cache_time = 2592000; // 60*60*24*30
	
	public function __construct($tpl_path='', $view_path='', $tpl_cache_time=2592000){
		$this->tpl_path = rtrim($tpl_path, '/').'/';
		$this->view_path = rtrim($view_path, '/').'/';
		$this->tpl_cache_time = $tpl_cache_time;
	}
	
	// set tpl_path & view_path
	public function set_path($tpl_path, $view_path){
		$this->tpl_path = rtrim($tpl_path, '/').'/';
		$this->view_path = rtrim($view_path, '/').'/';
	}
	
	// set tpl cache time
	public function set_tpl_cache_time($tpl_cache_time){
		$this->tpl_cache_time = $tpl_cache_time;
	}
	
	// generate template
	// $force: 是否不管过期与否强制编译
	// $return: 是否要返回字符串
	public function generate_template($file_path, $force=false, $return=false){
		if ( ! is_file($this->tpl_path.$file_path)){
			return false;
		}
		
		// check cache
		if ($force===false && $this->isCacheValid($file_path)){
			if ($return === true){
				return file_get_contents($this->view_path.$file_path);
			}
			
			return true;
		}
		
		// compile
		$content = file_get_contents($this->tpl_path.$file_path);
		$content = $this->compile($content);
		
		// if need, return $content
		if ($return === true){
			return $content;
		}
		
		// if dir not exist, mkdir
		$dir = dirname($this->view_path.$file_path);
		if ( ! is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		file_put_contents($this->view_path.$file_path, $content);
	}
	
	// check cache valid
	public function isCacheValid($file_path){
		$abs_view_file_path = $this->view_path.$file_path;
		$abs_tpl_file_path = $this->tpl_path.$file_path;
		
		if (!is_file($abs_view_file_path)){
			return false;
		}
		
		if (filemtime($abs_tpl_file_path) > filemtime($abs_view_file_path)){
			return false;
		}
		
		if ($this->tpl_cache_time!=-1 && time()>filemtime($abs_view_file_path)+$this->tpl_cache_time){
			return false;
		}
		
		return true;
	}
	
	// compile, just replace the content
	public function compile($content){
		$vp = '\$[0-9a-zA-Z_\'"\[\]]+'; // variable
		$vcp = '[\$0-9a-zA-Z_\'"\[\]]+'; // variable, const variable
		$file_path = '[\$0-9a-zA-Z_\'"\[\]\/\.]+'; // file path
		$condition = '[\$0-9a-zA-Z_\'"\[\]<>=!&|\(\)\s]+'; // condition
		
		// find include tag, compile.
		preg_match_all('/<include\s+file="('.$file_path.')"\s*\/>/', $content, $matches);
		foreach ($matches[1] as $val){
			$this->generate_template($val);
		}
		
		$pattern = array('/<foreach\s+list="('.$vp.')"\s+key="('.$vp.')"\s+item="('.$vp.')"\s*>/', // foreach tag
						'/<\/foreach>/',
						
						'/<if\s+condition="('.$condition.')"\s*>/', // if tag
						'/<elseif\s+condition="('.$condition.')"\s*\/>/',
						'/<else\s*\/>/',
						'/<\/if>/',
						
						'/<include\s+file="('.$file_path.')"\s*\/>/', // include tag
						
						'/{('.$vp.')}/'); // {$name}
		$replacement = array('<?php foreach (${1} as ${2}=>${3}){ ?>',
							'<?php } ?>',
							
							'<?php if (${1}){ ?>',
							'<?php } else if (${1}){ ?>',
							'<?php } else { ?>',
							'<?php } ?>',
							
							'<?php require "'.$this->view_path.'${1}"; ?>',
							/*'<?php generate_template("${1}")); ?>',*/
							
							'<?php echo ${1}; ?>');
		
		return preg_replace($pattern, $replacement, $content);
	}
}