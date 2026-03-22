<?php

declare(strict_types=1);

use Subway\core\Subway;
use Subway\core\template\TwigBox;

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

$oTwig = TwigBox::getInstance();
$oTwig->registerModule("Subway");

echo $oTwig->render(
    "@Subway/tool.lte",
    [
        'message' => Subway::getInstance()->language['NO_INTERFACE']
    ]
);
