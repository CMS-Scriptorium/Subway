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

trait RequestStrings
{
    /**
     *
     * @param mixed $value      The currend value; call by reference
     * @param mixed $default    The default value
     * @param array $options    Call by reference
     *
     * @return void Nothing
     */
    protected function handleStrRange(
        mixed &$value,
        mixed $default,
        array &$options
    ): void
    {
        if (isset($options['max']))
        {
            $value = substr($value, 0, intval($options['max']));
        }
        if (isset($options['min']))
        {
            $x = strlen($value);
            $add = \str_repeat(($options['fill'] ?? " "), \intval($options['min']) - $x);
            if (($options['prepend'] ?? false) === true)
            {
                $value = $add . $value;
            } else
            {
                $value .= $add;
            }
        }
        if (isset($options['default']) && ($options['default']))
        {
            $value  = $default;
        }
    }
}
