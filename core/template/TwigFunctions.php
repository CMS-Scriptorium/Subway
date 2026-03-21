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

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFunctions extends AbstractExtension
{
    // Requested by the parent.
    public function __construct() {
        // At this time nothing to do here.
    }

    /**
     *  check if file exists.
     *  @param string $lookUpPath  A valid path including LEPTON_PATH
     *  @usage (use tilde as a term-connector): {% if !fileExists( (AnyPath ~ AnyFilePart1 ~ '_' ~ AnyFilePart2 ~ '.pdf') ) %}
     *  @return object.
     */
    public static function fileExists(string $lookUpPath = ''): object {
        return new TwigFunction('fileExists', function (string $lookUpPath = '') {
            return file_exists($lookUpPath);
        });
    }

    /**
     *  processTranslation / L_
     *  ---------------------------------------------------------------------------
     *  This function allows you to use any language string that is active on the 
     *  page you're templating. No need to hand over long lists of lang strings
     *  to the templates anymore as it was with the previously used Template Engine
     *
     * Correct format would be:
     *     L_('ARRAY:KEY'); or
     *     L_('{ARRAY:KEY}'); 
     * example:
     *     L_('TEXT:ACTIVE');
     *     L_('{TEXT:ACTIVE}');
     *
     * @author Christian M. Stefan <stefek@designthings.de>
     * @param  string
     * @param  bool
     * @return string Translated String
     */
    public static function processTranslationL(string $sStr = ''): object
    {
        return new TwigFunction("L_", function ($sStr) {
            $sRetVal = '';
            if (strpos($sStr, ':') !== false)
            {
                $tmp = self::splitString($sStr);
                $arr = $tmp[0];
                $key = $tmp[1];

                $sRetVal = (is_array($GLOBALS[$arr]) && array_key_exists($key, $GLOBALS[$arr]))
                    ? $GLOBALS[$arr][$key]
                    : self::formatString($arr, $key)
                    ;
                
            } else
            {
                $sRetVal = $sStr;
            }
            return $sRetVal;
        });
    }

    protected static function splitString(string &$sStr): array
    {
        $sStr = str_replace(' ', '', $sStr);
        if (strpos($sStr, '{') !== false)
        {
            $sOut = [];
            preg_match_all('/{(.*?)}/', $sStr, $sOut);
            $tmp = explode(':', $sOut[1][0]);
        } else
        {
            $tmp = explode(':', $sStr);
        }
        return $tmp;
    }

    protected static function formatString(string $arr, string $key): string
    {
        $bShowMissing = (defined('TWIG_SHOW_MISSING_LANG_STRINGS') && TWIG_SHOW_MISSING_LANG_STRINGS == true) ? true : false;
        if ($bShowMissing)
        {
            $sRetVal = "<span style='color:purple'>";
            $sRetVal .= (!is_array($GLOBALS[$arr])) ? 'Array ' . $arr . ' does not exist.<br>' : '';
            $sRetVal .= "<b>Missing Translation:</b> <input style=\"width:450px\" type=\"text\" value=\"$" . $arr . "['" . $key . "']\"></span>";
        } else
        {
            $key = str_replace('_', ' ', $key) . '.';
            $sRetVal = $key;
        }
        return $sRetVal;
    }
}
