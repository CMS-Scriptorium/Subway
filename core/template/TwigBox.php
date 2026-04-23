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

namespace Subway\core\template;

use Subway\core\sql\Database;
use Subway\core\traits\Singleton;
use Subway\core\template\TwigBox\TwigFilters;
use Subway\core\template\TwigBox\TwigFunctions;
use Subway\core\template\TwigBox\TwigOperators;
use Subway\core\template\TwigBox\TwigOperatorsOld;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use const ADMIN_URL;
use const DEFAULT_TEMPLATE;
use const DEFAULT_THEME;
use const MEDIA_DIRECTORY;
use const PAGE_ID;
use const TABLE_PREFIX;
use const THEME_PATH;
use const THEME_URL;
use const WB_PATH;
use const WB_URL;

class TwigBox
{
    use Singleton;

    protected const TWIG_BASE_PATH = "/include/Sensio/";
    protected const TEMPLATE_DIR = "/templates/";

    /**
     *  Public var that holds the instance of the TWIG-loader.
     */
    public ?object $loader = null;

    /**
     *  Public var that holds the instance of the TWIG-parser.
     */
    public ?object $parser = null;

    public static $instance;

    protected function __construct()
    {
        spl_autoload_register([__CLASS__, 'twigAutoload'], true, true);

        $this->loader = new FilesystemLoader(WB_PATH.'/');

        $this->registerPath(
                WB_PATH.self::TEMPLATE_DIR.DEFAULT_THEME.self::TEMPLATE_DIR, "theme"
        );
        $this->registerPath(
                WB_PATH.self::TEMPLATE_DIR.DEFAULT_TEMPLATE.self::TEMPLATE_DIR, "frontend"
        );

        $this->parser = new Environment(
            $this->loader,
            [
                'cache' => false, // WB_PATH."/temp/modules/Subway/",
                'debug' => true
            ]
        );

        // [1] Global constants
        $this->parser->addGlobal("WB_PATH", WB_PATH);
        $this->parser->addGlobal("WB_URL", WB_URL);
        $this->parser->addGlobal("ADMIN_URL", ADMIN_URL);
        $this->parser->addGlobal("THEME_PATH", THEME_PATH);
        $this->parser->addGlobal("THEME_URL", THEME_URL);
        $this->parser->addGlobal("MEDIA_DIRECTORY", MEDIA_DIRECTORY);

        // [2] Extensions
        $this->parser->addExtension(new DebugExtension());
        if (version_compare(Environment::VERSION, "3.21.0") >= 0)
        {
            $this->parser->addExtension(new TwigOperators());
        } else {
            $this->parser->addExtension(new TwigOperatorsOld());
        }

        // [3] Functions
        $this->parser->addFunction(TwigFunctions::fileExists());
        $this->parser->addFunction(TwigFunctions::processTranslationL());

        // [4] Filters
        $this->parser->addFilter(TwigFilters::getFilterDisplay());
        $this->parser->addFilter(TwigFilters::getFilterIntersects());
    }

    /**
     * Public function to register a path to the current instance.
     * If the path doesn't exist he will not be added to avoid Twig-internal warnings.
     *
     * @param string $sPath      A path to any local template directory.
     * @param string $sNamespace An optional namespace (-identifier),
     *                           by default "__main__", normal e.g.
     *                           the namespace of a module. See the
     *                           Twig documentation for details about
     *                           using "template" namespace.
     *
     * @return bool True if success, false if file doesn't exist
     *              or the first param is empty.
     */
    public function registerPath(
        string $sPath = "",
        string $sNamespace = "__main__"
    ): bool
    {
        if ($sPath === "")
        {
            return false;
        }

        if (true === file_exists($sPath))
        {
            $current_paths = $this->loader->getPaths($sNamespace);
            if (!in_array($sPath, $current_paths)) {
                $this->loader->prependPath($sPath, $sNamespace);
                return true;
            }
        }

        return false;
    }

    /**
     *  Public function to "register" all module specific paths at once
     *
     * @param string $sModuleDir A valid module-directory (also used as namespace).
     *
     * @return void
     *
     */
    public function registerModule(string $sModuleDir): void
    {
        $basePath = WB_PATH."/modules/".$sModuleDir;
        $this->registerPath($basePath.self::TEMPLATE_DIR, $sModuleDir);
        $this->registerPath($basePath.self::TEMPLATE_DIR."backend", $sModuleDir);
        $this->registerPath($basePath.self::TEMPLATE_DIR."frontend", $sModuleDir);

        $this->registerPath(WB_PATH.self::TEMPLATE_DIR.DEFAULT_THEME."/backend/".$sModuleDir."/", $sModuleDir);

        // for the frontend
        if (defined("PAGE_ID"))
        {
            $oDB = Database::getInstance();
            $page_template = $oDB->get_one("SELECT `template` FROM `".TABLE_PREFIX."pages` WHERE `page_id`=".PAGE_ID);
            $this->registerPath(WB_PATH.self::TEMPLATE_DIR.( $page_template === "" ? DEFAULT_TEMPLATE : $page_template)."/frontend/".$sModuleDir."/", $sModuleDir);
        }
    }

    /**
     *  Public shortcut to the internal loader->render method.
     *
     * @param string $sTemplateName A valid template-name (to use) incl. the namespace.
     * @param array  $aMixed        The values to parse.
     *
     * @return string  The parsed template string.
     */
    public function render(string $sTemplateName, array $aMixed): string
    {
        $sTemplateNameClean = str_replace("/frontend/", "/", $sTemplateName);
        return static::$instance->parser->render($sTemplateNameClean, $aMixed);
    }

    /**
     * Handles autoload of classes.
     *
     * @param string $class A class name.
     *
     * @return bool
     */
    public static function twigAutoload(string $class): bool
    {
        if (!str_starts_with($class, 'Twig\\')) // Hack for WBCE
        {
            return false;
        }

        // Are there any namespaces?
        $aTempTerms = explode("\\", $class);
        if (count($aTempTerms) > 1)
        {
            // array_shift( $aTempTerms ); // remove the \Twig\
            $sPath = implode(DIRECTORY_SEPARATOR, $aTempTerms);
            $file = WB_PATH.self::TWIG_BASE_PATH.$sPath.".php";
            
            if (is_file($file))
            {
                include_once $file;
            }
        }
        return false;
    }
}
