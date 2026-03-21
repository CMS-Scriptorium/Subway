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

namespace Subway\core\traits;

trait RequestNumbers
{
    /**
     * 
     * @param mixed $value      The currend value; call by reference
     * @param mixed $default    The default value
     * @param array $options    Call by reference
     * 
     * @return void Nothing
     */
    static function handleIntRange(
        mixed &$value, 
        mixed $default,
        array &$options
    ): void
    {
        if (isset($options['min']))
        {
            $iMin = intval($options['min']);
            if ($value < $iMin)
            {
                $value = $options['default'] ?? $default;
            }
        }
        if (isset($options['max']))
        {
            $iMax = intval($options['max']);
            if ($value > $iMax)
            {
                $value = $options['default'] ?? $default;
            }
        }
    }
}
