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

use I;
use Subway\core\traits\Constants;
use Subway\core\traits\Singleton;
use const DEFAULT_TEMPLATE;
use const LANGUAGE;
use const WB_PATH;
use const WB_URL;

class Subway
{
    use Singleton;
    use Constants;

    public array $language = [];

    public static $instance;

    protected const string CLASSNAMESPACE = "\\Subway\\core\\language\\";

    protected const string DEFAULT_FRONTEND_CSS = "modules/Subway/css/frontend.css";

    protected bool $cssLoaded = false;


    public function initFrontend(): void
    {
        if (!$this->cssLoaded)
        {
            $page = $GLOBALS['wb']->page ?? null;
            $template = !empty($page->template) ? $page->template : DEFAULT_TEMPLATE;
            $lookFor = "/templates/".$template."/frontend/Subway/css/frontend.css";
            
            $cssFile = (file_exists(WB_PATH.$lookFor))
                ? $lookFor
                : self::DEFAULT_FRONTEND_CSS
                ;

            // Using WBCE internal
            I::insertCssFile(WB_URL . $cssFile, 'HEAD BTM-');

            $this->cssLoaded = true;
        }
    }

    protected function __construct()
    {
        $lang = defined("LANGUAGE") ? LANGUAGE : "EN";
        
        if (!class_exists(self::CLASSNAMESPACE.$lang))
        {
            $lang= "EN";
        }

        $this->language = (self::CLASSNAMESPACE.$lang)::getInstance()
            ->getConstants();
    }
}
