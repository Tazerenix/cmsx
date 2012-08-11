<?php

class cmsxException extends Exception
{
    const UNKNOWN = 0; 
    
    private $technicalMessage; // only displayed in debug mode
    private $header;
    
    public function __construct($message, $header, $technicalMessage, $code)
    {
        try
        {
            if (!strlen($message))
            {
                throw new cmsxException('An exception was thrown without a message specified.', 'Improper Exception Thrown', EXCEPTION_CORE);
            }
        }
        catch (coreException $e)
        {
            $e->triggerError();
        }
        
        $this->message = $message;
        $this->header = $header;
        $this->technicalMessage = $technicalMessage;
        $this->code = $code;
    }
    
    final public function setHeader($string)
    {
        $this->header = $string;
    }
    
    final public function getHeader()
    {
        return $this->header;
    }
    
    final public function setTechnicalMessage($string)
    {
        $this->technicalMessage = $string;
    }
    
    final public function getTechnicalMessage()
    {
        return $this->technicalMessage;
    }
    
    final public function triggerError()
    {
        $this->displayError();
        switch ($this->code)
        {
            case cmsxException::UNKNOWN:
                if (DEBUG == TRUE)
                {
                    logger::log('An error has occured: ' . $this->message);
                }
                break;
                
            default:
                logger::log('An error has occured: ' . $this->message);
                break;
        }
        exit;
    }
    
    final private function displayError()
    {
        echo '
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="' . CMSX_ROOT_URL . '/includes/scripts/css/error.css" />
        <title>' . $this->getHeader() . '</title>               
    </head>
    <body>
    <div id="error"><div id="header">Error: ';
        echo $this->getHeader();
        echo '</div>
    <div id="content">
    <p>
    ' . $this->getMessage() . '
    </p>
    Technical Information:
    <div class="technicalInformation">
    ' . $this->getTechnicalMessage() . '
    </div>

    </div>
    </div>
        </body></html>';
    }
}

?>