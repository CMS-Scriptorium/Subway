<?php

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

$base = "../../config.php";
$c = 0;
while((!file_exists($base)) || (++$c < 10))
{
    $base = "../".$base;
}
$lookup = str_replace("config.php", "index.php", $base);
if (file_exists($lookup))
{
    header("Location: ".$lookup, true, 301);
}