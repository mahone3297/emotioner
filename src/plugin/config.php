<?php if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-02-23
// @author: mahone

// class EmpHttp
$config['emphttp'] = array(
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 10
);

// class EmpCacheMemcache
$config['empcachememcache'] = array(
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