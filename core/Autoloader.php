<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.1
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.x
 */

namespace Subway\core;

class Autoloader
{

    /** @var self|null */
    private static ?self $instance = null;

    /**
     * Return the instance of this class.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (null === static::$instance)
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct()
    {
        // register the autoloader; throw on failure and prepend it
        spl_autoload_register([static::class, 'subwayAutoload'], true, true);
    }

    /**
     * Handles autoload of classes with namespace "addon\any\where\class" from WebsiteBaker
     *
     * @param   string $classname   A class name (fully qualified).
     *
     * @return  bool    True if the class file was found and required.
     */
    public static function subwayAutoload(string $classname): bool
    {
        // normalize leading backslash if present
        $class = ltrim($classname, '\\');

        // quick, case-insensitive check for "addon\" prefix
        if (str_starts_with($class, 'addon\\') || strncasecmp($class, 'addon\\', 6) === 0)
        {
            $elements = explode("\\", $class);

            // map top-level 'addon' (any case) to 'modules'
            if (!empty($elements) && (strcasecmp($elements[0], 'addon') === 0))
            {
                $elements[0] = 'modules';

                return self::requireFile(
                    WB_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $elements) . '.php'
                );
            }
        }

        return false;
    }

    /**
     * Require once a given file(-path).
     *
     * @param   string  $lookupPath The given file(-path) to load.
     * @return  bool    True if it is readable (exists), false if not.
     */
    public static function requireFile(string $lookupPath): bool
    {
        if (is_readable($lookupPath))
        {
            require_once $lookupPath;
            return true;
        }

        return false;
    }
}
