<?php

class session
{
    private $db;
    private $alive = true;
    
    public function __construct($dbh)
    {
        $this->db = $dbh;
        session_set_save_handler(array(&$this, 'open'),
                                 array(&$this, 'close'),
                                 array(&$this, 'read'),
                                 array(&$this, 'write'),
                                 array(&$this, 'destroy'),
                                 array(&$this, 'gc'));
        session_start();
    }
    
    public function __destruct()
    {
        if ($this->alive)
        {
            session_write_close();
            $this->alive = false;
        }
    }
    
    public function setVal($key, $value)
    {
        $_SESSION[$key] = $value;
        return true;
    }
    
    public function getVal($key)
    {
        if (isset($_SESSION[$key]))
        {
            return $_SESSION[$key];
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Open the session
     * @return bool
     */
    public function open() 
    {
        return true;
    }

    /**
     * Close the session
     * @return bool
     */
    public function close() 
    {
        return true;
    }

    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id) 
    {
        try
        {
            $result = $this->db->getResult('SELECT data FROM '.TBL_SESSION.' WHERE id = :id', array(':id' => $id));
        }
        catch (dbException $e)
        {
            return false;
        }
        return $result ? $result : '';
    }

    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data) 
    {
        try
        {
            $this->db->runQuery('DELETE FROM '.TBL_SESSION.' WHERE id = :id', array(':id' => $id));
            $this->db->runQuery('INSERT INTO '.TBL_SESSION.' (id, data) VALUES (:id , :data)', array(':id' => $id, ':data' => $data));
        }
        catch (dbException $e)
        {
            return false;
        }
        return true;

    }

    /**
     * Destory the session
     * @param int session id
     * @return bool
     */
    public function destroy($id) 
    {
        try
        {
            $this->db->runQuery('DELETE FROM '.TBL_SESSION.' WHERE id = :id', array(':id' => $id));
        }
        catch (dbException $e)
        {
            return false;
        }
        return true;
    }

    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public function gc($max) 
    {
        try
        {
            $this->db->runQuery('DELETE FROM '.TBL_SESSION.' WHERE timestamp < FROM_UNIXTIME(:time)', array(':time' => time() - $max));
        }
        catch (dbException $e)
        {
            return false;
        }
        return true;
    }

}



?>