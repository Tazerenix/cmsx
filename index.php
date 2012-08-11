<?php
try
{
define('CMSX_ROOT', getcwd());

require_once(CMSX_ROOT . '/includes/core.php');

echo $user->isLoggedin() ? 'yes' : 'no';
}
catch (dbException $e)
{
    $e->triggerError();
}
?>
echo