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

if (!defined('WB_PATH'))
{
    header('Location: ../../index.php');
    die();
}

/** 
 * [1] Just to make sure the WBCE autoloader will find the module classes
 *     (As this one line is missing in the "inizialize" file of the root.)
 */
WbAuto::AddDir(WB_PATH."/modules/");

/**
 * [2] Backwards for the "L_" processTranslation by Stefek.

if (!defined('TWIG_SHOW_MISSING_LANG_STRINGS'))
{
    define('TWIG_SHOW_MISSING_LANG_STRINGS', false);
}
*/
