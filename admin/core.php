<?php

require_once(CMSX_ROOT . '/config.php');
error_reporting(DEBUG ? E_ALL : 0);

function __autoload($class)
{
    require(CMSX_ROOT . '/includes/classes/' . $class . '.class.php');
}

modules::initModules();
$user = container::getUser();
$admin = container::getAdmin();

?>