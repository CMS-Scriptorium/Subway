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

use Twig\TwigFilter;

class TwigFilters // extends Twig\Extension\AbstractExtension
{
    // Requested by the parent.
    public function __construct()
    {
        // At this time nothing to do here.    
    }

    /**
     *  See https://twig.symfony.com/doc/3.x/advanced.html
     *
     *  @usage inside twig template
     *      {{ getPasswordPattern()|display() }}
     *
     *      {{ 'hello world"|display('code') }}
     *
     *      {{ [1, 22,55]|display('pre', 'ui message red') }}
     *
     *      {{ anyValue|display(tagType, cssClass(-s)) }}
     *
     *      {{ getPasswordPattern()|display('pre', 'ui message red', true) }}
     *
     *  @return object
     */
    public static function getFilterDisplay(): object
    {
        return new TwigFilter('display', function ($env, $string, string $tagType='pre', string $cssClass = "ui message olive", bool $useVarDump=false)
        {
            /**
            $a = get_class_methods($env);
            natsort($a);
            return LEPTON_tools::display($a);
            **/
            if (true === $useVarDump)
            {
                \Subway\core\tools\data::use_var_dump(true);
            }
            return \Subway\core\tools\data::display($string, $tagType, $cssClass);
        }, ['needs_environment' => true]);
    }
    
    /**
     *  See https://twig.symfony.com/doc/3.x/advanced.html
     *
     *  @usage inside twig template
     *      {{ array.any_marker|CharsDecodeOutput }}
     *
     *  @return object
     */
    public static function CharsDecodeOutput(): object
    {
        return new TwigFilter('CharsDecodeOutput', function ($string)
        {
            $content = htmlspecialchars_decode($string);
            return $content;
        });
    }
    
    /**
     *  See https://twig.symfony.com/doc/3.x/advanced.html
     *
     *  @usage inside twig template
     *      {{ if array_needle|intersects(array_heystack) }} ... {{ endif }}
     *
     *      {{ set userHasAccess = array_needle|intersects(array_heystack) }}
     *
     *  @return object
     */
    public static function getFilterIntersects(): object
    {
        return new \Twig\TwigFilter('intersects', function (array $needle, array $heystack)
        {
            $aNewArray = array_intersect($needle, $heystack);
            return !empty($aNewArray);
        });
    }
    
    /**
     *  Force a time(-string) to display only HH:mm (e.g. "12:15")
     *  Example given
     *    a) "15:04:0001223" will be force to "15:04".
     *    b) "15" will be force to "15:00".
     *    c) NUll or empty string will be untouched!
     *    d) A value of "0" will be forces to be "00:00".
     *
     *
     *  @usage inside twig template
     *      {{ myTimeWithSeconds|timeF() }
     *
     *  @return object
     *
     *  For TWIG functions see https://twig.symfony.com/doc/3.x/advanced.html
     *
     */
    public static function getFilterTimeF(): object
    {
        // Aldus: keep in mind that the first argument of the function $string comes
        //        up from TWIG! So $string is the //value// itself to filter!
        //        As we don't know the type there is no type hint here!
        return new \Twig\TwigFilter('timeF', function ($string)
        {
            $pattern = "~[0-2]?[0-9]\:([0-2]?[0-9])~iU";
            $matches = [];
            if ((0 == preg_match_all($pattern, $string ?? '', $matches, PREG_SET_ORDER)) && ($string != '0'))
            {
                return $string;
            }

            if (empty($string) && ($string != '0'))
            {
                return $string;
            }

            if ($string == "0")
            {
                $string = "00:00:00";
            }

            $aTemp = explode(':', $string);
            return $aTemp[0].':'.($aTemp[1] ?? "00");
        });
    }
}
