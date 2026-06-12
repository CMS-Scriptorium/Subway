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

namespace Subway\core\css;

use Subway\core\traits\Singleton;
use I;

class Fomantic
{
    use Singleton;

    public static $instance;

    public function initFramework()
    {
        $cssFile = "/modules/Subway/core/css/Fomantic/semantic.min.css";
        I::insertCssFile(WB_URL . $cssFile, 'HEAD BTM+');
        
        $jsFile = "/modules/Subway/core/css/Fomantic/semantic.min.js";
        I::insertJsFile(WB_URL . $jsFile, 'HEAD BTM+');
    }
}