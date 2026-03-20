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

namespace Subway\core;

use Subway\core\traits\Singleton;

/**
 * Description of Request
 *
 * @author work
 */
class Request
{

    use Singleton;

    public const USE_POST = "post";
    public const USE_GET = "get";
    public const USE_REQUEST = "request";
    public const USE_SESSION = "session";
    public const USE_SERVER = "server";
    public const USE_ENV = "env";

    public static $instance;

    protected function __construct()
    {
        
    }

    /**
     * 
     * @param string $where
     * @param string $name
     * @param string $type
     * @param mixed $default
     * @param array $options
     * @return type
     */
    public function getValue(string $where, string $name, string $type, mixed $default, array $options = []): mixed
    {

        switch ($where)
        {
            case self::USE_POST :
                $refLookUp = &$_POST;
                break;

            case self::USE_GET :
                $refLookUp = &$_GET;
                break;

            case self::USE_REQUEST :
                $refLookUp = &$_REQUEST;
                break;

            case self::USE_SESSION :
                $refLookUp = &$_SESSION;
                break;

            case self::USE_SERVER :
                $refLookUp = &$_SERVER;
                break;

            case self::USE_ENV :
                $refLookUp = &$_ENV;
                break;
        }

        $tempVal = $refLookUp[$name] ?? $default;
        $retVal = $this->testValue($tempVal, $type, $default);

        $this->handleOptions($retVal, $type, $options);

        return $retVal;
    }

    /**
     * 
     * @param mixed $value
     * @param string $type
     * @param mixed $default
     * @return mixed
     */
    protected function testValue(mixed $value, string $type, mixed $default): mixed
    {
        $retVal = NULL;

        switch(strtolower($type))
        {
            case 'i':
            case 'integer':
                $retVal = (is_numeric($value))
                    ? intval($value)
                    : $default
                    ;
                break;

            case 's':
            case 'strip':
            case 'string':
            case 'email':
                $retVal = (is_string($value))
                    ? $value
                    : $default
                    ;
                break;

            default:
                $retVal = $value;
                break;
        }
        
        return $retVal;
    }

    /**
     * 
     * @param mixed $value
     * @param string $type
     * @param array $options
     */
    protected function handleOptions(mixed &$value, string $type, array $options)
    {
        if (!empty($options))
        {
            switch($type)
            {
                case 'i':
                case 'integer':
                    if (isset($options['min']))
                    {
                        $iMin = intval($options['min']);
                        if ($value < $iMin)
                        {
                            $value = $options['default'] ?? $iMin;
                        }
                    }
                    if (isset($options['max']))
                    {
                        $iMax = intval($options['max']);
                        if ($value > $iMax)
                        {
                            $value = $options['default'] ?? $iMax;
                        }
                    }
                    break;

                case 's':
                case 'string':
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
                    break;

                case 'strip':
                    $value = strip_tags($value, ($options['allowed'] ?? ""));
                    break;

                case 'regexpr':
                    if ((isset($options['pattern']) && (!empty($options['pattern']))))
                    {
                        $results = [];
                        preg_match($options['pattern'], $value, $results);
                        
                        if (empty($results))
                        {
                            $value = $options['default'] ?? "";
                        }
                    }
                    break;

                case "email":
                    if (false === filter_var($value, FILTER_VALIDATE_EMAIL))
                    {
                        $value = $options['default'] ?? "";
                    }
                    break;
            }
        }
    }
}
