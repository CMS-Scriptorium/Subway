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

use Subway\core\traits\Singleton;
use I;

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
    }

    public function testPicker(): string
    {
        $oTwig = \Subway\core\template\TwigBox::getInstance();
        $oTwig->registerPath(dirname(__DIR__, 2)."/templates/", "Subway");

        return $oTwig->render(
            "@Subway/testFlatPickr.lte",
            [
                'title_datepicker' => "Test datetimepicker FlatPickR input"
            ]
        );
    }
}
