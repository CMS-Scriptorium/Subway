<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

/**
 * Trait for singleton instances.
 *
 * @See: https://de.wikipedia.org/wiki/Singleton_(Entwurfsmuster)
 */

namespace Subway\core\traits;

trait Singleton
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
            static::$instance = new static( func_get_args() );
        }
        return static::$instance;
    }
}