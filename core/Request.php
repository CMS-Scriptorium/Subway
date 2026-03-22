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
use Subway\core\traits\RequestNumbers;
use Subway\core\traits\RequestStrings;

/**
 * Description of Request
 *
 * @author work
 */
class Request
{

    use Singleton;
    use RequestNumbers;
    use RequestStrings;

    public const USE_POST = "post";
    public const USE_GET = "get";
    public const USE_REQUEST = "request";
    public const USE_SESSION = "session";
    public const USE_SERVER = "server";
    public const USE_ENV = "env";

    public static $instance;

    protected function __construct()
    {
        // Nothing to do here now.
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

            default:
                die("Unsupported lookup!");
                break;
        }

        $tempVal = $refLookUp[$name] ?? $default;
        $retVal = $this->testValue($tempVal, $type, $default);

        if ($retVal !== $default)
        {
            $this->handleOptions($retVal, $type, $default, $options);
        }

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
        $retVal = null;

        switch(strtolower($type))
        {
            case 'i':
            case 'int':
            case 'int+':
            case 'i+':
            case 'integer':
            case 'integer+':
                $retVal = (is_numeric($value))
                    ? intval($value)
                    : $default
                    ;
                break;

            case 's':
            case 'str':
            case 'strip':
            case 'string':
                $retVal = (is_string($value))
                    ? $value
                    : $default
                    ;
                break;

            case 'email':
                $sTempResult = trim($value);
                $retVal = (false === filter_var($sTempResult, FILTER_VALIDATE_EMAIL))
                    ? $default
                    : $sTempResult
                    ;
                break;

            default:
                $retVal = $value;
                break;
        }
        
        return $retVal;
    }

    /**
     *  handleOptions
     *
     * @param mixed $value    A given "value" (call-by-reference)
     * @param string $type    The type as string (e.g. "i", or "str").
     * @param array $options  The given options (call-by-reference).
     */
    protected function handleOptions(mixed &$value, string $type, mixed &$default, array $options)
    {
        if (!empty($options))
        {
            switch($type)
            {
                case 'i':
                case 'int':
                case 'integer':
                    $this->handleIntRange($value, $default, $options);
                    break;

                case 's':
                case 'string':
                    $this->handleStrRange($value, $default, $options);
                    break;

                case 'strip':
                    $value = strip_tags($value, ($options['allowed'] ?? ""));
                    break;

                case 'regexpr':
                    $this->handleRegexpr($value, $default, $options);
                    break;

                case "email":
                    $this->handleEmail($value, $default, $options);
                    break;

                default:
                    // nothing - keep it as it is.
                    break;
            }
        }
    }
}
