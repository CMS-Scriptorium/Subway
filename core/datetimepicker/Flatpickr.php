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

namespace Subway\core\datetimepicker;

use I;
use Subway\core\template\TwigBox;
use Subway\core\traits\Singleton;
use const LANGUAGE;
use const WB_PATH;
use const WB_URL;

/**
 * @see     https://flatpickr.js.org/getting-started/
 * @see     https://flatpickr.js.org/formatting/ 
*
 */
class Flatpickr
{
    use Singleton;

    protected const BASE_PATH = "/modules/Subway/core/datetimepicker/flatpickr/dist";
    protected const STD_HEAD = "HEAD BTM+";

    public static $instance;

    protected function __construct()
    {
        // [1] css
        I::insertCssFile(WB_URL . self::BASE_PATH . "/flatpickr.css", self::STD_HEAD);
        I::insertCssFile(WB_URL . self::BASE_PATH . "/ie.css", self::STD_HEAD);
        I::insertCssFile(WB_URL . self::BASE_PATH . "/plugins/confirmDate/confirmDate.css", self::STD_HEAD);
        I::insertCssFile(WB_URL . self::BASE_PATH . "/plugins/monthSelect/style.css", self::STD_HEAD);
        
        // [2] js
        I::insertJsFile(WB_URL . self::BASE_PATH . "/flatpickr.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/rangePlugin.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/confirmDate/confirmDate.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/minMaxTimePlugin.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/monthSelect/index.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/scrollPlugin.js", self::STD_HEAD);
        I::insertJsFile(WB_URL . self::BASE_PATH . "/plugins/weekSelect/weekSelect.js", self::STD_HEAD);
        
        $lookUp = self::BASE_PATH . "/l10n/".strtolower(LANGUAGE).".js";
        if (file_exists(WB_PATH . $lookUp))
        {
            I::insertJsFile(WB_URL . $lookUp, self::STD_HEAD);
        }
    }

    public function testPicker(string $title="", string $format="Y.m-d"): string
    {
        $oTwig = TwigBox::getInstance();
        $oTwig->registerPath(dirname(__DIR__, 2)."/templates/", "Subway");

        return $oTwig->render(
            "@Subway/testFlatPickr.lte",
            [
                'title_datepicker' => $title,
                'format' =>$format,
                'random_id' => \random_int(1000, 999999),
                'lang'  => strtolower(LANGUAGE)
            ]
        );
    }
}
