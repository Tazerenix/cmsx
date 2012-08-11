<?php

class logger
{
    public static function log($string)
    {
        $time = date('Y-m-d H:i:s');
        file_put_contents(LOG, $time . ' - ' . $string . "\n", FILE_APPEND);
    }
}

?>