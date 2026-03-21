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

class System
{
    use Singleton;
    use Constants;

    public static $instance;

    protected bool $isWBCE = false;
    protected bool $isLEPTON = false;
    protected bool $isWB = false;

    public function getInfo(): array
    {
        $aInfo =  [];

        if ($this->isWBCE) {
            $aInfo['cms'] = "WBCE CMS";
            $aInfo['version'] = WBCE_VERSION;
        } elseif ($this->isLEPTON) {
            $aInfo['cms'] = "LEPTON-CMS";
            $aInfo['version'] = VERSION;
        } elseif ($this->isWB) {
            $aInfo['cms'] = "WebsiteBaker";
            $aInfo['version'] = WB_VERSION;
        }

        return $aInfo;
    }

    protected function __construct()
    {
        // Das kann so nicht bleiben! To do!!
        $this->isWBCE = (defined("WBCE_VERSION"));
        $this->isLEPTON = (defined("LEPTON_PATH"));
    }
}
