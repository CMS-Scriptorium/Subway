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

require_once __DIR__.'/initialize.php';

use Subway\core\Subway;

$module_directory   = Subway::MODULE_DIRECTORY;    // 'WbceGateway';
$module_name        = Subway::MODULE_NAME;         // 'Wbce Gateway';
$module_function    = Subway::MODULE_FUNCTION;     // 'tool, initialize';
$module_version     = Subway::MODULE_VERSION;      // '0.1.0';
$module_status      = Subway::MODULE_STATUS;       // 'stable';
$module_platform    = Subway::MODULE_PLATFORM;     // '1.6.0';
$module_author      = Subway::MODULE_AUTHOR;       // 'Kant (Aldus)';
$module_license     = Subway::MODULE_LICENSE;      // 'CC BY-SA 4.0';
$module_description = Subway::MODULE_DESCRIPTION;  // 'Nothing more and nothing less than a private study for WBCE and some additional code.';
$module_guid        = Subway::MODULE_GUID;         // 'F6148B2F-9758-4A1A-9B2B-04ADEA861192';
$module_icon        = 'fa fa-diamond';             // This doesn't work: Subway::module_icon;
