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

namespace Subway\core\language;

use Subway\core\traits\Constants;
use Subway\core\traits\Singleton;

class EN
{
    use Singleton;
    use Constants;

    public static $instance;

    public const string HELLO_WORLD = "Hello world! That's me: <em>'".__CLASS__."'</em>!";
    public const string MODULE_DESCRIPTION = 'Nothing more and nothing less than a private study for WBCE and some additional code.';
    public const string NO_INTERFACE = "No interface implantation at this time!";
}
