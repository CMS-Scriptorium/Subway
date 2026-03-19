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

$module_directory   = Subway::module_directory;    // 'WbceGateway';
$module_name        = Subway::module_name;         // 'Wbce Gateway';
$module_function    = Subway::module_function;     // 'tool, initialize';
$module_version     = Subway::module_version;      // '0.1.0';
$module_status      = Subway::module_status;       // 'stable';
$module_platform    = Subway::module_platform;     // '1.6.0';
$module_author      = Subway::module_author;       // 'Kant (Aldus)';
$module_license     = Subway::module_license;      // 'CC BY-SA 4.0';
$module_description = Subway::module_description;  // 'Nothing more and nothing less than a private study for WBCE and some additional code.';
$module_guid        = Subway::module_guid;         // 'F6148B2F-9758-4A1A-9B2B-04ADEA861192';
$module_icon        = 'fa fa-diamond';             // This doesn't work: Subway::module_icon;