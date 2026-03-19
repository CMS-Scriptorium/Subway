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

use Subway\core\traits\Constants;
use Subway\core\traits\Singleton;
use const LANGUAGE;

class Subway extends Info
{
    use Singleton;
    use Constants;

    public array $language = [];

    public static $instance;

    protected const string CLASSNAMESPACE = "\\Subway\\core\\language\\";

    protected function __construct()
    {
        $lang = defined("LANGUAGE") ? LANGUAGE : "EN";
        
        if (!class_exists(self::CLASSNAMESPACE.$lang))
        {
            $lang= "EN";
        }

        $this->language = (self::CLASSNAMESPACE.$lang)::getInstance()
            ->getConstants();
    }
}
