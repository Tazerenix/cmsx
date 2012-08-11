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
     * Defines which method is used to run queries.
     * Either PDO::Query() or PDO::Prepare()
     * @var int 
     */
    private $queryMethod;
    
    /**
     * A count of the number of queries run by the instance of the db
     * Can be retrieved through external functions by db::getAttribute('queriesMade')
     * @var int 
     */
    private $queriesMade;
    
    const METHOD_QUERY = 1;
    const METHOD_PREPARE = 2;
    
    /**
     * Creates a new database connection and stores the PDO object in $pdoHandle
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param int $method default = db::METHOD_PREPARE
     * @param array $options default = array()
     * @throws dbException 
     */
    public function __construct($dsn, $username, $password, $method = self::METHOD_PREPARE, $options = array()) 
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
        $this->queryMethod = $method;
    }

    /**
     * Used to get the values of class properties.
     * Currently the only properties available are $queryMethod and $queriesMade but it is left open to be expanded later.
     * @param string $attr
     * @return mixed 
     */
    public function getAttribute($attr)
    {
        if (isset($this->$attr) && $attr != 'pdoHandle')
        {
            return $this->$attr;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Allows the value of class attributes to be changed. Currently the only attribute that can be changed is $queryMethod
     * but it's left open for more properties in the future.
     * @param string $attr
     * @param mixed $value
     * @return \db|boolean
     * @throws dbException 
     */
    public function setAttribute($attr, $value)
    {
        switch ($attr)
        {
            case 'queryMethod':
                $this->queryMethod = $value == self::METHOD_QUERY ? self::METHOD_QUERY : self::METHOD_PREPARE;
                return $this;
                break;
                
            case 'queriesMade':
            default:
                return false;
                break;
        }
    }
    
    /**
     * Calls either db::runQueryPrepare or db::runQueryQuery depending on $queryMethod
     * and returns the current db object on success.
     * @param string $query
     * @param array $values default = array()
     * @param int $queryMethod default = null
     * @return mixed
     * @throws dbException 
     */
    public function runQuery($query, $values = array(), $queryMethod = null)
    {
        $method = $queryMethod ? $queryMethod : $this->getAttribute('queryMethod');
        switch ($method)
        {
            case self::METHOD_QUERY:
                $result = $this->runQueryQuery($query);
                $this->queriesMade++;
                return $result;
                break;
            
            case self::METHOD_PREPARE:
            default:
                $result = $this->runQueryPrepare($query, $values);
                $this->queriesMade++;
                return $result;
                break;
        }
    }
    
    /**
     * Runs a query that is not expected to return any results using PDO::prepare. 
     * @param string $query
     * @param array $values
     * @return \db
     * @throws dbException 
     */
    private function runQueryPrepare($query, $values)
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        
        return $this;
    }
    
    /**
     * Runs a query that is not expected to return any results using PDO::query.
     * @param string $query
     * @return \db
     * @throws dbException 
     */
    private function runQueryQuery($query)
    {
        if (!$stmt = $this->pdoHandle->query($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        return $this;
    }
    
    /**
     * Calls either db::getResultPrepare or db::getResultQuery depending on $queryMethod
     * and returns the result on success.
     * @param string $query
     * @param array $values default = array()
     * @param int $queryMethod default = null
     * @return mixed
     * @throws dbException 
     */
    public function getResult($query, $values = array(), $queryMethod = null)
    {
        $method = $queryMethod ? $queryMethod : $this->getAttribute('queryMethod');
        switch ($method)
        {
            case self::METHOD_QUERY:
            
                $result = $this->getResultQuery($query);
                $this->queriesMade++;
                return $result;
                break;
            
            case self::METHOD_PREPARE:
            default:
                $result = $this->getResultPrepare($query, $values);
                $this->queriesMade++;
                return $result;
                break;
        }
    }

    /**
     * Gets the single scalar value result of a query using the PDO::prepare method. 
     * @param string $query
     * @param array $values
     * @return mixed
     * @throws dbException 
     */
    private function getResultPrepare($query, $values)
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
        return $result[0];
    }
    
    /**
     * Gets the single scalar value result of a query using the PDO::query method. 
     * @param string $query
     * @return mixed
     * @throws dbException 
     */
    private function getResultQuery($query)
    {
        if (!$stmt = $this->pdoHandle->query($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        $result = $stmt->fetch(PDO::FETCH_LAZY);
        return $result[0];
    }
    
    /**
     * Calls either db::getRowPrepare or db::getRowQuery depending on $queryMethod
     * and returns the current db object on success.
     * @param string $query
     * @param array $values default = array()
     * @param int $queryMethod default = null
     * @return mixed
     * @throws dbException 
     */
    public function getRow($query, $values = array(), $queryMethod = null)
    {
        $method = $queryMethod ? $queryMethod : $this->getAttribute('queryMethod');
        switch ($method)
        {
            case self::METHOD_QUERY:
                $result = $this->getRowQuery($query);
                $this->queriesMade++;
                return $result;
                break;
            
            default:
            case self::METHOD_PREPARE:
                $result = $this->getRowPrepare($query, $values);
                $this->queriesMade++;
                return $result;
                break;
        }
    }
    
    /**
     * Gets the single row array result of a query using the PDO::prepare method. 
     * @param string $query
     * @param array $values
     * @return mixed
     * @throws dbException 
     */
    private function getRowPrepare($query, $values)
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
        return $result;
    }
    
    /**
     * Gets the single row array result of a query using the PDO::query method. 
     * @param string $query
     * @return mixed
     * @throws dbException 
     */
    private function getRowQuery($query)
    {
        if (!$stmt = $this->pdoHandle->query($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
        $method = $queryMethod ? $queryMethod : $this->getAttribute('queryMethod');
        switch ($method)
        {
            case self::METHOD_QUERY:
                $result = $this->getRowsQuery($query);
                $this->queriesMade++;
                return $result;
                break;
            
            default:
            case self::METHOD_PREPARE:
                $result = $this->getRowsPrepare($query, $values);
                $this->queriesMade++;
                return $result;
                break;
        }
    }
    
    /**
     * Gets the multi row array result of a query using the PDO::prepare method as a multidimensional array. 
     * @param string $query
     * @param array $values
     * @return mixed
     * @throws dbException 
     */
    private function getRowsPrepare($query, $values)
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
        return $stmt->fetchAll();
    }
    
    /**
     * Gets the multi row array result of a query using the PDO::query method as a multidimensional array. 
     * @param string $query
     * @return mixed
     * @throws dbException 
     */
    private function getRowsQuery($query)
    {
        if (!$stmt = $this->pdoHandle->query($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }
    
    /**
     * Calls either db::countRowsPrepare or db::countRowsQuery depending on $queryMethod
     * and returns the number of rows affected by the query.
     * @param string $query
     * @param array $values default = array()
     * @param int $queryMethod default = null
     * @return int
     * @throws dbException 
     */
    public function countRows($query, $values = array(), $queryMethod = null)
    {
        $method = $queryMethod ? $queryMethod : $this->getAttribute('queryMethod');
        switch ($method)
        {
            case self::METHOD_QUERY:
                $result = $this->countRowsQuery($query);
                $this->queriesMade++;
                return $result;
                break;
            
            default:
            case self::METHOD_PREPARE:
                $result = $this->countRowsPrepare($query, $values);
                $this->queriesMade++;
                return $result;
                break;
        }
    }
    
    /**
     * Gets the number of rows affected by a query using the PDO::prepare method. 
     * @param string $query
     * @param array $values
     * @return int
     * @throws dbException 
     */
    private function countRowsPrepare($query, $values)
    {
        if (!$stmt = $this->pdoHandle->prepare($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
        if (!$stmt->execute($values))
        {
            throw new dbException(dbException::QUERY_FAILURE, $stmt->errorInfo());
        }
        
        return $stmt->rowCount();
    }
    
    /**
     * Gets the number of rows affected by a query using the PDO::query method. 
     * @param string $query
     * @return int
     * @throws dbException 
     */
    private function countRowsQuery($query)
    {
        if (!$stmt = $this->pdoHandle->query($query))
        {
            throw new dbException(dbException::QUERY_FAILURE);
        }
        
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