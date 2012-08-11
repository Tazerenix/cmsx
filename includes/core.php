<?php

require_once(CMSX_ROOT . '/config.php');
error_reporting(DEBUG ? E_ALL : 0);

function __autoload($class)
{
    include(CMSX_ROOT . '/includes/classes/' . $class . '.class.php');
}

require_once(CMSX_ROOT . '/includes/classes/container.class.php');
modules::initModules();
$user = container::getUser();

?>