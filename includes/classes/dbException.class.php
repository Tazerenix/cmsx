<?php

class dbException extends cmsxException
{
    const PDO_DISABLED = 5;
    const CONNECTION_ERROR = 4;
    const QUERY_FAILURE = 3;
    
    private $responses = array(
        self::PDO_DISABLED => array(
            'header' => 'PDO Unavailable',
            'message' => 'PDO is not installed or enabled on this system.'
        ),
        self::CONNECTION_ERROR => array(
            'header' => 'Unable to connect to Database',
            'message' => 'CMSx was unable to connect to the database. Please try again later.'
        ),
        self::QUERY_FAILURE => array(
            'header' => 'Database query failure',
            'message' => 'A query to the database has failed. Please try again later.'
        )
    );

    
    public function __construct($code = cmsxException::UNKNOWN, $error = array()) 
    {
        if (!isset($this->responses[$code]))
        {
            $code = cmsxException::UNKNOWN;
        }
        
        $header = $this->responses[$code]['header'];
        $message = $this->responses[$code]['message'];
        
        if ($error)
        {
            $technicalMessage = 'Reported Issue: (' . $error[0] . ') ' . $error[2];
        }
        else
        {
            $technicalMessage = 'No technical information available.';
        }
        
        
        parent::__construct($message, $header, $technicalMessage, $code);     
    }
}

?>