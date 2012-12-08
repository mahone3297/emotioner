<?php

// @date 2012-02-23
// @author mahone

require_once __DIR__ . '/../../src/plugin/class/EmpCacheMemcache.php';

class EmpCacheMemcacheTest extends PHPUnit_Framework_TestCase
{
    protected $config = array(
        'servers' => array(
            array('host' => 'localhost', 'port' => 11211, 'weight' => 1)
        ),
        'option' => array(
            Memcached::OPT_COMPRESSION => true,
            Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP,
            Memcached::OPT_PREFIX_KEY => '',
            Memcached::OPT_HASH => Memcached::HASH_DEFAULT,
            Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT,
            Memcached::OPT_LIBKETAMA_COMPATIBLE => true, // note: md5, consistent hash
            Memcached::OPT_BUFFER_WRITES => false,
            Memcached::OPT_BINARY_PROTOCOL => false,
            Memcached::OPT_NO_BLOCK => false,
            Memcached::OPT_TCP_NODELAY => false,
            // Memcached::OPT_SOCKET_SEND_SIZE // bytes
            // Memcached::OPT_SOCKET_RECV_SIZE // bytes
            Memcached::OPT_CONNECT_TIMEOUT => 1000, // millisecond
            Memcached::OPT_RETRY_TIMEOUT => 0, // second
            Memcached::OPT_SEND_TIMEOUT => 0, // microsecond
            Memcached::OPT_RECV_TIMEOUT => 0, // microsecond
            Memcached::OPT_POLL_TIMEOUT => 1000, // millisecond
            Memcached::OPT_CACHE_LOOKUPS => false,
            Memcached::OPT_SERVER_FAILURE_LIMIT => 0
        )
    );
    
    public function setUp()
    {
        parent::setUp();
        
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $mem->flush();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $mem->flush();
    }
    
    public function test_add()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->add('key', 'value', 3);
        $this->assertEquals(true, $rt);
        
        $rt = $mem->add('key', 'value', 3);
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->add('key', 'value', 3, 'server_key');
        $this->assertEquals(true, $rt);
        
        $rt = $mem->add('key', 'value', 3, 'server_key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
    }
    
    public function test_cas()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $mem->add('key', 'value', 3);
        $rt = $mem->get('key', null, $cas_token);
        $this->assertEquals('value', $rt);
        
        $rt = $mem->cas($cas_token, 'key', 'value_2', 3);
        $this->assertEquals(true, $rt);
        $rt = $mem->get('key', null, $cas_token);
        $this->assertEquals('value_2', $rt);
        
        // have server_key test below
        $mem->flush();
        
        $mem->add('key', 'value', 3, 'server_key');
        $rt = $mem->get('key', null, $cas_token, 'server_key');
        $this->assertEquals('value', $rt);
        
        $rt = $mem->cas($cas_token, 'key', 'value_2', 3, 'server_key');
        $this->assertEquals(true, $rt);
        $rt = $mem->get('key', null, $cas_token, 'server_key');
        $this->assertEquals('value_2', $rt);
    }
    
    public function test_delete()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $mem->add('key', 'value', 3);
        $rt = $mem->delete('key');
        $this->assertEquals(true, $rt);
        
        $rt = $mem->delete('key');
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
        
        // have server_key test below
        $mem->flush();
        
        $mem->add('key', 'value', 3, 'server_key');
        $rt = $mem->delete('key', 0, 'server_key');
        $this->assertEquals(true, $rt);
        
        $rt = $mem->delete('key', 0, 'server_key');
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
    }
    
    public function test_replace()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->replace('key', 'value_2', 3);
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->add('key', 'value', 3);
        $rt = $mem->replace('key', 'value_2', 3);
        $this->assertEquals(true, $rt);
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->replace('key', 'value_2', 3, 'server_key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->add('key', 'value', 3, 'server_key');
        $rt = $mem->replace('key', 'value_2', 3, 'server_key');
        $this->assertEquals(true, $rt);
    }
    
    public function test_get()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->get('key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
        
        $mem->add('key', 'value', 3);
        $rt = $mem->get('key');
        $this->assertEquals('value', $rt);
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->get('key', null, $cas_token, 'server_key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
        
        $mem->add('key', 'value', 3, 'server_key');
        $rt = $mem->get('key', null, $cas_token, 'server_key');
        $this->assertEquals('value', $rt);
    }
    
    public function test_set()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->set('key', 'value', 3);
        $this->assertEquals(true, $rt);
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->set('key', 'value', 3, 'server_key');
        $this->assertEquals(true, $rt);
    }
    
    public function test_get_multi()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $mem->set_multi($items, 3);
        $rt = $mem->get_multi(array('key1', 'key3', 'badkey'));
        $this->assertEquals(array(
            'key1' => 'value1',
            'key3' => 'value3',
            'badkey' => null
        ), $rt);
        
        // have server_key test below
        $mem->flush();
        
        $cas_tokens = array();
        $mem->set_multi($items, 3, 'server_key');
        $rt = $mem->get_multi(array('key1', 'key3', 'badkey'), $cas_tokens, Memcached::GET_PRESERVE_ORDER, 'server_key');
        $this->assertEquals(array(
            'key1' => 'value1',
            'key3' => 'value3',
            'badkey' => null
        ), $rt);
    }
    
    public function test_set_multi()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->set_multi($items, 3);
        $this->assertEquals(true, $rt);
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->set_multi($items, 3, 'server_key');
        $this->assertEquals(true, $rt);
    }
    
    public function test_prepend()
    {
        $option = $this->config['option'];
        $option[Memcached::OPT_COMPRESSION] = false;
        $mem = new EmpCacheMemcache($this->config['servers'], $option);
        $rt = $mem->prepend('foo', 'def');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->set('foo', 'abc', 3);
        $rt = $mem->prepend('foo', 'def');
        $this->assertEquals(true, $rt);
        $this->assertEquals('defabc', $mem->get('foo'));
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->prepend('foo', 'def', 'server_key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->set('foo', 'abc', 3, 'server_key');
        $rt = $mem->prepend('foo', 'def', 'server_key');
        $this->assertEquals(true, $rt);
        $this->assertEquals('defabc', $mem->get('foo', null, $cas_token, 'server_key'));
    }
    
    public function test_append()
    {
        $option = $this->config['option'];
        $option[Memcached::OPT_COMPRESSION] = false;
        $mem = new EmpCacheMemcache($this->config['servers'], $option);
        $rt = $mem->append('foo', 'def');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->set('foo', 'abc', 3);
        $rt = $mem->append('foo', 'def');
        $this->assertEquals(true, $rt);
        $this->assertEquals('abcdef', $mem->get('foo'));
        
        // have server_key test below
        $mem->flush();
        
        $rt = $mem->append('foo', 'def', 'server_key');
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTSTORED,
            'rs_msg' => 'NOT STORED'
        ), $mem->get_result());
        
        $mem->set('foo', 'abc', 3, 'server_key');
        $rt = $mem->append('foo', 'def', 'server_key');
        $this->assertEquals(true, $rt);
        $this->assertEquals('abcdef', $mem->get('foo', null, $cas_token, 'server_key'));
    }
    
    public function test_increment()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->increment('counter', 2);
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
        
        $mem->set('key', 'value', 3);
        $rt = $mem->increment('key', 2);
        $this->assertEquals(false, $rt);
        
        $mem->set('counter', 10, 3);
        $rt = $mem->increment('counter', 2);
        $this->assertEquals(12, $mem->get('counter'));
    }
    
    public function test_decrement()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->decrement('counter', 2);
        $this->assertEquals(false, $rt);
        $this->assertEquals(array(
            'rs_code' => Memcached::RES_NOTFOUND,
            'rs_msg' => 'NOT FOUND'
        ), $mem->get_result());
        
        $mem->set('key', 'value', 3);
        $rt = $mem->decrement('key', 2);
        $this->assertEquals(false, $rt);
        
        $mem->set('counter', 10, 3);
        $rt = $mem->decrement('counter', 2);
        $this->assertEquals(8, $mem->get('counter'));
    }
    
    public function test_get_server_by_key()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $this->assertEquals(array(
            'host' => 'localhost',
            'port' => 11211,
            'weight' => 1
        ), $mem->get_server_by_key('server_key'));
    }
    
    public function test_get_server_list()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $this->assertEquals(array(
            array(
                'host' => 'localhost',
                'port' => 11211,
                'weight' => 1
            )
        ), $mem->get_server_list('server_key'));
    }
    
    public function test_get_stats()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->get_stats();
        $this->assertEquals(true, is_array($rt));
    }
    
    public function test_get_version()
    {
        $mem = new EmpCacheMemcache($this->config['servers'], $this->config['option']);
        $rt = $mem->get_stats();
        $this->assertEquals(true, is_array($rt));
    }
}