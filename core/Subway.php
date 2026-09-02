<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.1
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

    protected const string CLASS_LANGUAGE_NAMESPACE = "\\Subway\\core\\language\\";

    protected const string DEFAULT_FRONTEND_CSS = "/modules/Subway/css/frontend.css";
    protected const string DEFAULT_FRONTEND_JS = "/modules/Subway/js/frontend.js";

    protected const string DEFAULT_BACKEND_CSS = "/modules/Subway/css/backend.css";
    protected const string DEFAULT_BACKEND_JS = "/modules/Subway/js/backend.js";

    protected const string TEMPLATE_DIR = "/templates/";
    protected const string HEAD_FLAG = 'HEAD BTM-';

    protected bool $cssLoaded = false;
    protected bool $jsLoaded = false;

    /**
     * Initialize the frontend - load the css- and js-files.
     * Also looking for the files inside the frontend-tremplate,
     * males use of the individuual settings of the page, or use
     * the default-template
     *
     * E.g.
     *   ~/templates/<current_frontend_template>/frontend/Subway/frontend.css
     *   ~/templates/<current_frontend_template>/frontend/Subway/frontend.js
     *
     * @return void
     */
    public function initFrontend(): void
    {
        $page = $GLOBALS['wb']->page ?? null;
        $template = $page?->template ?? DEFAULT_TEMPLATE;

        if (!$this->cssLoaded)
        {
            $lookFor = self::TEMPLATE_DIR . $template . "/frontend/Subway/css/frontend.css";
            
            $cssFile = (file_exists(WB_PATH.$lookFor))
                ? $lookFor
                : self::DEFAULT_FRONTEND_CSS;

            // Using WBCE internal
            I::insertCssFile(WB_URL . $cssFile, self::HEAD_FLAG);

            $this->cssLoaded = true;
        }

        if (!$this->jsLoaded)
        {
            $lookFor = self::TEMPLATE_DIR . $template . "/frontend/Subway/js/frontend.js";
            
            $jsFile = (file_exists(WB_PATH.$lookFor))
                ? $lookFor
                : self::DEFAULT_FRONTEND_JS;

            // Using WBCE internal
            I::insertJsFile(WB_URL . $jsFile, self::HEAD_FLAG);

            $this->jsLoaded = true;
        }

    }

    /**
     * Initialize the backend- load the css- (and js-)files.
     * Also looking inside the theme-template for the files.
     * E.g.
     *  ~/templates/<current_theme>/backend/Subway/backend.css
     *  ~/templates/<current_theme>/backend/Subway/backend.js
     *
     * @return void
     */
    public function initBackend(): void
    {
        if (!$this->cssLoaded)
        {
            $lookUpFile = self::TEMPLATE_DIR . DEFAULT_THEME . "/backend/Subway/backend.css";

            $cssFile = file_exists(WB_PATH . $lookUpFile)
                ? $lookUpFile
                : self::DEFAULT_BACKEND_CSS;

            I::insertCssFile(WB_URL . $cssFile, self::HEAD_FLAG);

            $this->cssLoaded = true;
        }

        if (!$this->jsLoaded)
        {
            $lookUpFile = self::TEMPLATE_DIR . DEFAULT_THEME . "/backend/Subway/backend.js";

            $jsFile = file_exists(WB_PATH . $lookUpFile)
                ? $lookUpFile
                : self::DEFAULT_BACKEND_JS;

            I::insertJsFile(WB_URL . $jsFile, self::HEAD_FLAG);

            $this->jsLoaded = true;
        }
    }

    protected function __construct()
    {
        $lang = defined("LANGUAGE") ? LANGUAGE : "EN";
        
        $class = self::CLASS_LANGUAGE_NAMESPACE  . $lang;
        if (!class_exists($class))
        {
            $class = self::CLASS_LANGUAGE_NAMESPACE  . 'EN';
        }
        $this->language = $class::getInstance()->getConstants();
    }
}
