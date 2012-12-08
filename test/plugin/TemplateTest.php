<?php

// @date: 2011-10-31
// @author: mahone

require_once __DIR__.'/../class/Template.php';

class TemplateTest extends PHPUnit_Framework_TestCase{
	
	// 测试生成模板
    public function test_generate_template(){
		$tpl_path = __DIR__.'/template/tpl/';
		$view_path = __DIR__.'/template/view/';
		$tpl_cache_time = 1;
		$tpl = new Template($tpl_path, $view_path, $tpl_cache_time);
		$tpl->generate_template('mix.php');
		
		$this->assertFileExists($view_path.'mix.php');
		$this->assertFileExists($view_path.'inc/inc.php');
		
		$content = file_get_contents($tpl_path.'mix.php');
		$content = str_replace('<title>template {$title}</title>', '<title>new template {$title}</title>', $content);
		file_put_contents($tpl_path.'mix.php', $content);
		sleep($tpl_cache_time+1);
		
		$tpl->generate_template('mix.php');
		$this->assertEquals(true, false!==strstr(file_get_contents($view_path.'mix.php'), '<title>new template'));
		
		$content = file_get_contents($tpl_path.'mix.php');
		$content = str_replace('<title>new template {$title}</title>', '<title>template {$title}</title>', $content);
		file_put_contents($tpl_path.'mix.php', $content);
		
		@unlink(__DIR__.'/template/view/mix.php');
		@unlink(__DIR__.'/template/view/inc/inc.php');
		@rmdir(__DIR__.'/template/view/inc');
    }
	
	// 测试编译(即字符串替换)功能
	public function test_compile(){
		$tpl_path = __DIR__.'/template/tpl/';
		$view_path = __DIR__.'/template/view/';
		$tpl_cache_time = 1;
		$tpl = new Template($tpl_path, $view_path, $tpl_cache_time);
		
		$src_compiled = $tpl->compile(file_get_contents(__DIR__.'/template/foreach.php'));
		$exp_str = file_get_contents(__DIR__.'/template/foreach.exp.php');
		$this->assertEquals($exp_str, $src_compiled);
		
		$src_compiled = $tpl->compile(file_get_contents(__DIR__.'/template/if.php'));
		$exp_str = file_get_contents(__DIR__.'/template/if.exp.php');
		$this->assertEquals($exp_str, $src_compiled);
		
		// include tag 暂时没有提供测试
		// $src_compiled = $tpl->compile(file_get_contents(__DIR__.'/template/include.php'));
		// $exp_str = file_get_contents(__DIR__.'/template/include.exp.php');
		// eval("\$exp_str = \"$exp_str\";");
		// $this->assertEquals($exp_str, $src_compiled);
		
		$src_compiled = $tpl->compile(file_get_contents(__DIR__.'/template/echo.php'));
		$exp_str = file_get_contents(__DIR__.'/template/echo.exp.php');
		$this->assertEquals($exp_str, $src_compiled);
	}
}