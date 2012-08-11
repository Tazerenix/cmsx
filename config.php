<?php

if (!defined('CMSX_ROOT'))
{
    die('Unauthorized Access');
}

define('DEBUG', true);
define('CMSX_ROOT_URL', 'http://localhost/cms');
define('LOG', CMSX_ROOT . 'log.txt');

/* Database */
define('DSN', 'mysql:host=localhost;dbname=cmsx');
define('DBUSER', 'root');
define('DBPASS', '');
define('TBL_PREFIX', 'cmsx_');

?>