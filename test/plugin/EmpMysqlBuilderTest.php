<?php

// @date 2012-04-17
// @author mahone

require_once __DIR__ . '/../../src/plugin/class/EmpMysqlBuilder.php';

class EmpMysqlBuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    public function tearDown()
    {
        parent::tearDown();
    }
    
    public function test_build_sql_insert()
    {
        $builder = new EmpMysqlBuilder();
        $this->assertEquals("INSERT INTO test_user(username,password,age) VALUES('jacky','123456',10)", $builder->build_sql_insert('test_user', array(
            'username' => 'jacky',
            'password' => '123456',
            'age' => 10
        )));
    }
    
    public function test_build_sql_update()
    {
        $builder = new EmpMysqlBuilder();
        $this->assertEquals("UPDATE test_user SET username='jacky',age=10,test='tes\\\"asd\'123'", $builder->build_sql_update('test_user', array(
            'username' => 'jacky',
            'age' => 10,
            'test' => 'tes"asd\'123'
        )));
        
        // add where sql
        $builder->where("username='test'")->where(array(
            'password' => '123456',
            'age>=' => 10
        ));
        $this->assertEquals("UPDATE test_user SET username='jacky',age=10 WHERE username='test' AND password='123456' AND age>=10", $builder->build_sql_update('test_user', array(
            'username' => 'jacky',
            'age' => 10
        )));
    }
    
    public function test_build_sql_delete()
    {
        $builder = new EmpMysqlBuilder();
        $this->assertEquals("DELETE FROM test_user", $builder->build_sql_delete('test_user'));
        
        // add where sql
        $builder->where("username='test'")->where(array(
            'password' => '123456',
            'age>=' => 10
        ));
        $this->assertEquals("DELETE FROM test_user WHERE username='test' AND password='123456' AND age>=10", $builder->build_sql_delete('test_user'));
    }
    
    public function test_build_sql_select()
    {
        $builder = new EmpMysqlBuilder();
        $builder->select('username,password,age')->from('test_user')->where('username=1')->where(array(
            'password' => '123456',
            'age>=' => 10
        ))->like("like_test like '%test0%'")->like(array(
            array('like_test', 'test1', 'before'),
            array('like_test', 'test2', 'after'),
            array('like_test', 'test3', 'both'),
        ))->group_by('group0')->group_by(
            array('group1', 'group2', 'group3')
        )->having('count(id)>10')->having(array(
            'count(id)<=' => 100
        ))->order_by('order0 ASC')->order_by(array(
            'order1 ASC',
            'order2 DESC'
        ))->limit(0, 10);
        $this->assertEquals("SELECT username,password,age FROM test_user WHERE username=1 AND password='123456' AND age>=10 AND like_test like '%test0%' AND like_test LIKE '%test1' AND like_test LIKE 'test2%' AND like_test LIKE '%test3%' GROUP BY group0,group1,group2,group3 HAVING count(id)>10 AND count(id)<=100 ORDER BY order0 ASC,order1 ASC,order2 DESC LIMIT 0, 10", $builder->build_sql_select());
    }
}