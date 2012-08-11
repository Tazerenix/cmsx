<?php

final class modules
{
    private static $modules = array();
    
    final private static function validModule($module)
    {
        return true;
    }
    
    final public static function initModules()
    {
        foreach (scandir(CMSX_ROOT . '/includes/modules/') as $module)
        {
            if ($module[0] == '.' || !is_dir(CMSX_ROOT . '/includes/modules/' . $module))
            {
                continue;
            }
            
            if (self::validModule($module))
            {
                self::loadModule($module);
            }
            else
            {
                throw new cmsxException('There is an invalid module in ' . CMSX_ROOT_URL . '/includes/modules/', 'Invalid Module', EXCEPTION_CORE, 'Module "' . $module . '" found to be invalid');
            }
        }
    }
    
    final private static function loadModule($module)
    {
        if (array_search($module, self::$modules))
        {
            return;
        }
        
        $load = CMSX_ROOT . '/includes/modules/' . $module . '/' . $module . '.class.php';
        $loadInfo = pathinfo($load);
        
        try
        {
            if (is_dir($loadInfo['dirname']))
            {
                if (is_file($loadInfo['dirname'] . '/' . $loadInfo['filename'] . '.' . $loadInfo['extension']))
                {
                    if (DEBUG == true)
                    {
                        logger::log('Attempting to include module "' . $module . '" from file "' . $load . '"');
                    }
                }
                else
                {
                    throw new cmsxException('Module class "' . $module . '.class.php" not found.', 'Module class not found', 2);
                }
            }
            else
            {
                throw new cmsxException('Module "' . $module . '" not found.', 'Module not found', 2);
            }
        }
        catch (cmsxException $e)
        {
            $e->triggerError();
        }

        if (include_once($load))
        {
            if (DEBUG == true)
            {
                logger::log('Module "' . $module . '" successfully included from file "' . $load . '"');
            }
            self::$modules[] = $module;
        }
    }
}

?>
