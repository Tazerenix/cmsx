<?php

trait userOwner
{
    private $user;
    
    final public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}

trait sessionOwner
{
    private $session;
    
    final public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }
}

trait dbOwner
{
    private $db;

    final public function setDatabase($dbh)
    {
        $this->db = $dbh;
        return $this;
    }
}

/**
 * A singleton wrapper/dependency injection class for all important objects
 */
class container
{
    private static $dbh = null;
    private static $session = null;
    private static $user = null;
    private static $admin = null;
    
    public static function getDb()
    {
        if (!self::$dbh)
        {
            self::$dbh = new db(DSN, DBUSER, DBPASS);
        }
        return self::$dbh;
    }
    
    public static function getSession()
    {
        if (!self::$session)
        {
            self::$session = new session(self::getDb());
        }
        return self::$session;
    }
    
    public static function getUser($user_id = 0)
    {
        if($user_id)
        {
            $u = new user();
            return $u->setDatabase(self::getDb())
                     ->setSession(self::getSession())
                     ->setUser($user_id);
        }
        if (!self::$user)
        {
            self::$user = new user();
            self::$user->setDatabase(self::getDb());
            self::$user->setSession(self::getSession());
            self::$user->setUser($user_id);
        }
        return self::$user;
    }
    
    public static function getAdmin()
    {
        if (!self::$admin)
        {
            self::$admin = new admin();
            self::$admin->setDatabase(self::getDb());
            self::$admin->setUser(self::getUser());
        }
        return self::$admin;
    }
}

?>