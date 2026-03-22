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
     * Internal Function 'handleStrRange'
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
            if ($x < \intval($options['min']))
            {
                $is = \intval($options['min']) - $x;
                $add = \str_repeat(($options['fill'] ?? " "), $is);
                if (($options['prepend'] ?? false) === true)
                {
                    $value = $add . $value;
                } else
                {
                    $value .= $add;
                }
            }
        }
        if (isset($options['default']) && ($options['default']))
        {
            $value  = $default;
        }
    }

    /**
     * handleEmail
     * Also not clear at all.
     *
     * @param mixed $value      The given value (call-by-reference).
     * @param mixed $default    The given default.
     * @param array $options    The options (call-by-reference).
     *
     * @return void
     */
    protected function handleEmail(
        mixed &$value,
        mixed $default,
        array &$options
    ): void
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $value = $options['default'] ?? $default;
        }
    }

    /**
     * handleRegexpr
     * Notice - at this time it is not clear in witch was to handle this!
     *
     * @param mixed $value      A given value (call-by-reference).
     * @param mixed $default    A given default.
     * @param array $options    The options as array (call-by-reference).
     *
     * @return void
     */
    protected function handleRegexpr(
        mixed &$value,
        mixed $default,
        array &$options
    ): void
    {
        if (isset($options['pattern']) && (!empty($options['pattern'])))
        {
            $results = [];
            preg_match($options['pattern'], $value, $results);

            if (empty($results))
            {
                $value = $options['default'] ?? $default;
            }
        }
    }
}
