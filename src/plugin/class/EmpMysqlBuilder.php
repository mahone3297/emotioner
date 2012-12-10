<?php

// @date 2012-04-17
// @author mahone

class EmpMysqlBuilder
{
    protected $ar_select = array();
    protected $ar_from = '';
    // protected $ar_join = array();
    protected $ar_where = array();
    protected $ar_like = array();
    protected $ar_groupby = array();
    protected $ar_having = array();
    protected $ar_orderby = array();
    protected $ar_limit = '';
    // protected $ar_keys = array();
    
    public function __construct()
    {
    }
    
    public function reset()
    {
        $this->ar_select = array();
        $this->ar_from = '';
        $this->ar_where = array();
        $this->ar_like = array();
        $this->ar_groupby = array();
        $this->ar_having = array();
        $this->ar_limit = '';
        $this->ar_orderby = array();
    }
    
    // select
    public function select($select)
    {
        $select = explode(',', $select);
        
        foreach ($select as $val){
            $val = trim($val);
            
            if ($val == '') { continue; }
            
            $this->ar_select[] = $val;
        }
        
        return $this;
    }
    
    // from
    public function from($from)
    {
        $this->ar_from = trim($from);
        
        return $this;
    }
    
    // where
    public function where($where)
    {
        if (is_string($where)){
            $where = trim($where);
            
            if ($where == '') { return $this; }
            
            $this->ar_where[] = $where;
        } else if (is_array($where)){
            if ( ! empty($where)){
                foreach ($where as $key=>$val){
                    if ($this->has_operator($key)){
                        $this->ar_where[] = $key . $this->escape($val);
                    } else {
                        $this->ar_where[] = $key . '=' . $this->escape($val);
                    }
                }
            }
        }
        
        return $this;
    }
    
    // like
    public function like($like)
    {
        if (is_string($like)){
            $like = trim($like);
            
            if ($like == '') { return $this; }
            
            $this->ar_like[] = $like;
        } else if (is_array($like)){
            if ( ! empty($like)){
                foreach ($like as $val){
                    $type = isset($val[2]) ? strtolower($val[2]) : 'both';
                    $field = $val[0];
                    $match = $val[1];
                    $match = ($type=='before' || $type=='both') ? "'%".$match : "'".$match;
                    $match = ($type=='after' || $type=='both') ? $match."%'" : $match."'";
                    $this->ar_like[] = $field . ' LIKE ' . $match;
                }
            }
        }
        
        return $this;
    }
    
    // group by
    public function group_by($group_by)
    {
        if (is_string($group_by)){
            $group_by = trim($group_by);
            
            if ($group_by == '') { return $this; }
            
            $this->ar_groupby[] = $group_by;
        } else if (is_array($group_by)){
            if ( ! empty($group_by)){
                foreach ($group_by as $val){
                    $this->ar_groupby[] = $val;
                }
            }
        }
        
        return $this;
    }
    
    // having
    public function having($having)
    {
        if (is_string($having)){
            $having = trim($having);
            
            if ($having == '') { return $this; }
            
            $this->ar_having[] = $having;
        } else if (is_array($having)){
            if ( ! empty($having)){
                foreach ($having as $key=>$val){
                    if ($this->has_operator($key)){
                        $this->ar_having[] = $key . $this->escape($val);
                    } else {
                        $this->ar_having[] = $key . '=' . $this->escape($val);
                    }
                }
            }
        }
        
        return $this;
    }
    
    // order by
    public function order_by($order_by)
    {
        if (is_string($order_by)){
            $order_by = trim($order_by);
            
            if ($order_by == '') { return $this; }
            
            $this->ar_orderby[] = $order_by;
        } else if (is_array($order_by)){
            if ( ! empty($order_by)){
                foreach ($order_by as $val){
                    $this->ar_orderby[] = $val;
                }
            }
        }
        
        return $this;
    }
    
    // limit
    public function limit($offset, $len)
    {
        $this->ar_limit = ' LIMIT ' . $offset . ', ' . $len;
        
        return $this;
    }
    
    // @access protected
    protected function has_operator($str)
    {
        $str = trim($str);
        if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str)){
            return false;
        }

        return true;
    }
    
    // @access protected
    protected function build_sql_where()
    {
        if (empty($this->ar_where) && empty($this->ar_like)){
            return '';
        }
        
        return ' WHERE ' . implode(' AND ', array_merge($this->ar_where, $this->ar_like));
    }
    
    // build sql insert
    public function build_sql_insert($table_name, $param)
    {
        if (empty($param)){
            return false;
        }
        
        $sql_key_array = array();
        $sql_val_array = array();
        foreach ($param as $key=>$val){
            $sql_key_array[] = $key;
            $sql_val_array[] = $this->escape($val);
        }
        $sql = 'INSERT INTO ' . $table_name . '(' . implode(',', $sql_key_array) . ') VALUES(' . implode(',', $sql_val_array) . ')';
        
        $this->reset();
        return $sql;
    }
    
    // build sql update
    public function build_sql_update($table_name, Array $data)
    {
        $sql_data_array = array();
        
        $sql = 'UPDATE ' . $table_name . ' SET ';
        if ( ! empty($data)){
            foreach ($data as $key=>$val){
                if ($this->has_operator($key)){
                    $sql_data_array[] = $key . $this->escape($val);
                } else {
                    $sql_data_array[] = $key . '=' . $this->escape($val);
                }
            }
        }
        $sql .= implode(',', $sql_data_array);
        $sql .= $this->build_sql_where();
        
        $this->reset();
        return $sql;
    }
    
    // build sql delete
    public function build_sql_delete($table_name)
    {
        $sql = 'DELETE FROM ' . $table_name;
        $sql .= $this->build_sql_where();
        
        $this->reset();
        return $sql;
    }
    
    // build sql select
    public function build_sql_select()
    {
        $sql = 'SELECT ' . implode(',', $this->ar_select);
        $sql .= ' FROM ' . $this->ar_from;
        $sql .= $this->build_sql_where();
        $sql .= empty($this->ar_groupby) ? '' : ' GROUP BY ' . implode(',', $this->ar_groupby);
        $sql .= empty($this->ar_having) ? '' : ' HAVING ' . implode(' AND ', $this->ar_having);
        $sql .= empty($this->ar_orderby) ? '' : ' ORDER BY ' . implode(',', $this->ar_orderby);
        $sql .= $this->ar_limit;
        
        $this->reset();
        return $sql;
    }
    
    // escape
    public function escape($str)
    {
        if (is_string($str)){
            $str = "'" . addslashes($str) . "'";
        } else if (is_bool($str)){
            $str = ($str === false) ? 0 : 1;
        } else if (is_null($str)){
            $str = 'NULL';
        }
        
        return $str;
    }
}