<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.1
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace Subway\core;

class Autoloader
{
    public static $instance;

    /**
     *  Return the instance of this class.
     *
     */
    public static function getInstance()
    {
        if (null === static::$instance)
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct()
    {
        spl_autoload_register([__CLASS__, 'subwayAutoload'], true, true);
    }
    
    /**
     * Handles autoload of classes with namespace "addon\any\where\class" from WebsiteBaker
     *
     * @param string $class A class name.
     *
     * @return bool
     */
    public static function subwayAutoload(string $class): bool
    {
        if (str_starts_with($class, 'addon\\')) // Hack for WBCE
        {
            
            $elements = explode("\\", $class);
            $elements[0] = "modules";
            
            $lookupPath = WB_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $elements) . ".php";
            if (file_exists($lookupPath))
            {
                require_once $lookupPath;
                return true;
            }
        
            return false;
        }
        return false;
    }
}
