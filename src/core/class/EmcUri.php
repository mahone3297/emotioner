<?php

// @date 2012-01-14
// @author mahone

class EmcUri
{
    private $uri_string = '';
    private $segment = array();
    
    public function __construct()
    {
        $this->parse();
    }
    
    // parse
    public function parse()
    {
        if (php_sapi_name() == 'cli' or defined('STDIN')){
            $this->parse_from_cli();
        } else {
            $this->parse_from_url();
        }
    }
    
    // parse from url
    public function parse_from_url()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0){
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		} else if (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0){
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
        
        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0){
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1])){
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		} else {
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}
        
        $this->uri_string = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');
        if ($this->uri_string != '/'){
            $this->segment = explode('/', trim($this->uri_string, '/'));
        }
    }
    
    // parse from cli
    public function parse_from_cli()
    {
        if ($_SERVER['argc'] < 2){
            return ;
        }
        
        $this->uri_string = '/' . trim($_SERVER['argv'][1], '/');
        $this->segment = explode('/', trim($this->uri_string, '/'));
    }
    
    // set segment
    public function set_segment($val)
    {
        if (is_array($val)){
            $this->segment = $val;
            return ;
        }
        
        $this->segment[] = $val;
    }
    
    // get segment
    public function get_segment()
    {
        return $this->segment;
    }
    
    // get uri string
    public function get_uri_string()
    {
        return $this->uri_string;
    }
}