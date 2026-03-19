<?php

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace Subway\core\traits;

trait Constants
{
    /**
     * Get the constants of the further inherited class.
     *
     * @return array
     */
    public static function getConstants(): array
    {
        // "static::class" here does the magic
        try {
            $reflectionClass = new \ReflectionClass(static::class);
            return $reflectionClass->getConstants();

        } catch ( \ReflectionException $e) {
            \Subway\core\tools\data::display($e->getMessage());
        }
        return [];
    }
}
