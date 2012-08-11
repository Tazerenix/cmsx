<?php

/*
 * First we have to check if PDO exists. It should do if you are using PHP 5.1.0 or later
 */
try
{
    if (!class_exists('PDO'))
    {
        throw new cmsxException(dbException::PDO_DISABLED);
    }
}
catch (cmsxException $e)
{
    $e->triggerError();// Deal with exception
}

/**
 * Table definitions
 */
define('TBL_SESSION', TBL_PREFIX . 'session');
define('TBL_USER', TBL_PREFIX . 'user');
define('TBL_ARTICLE', TBL_PREFIX . 'article');
define('TBL_CATEGORY', TBL_PREFIX . 'category');
define('TBL_COMMENT', TBL_PREFIX . 'comment');
define('TBL_LINK', TBL_PREFIX . 'link');
define('TBL_OPTION', TBL_PREFIX . 'option');
define('TBL_PAGE', TBL_PREFIX . 'page');
define('TBL_TAG', TBL_PREFIX . 'tag');

class db
{
    /**
     * This variable represents the PDO object attained in __construct()
     * It is used for queries throughout the class
     * @var PDO 
     */
    private $pdoHandle;
    
    /**
     * A count of the number of queries run by the instance of the db
     * Can be retrieved through external functions by db::getAttribute('queriesMade')
     * @var int 
     */
    private $queriesMade;
    
    
    /**
     * Creates a new database connection and stores the PDO object in $pdoHandle
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param int $method default = db::METHOD_PREPARE
     * @param array $options default = array()
     * @throws dbException 
     */
    public function __construct($dsn, $username, $password, $options = array()) 
    {
        try
        {
            $this->pdoHandle = new PDO($dsn, $username, $password, $options);
        }
        catch (PDOException $e)
        {
            if (is_object($this->pdoHandle))
            {
                $errorInfo = $this->pdoHandle->errorInfo();
            }
            else
            {
                $errorInfo = null;
            }
            throw new dbException(dbException::CONNECTION_ERROR, $errorInfo);
        }
        // Turn off PDO Exceptions, as we are handling exceptions with dbException
        $this->pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    public function runQuery($query, $values = array())
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        $this->queriesMade += $this->queriesMade;
        return $this;
    }
    

    public function getResult($query, $values = array())
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        
        $result = $stmt->fetch(PDO::FETCH_LAZY);
        $this->queriesMade += $this->queriesMade;
        return $result[0];
    }

    public function getRow($query, $values = array())
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->queriesMade += $this->queriesMade;
        return $result;
    }
    
    /**
     * Calls either db::getRowsPrepare or db::getRowsQuery depending on $queryMethod
     * and returns the current db object on success.
     * @param string $query
     * @param array $values default = array()
     * @param int $queryMethod default = null
     * @return mixed
     * @throws dbException 
     */
    public function getRows($query, $values = array(), $queryMethod = null)
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $this->queriesMade += $this->queriesMade;
        return $stmt->fetchAll();
    }

    public function countRows($query, $values = array(), $queryMethod = null)
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        $this->queriesMade += $this->queriesMade;
        return $stmt->rowCount();
    }
    
    /**
     * Returns the last inserted ID. Note this may not work with some database drivers as the function 
     * does not exist in them.
     * @return int 
     */
    public function lastInsertedId()
    {
        return $this->pdoHandle->lastInsertId();
    }
}

?>