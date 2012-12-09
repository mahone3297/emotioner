<?php

/*
@date 2011-11-12
@author mahone
@desc
    delimiter $$

    CREATE TABLE `user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(45) NOT NULL,
      `password` varchar(45) NOT NULL,
      `age` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8$$
*/

require_once __DIR__ . '/../../src/core/class/EmcConfig.php';
require_once __DIR__ . '/../../src/core/class/EmcGlobal.php';
require_once __DIR__ . '/../../src/core/class/EmcLog.php';
require_once __DIR__ . '/../../src/core/class/EmpMysql.php';

class EmpMysqlTest extends PHPUnit_Framework_TestCase
{
    protected $config = array(
        'master' => array(
            'host' => 'localhost',
            'username' => 'mahone',
            'password' => 'taobao',
            'database' => 'unittest',
            'port' => 3306,
            'charset' => 'utf8',
            'timeout' => 5
        )
    );
    
    public function setUp()
    {
        parent::setUp();
        
        $db = new EmpMysql($this->config);
        $db->query('TRUNCATE TABLE user');
        
        $db->query("INSERT INTO user(username, password, age) VALUES('jacky', 'jacky123', 1)");
        $db->query("INSERT INTO user(username, password, age) VALUES('betty', 'betty123', 2)");
        $db->query("INSERT INTO user(username, password, age) VALUES('bob', 'bob123', 3)");
        $db->query("INSERT INTO user(username, password, age) VALUES('json', 'json123', 4)");
        $db->query("INSERT INTO user(username, password, age) VALUES('marry', 'marry123', 5)");
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $db = new EmpMysql($this->config);
        $db->query('TRUNCATE TABLE user');
        $db->disconnect();
    }
    
    public function test_get_grid()
    {
        $db = new EmpMysql($this->config);
        $rt = $db->get_grid("SELECT username, password, age FROM user");
        $this->assertEquals(array(
            array('username'=>'jacky','password'=>'jacky123','age'=>1),
            array('username'=>'betty','password'=>'betty123','age'=>2),
            array('username'=>'bob','password'=>'bob123','age'=>3),
            array('username'=>'json','password'=>'json123','age'=>4),
            array('username'=>'marry','password'=>'marry123','age'=>5)
        ), $rt);
        
        $rt = $db->get_grid("SELECT username, password, age FROM user WHERE username='notexist'");
        $this->assertEquals(null, $rt);
    }
    
    public function test_get_row()
    {
        $db = new EmpMysql($this->config);
        $rt = $db->get_row("SELECT username, password, age FROM user ORDER BY id ASC");
        $this->assertEquals(array('username'=>'jacky','password'=>'jacky123','age'=>1), $rt);
        
        $rt = $db->get_row("SELECT username, password, age FROM user ORDER BY id ASC LIMIT 1");
        $this->assertEquals(array('username'=>'jacky','password'=>'jacky123','age'=>1), $rt);
        
        $rt = $db->get_row("SELECT username, password, age FROM user WHERE username='notexist'");
        $this->assertEquals(null, $rt);
    }
    
    public function test_get_col()
    {
        $db = new EmpMysql($this->config);
        $rt = $db->get_col("SELECT username, password, age FROM user ORDER BY id ASC");
        $this->assertEquals('jacky', $rt);
        
        $rt = $db->get_col("SELECT username FROM user WHERE username='notexist'");
        $this->assertEquals(null, $rt);
    }
    
    public function test_insert()
    {
        $db = new EmpMysql($this->config);
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $rt = $db->get_col("SELECT COUNT(*) AS count FROM user WHERE username='insert'");
        $this->assertEquals(3, $rt);
    }
    
    public function test_update()
    {
        $db = new EmpMysql($this->config);
        $db->query("UPDATE user SET password='123456' WHERE username='marry'");
        $rt = $db->get_row("SELECT username, password, age FROM user WHERE username='marry' ORDER BY id ASC");
        $this->assertEquals(array('username'=>'marry','password'=>'123456','age'=>5), $rt);
    }
    
    public function test_delete()
    {
        $db = new EmpMysql($this->config);
        $db->query("DELETE FROM user WHERE username IN ('bob', 'json', 'jacky')");
        $rt = $db->get_grid("SELECT username, password, age FROM user");
        $this->assertEquals(array(
            array('username'=>'betty','password'=>'betty123','age'=>2),
            array('username'=>'marry','password'=>'marry123','age'=>5)
        ), $rt);
    }
    
    public function test_escape()
    {
        $db = new EmpMysql($this->config);
        $str = 'uni"tt\'es"t';
        $str = $db->escape($str);
        $db->query("INSERT INTO user(username, password, age) VALUES('unittest', $str, 10)");
        $rt = $db->get_row("SELECT username, password, age FROM user WHERE username='unittest' LIMIT 1");
        $this->assertEquals(array('username'=>'unittest','password'=>'uni"tt\'es"t','age'=>10), $rt);
    }
    
    public function test_autobind()
    {
        $db = new EmpMysql($this->config);
        $db->query("INSERT INTO user(username, password, age) VALUES(?, ?, ?)",
            array('autobind', '123456', 10));
        $db->query("INSERT INTO user(username, password, age) VALUES(?, ?, ?)",
            array('autobind', '123456', 10));
        $db->query("INSERT INTO user(username, password, age) VALUES(?, ?, ?)",
            array('autobind', '123456', 10));
        $rt = $db->get_grid("SELECT username, password, age FROM user WHERE username=?", array('autobind'));
        $this->assertEquals(array(
            array('username' => 'autobind', 'password' => '123456', 'age' => 10),
            array('username' => 'autobind', 'password' => '123456', 'age' => 10),
            array('username' => 'autobind', 'password' => '123456', 'age' => 10)
        ), $rt);
        
        $rt = $db->get_row("SELECT username, password, age FROM user WHERE username=? LIMIT 1", array('autobind'));
        $this->assertEquals(array('username' => 'autobind', 'password' => '123456', 'age' => 10), $rt);
        
        $rt = $db->get_col("SELECT COUNT(*) AS count FROM user WHERE username=?", array('autobind'));
        $this->assertEquals(3, $rt);
    }
    
    public function test_insert_id()
    {
        $db = new EmpMysql($this->config);
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $this->assertEquals(6, $db->insert_id());
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $this->assertEquals(7, $db->insert_id());
        $db->query("INSERT INTO user(username, password, age) VALUES('insert', 'insert123', 10)");
        $this->assertEquals(8, $db->insert_id());
    }
    
    public function test_affected_rows()
    {
        $db = new EmpMysql($this->config);
        $db->query("UPDATE user SET password='jacky123' WHERE username='jacky'");
        $this->assertEquals(0, $db->affected_rows());
        
        $db->query("UPDATE user SET password='newjacky123' WHERE username='jacky'");
        $this->assertEquals(1, $db->affected_rows());
        
        $db->query("UPDATE user SET password='newpassword'");
        $this->assertEquals(5, $db->affected_rows());
    }
    
    // this case, mysql.trace_mode=Off
    public function _test_error()
    {
        $db = new EmpMysql($this->config);
        $db->query("INSERT INTO user(xusername, password, age) VALUES('insert', 'insert123', 10)");
        $this->assertEquals(1054, $db->errno());
        $this->assertEquals("Unknown column 'xusername' in 'field list'", $db->error());
    }
}