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

namespace Subway\core\tools;

class Data
{
    /**
     *  To use "var_dump" instead of "print_r" inside the "display"-method.
     *
     *  @property bool	$use_var_dump For the use of 'var_dump'.
     */
    static public bool $use_var_dump = false;

    /**
     *  Method to change the var_dump_mode
     *
     *  @param  boolean    $bUseVarDump True, to use "var_dump" instead of "print_r" for the "display"-method, false if not. Default is "true".
     *  @see    __self__::display
     *
     */
    static public function use_var_dump(bool $bUseVarDump=true): void
    {
        self::$use_var_dump = $bUseVarDump;
    }

    /**
     *  Static method to return the result of a "print_r" call for a given object/address.
     *
     * @param mixed       $something_to_display Any (e.g. mostly an object instance, or e.g. an array)
     * @param string      $tag                  Optional a "tag" (-name). Default is "pre".
     * @param string|null $css_class            Optional a class name for the tag.
     * @param bool|null   $useVarDump           Optional overwrite internal setting. Must be a boolean.
     *
     * @return string
     *
     *  example given:  
     *  @code{.php}
     *      LEPTON_tools::display( $result_array, "code", "example_class" )  
     *  @endcode
     *      will return:  
     *  @code{.xml}
     *
     *      <code class="example_class">  
     *          array( [1] => "whatever");  
     *      </code>  
     *
     *  @endcode
     *
     */
    static function display(
        mixed       $something_to_display ="",
        string      $tag="pre",
        string|null $css_class = null,
        bool|null   $useVarDump = null
    ): string
    {
        if (is_null($something_to_display))
        {
            $something_to_display = "The value is NULL!";
        }
        
        // [0] useVarDump?
        $useVarDumpParam = is_bool($useVarDump)
            ? (bool)$useVarDump
            : false
            ;
            
        $sReturnVal = "\n<".$tag.(null === $css_class ? "" : " class='".$css_class."'").">\n";
        ob_start();
            ((true === self::$use_var_dump) || (true === $useVarDumpParam))
            ? var_dump($something_to_display)
            : print_r($something_to_display)
            ;
        $sReturnVal .= ob_get_clean();
        $sReturnVal .= "\n</".$tag.">\n";

        return $sReturnVal;
    }

    /**
     * Static method to return the result of a "print_r" call for a given object/address like above
     * but add additional called file and line to the output.
     *
     * @param mixed       $something_to_display Any (e.g. mostly an object instance, or e.g. an array)
     * @param string      $tag                  Optional a "tag" (-name). Default is "pre".
     * @param string|null $css_class            Optional a class name for the tag.
     * @param bool|null   $useVarDump           Optional overwrite internal setting. Must be a boolean.
     *
     * @return string
     *
     *  example given:..
     *  @code{.php}
     *      LEPTON_tools::display_dev( $result_array, "code", "example_class" )  
     *  @endcode
     *      will return:  
     *  @code{.xml}
     *
     *      <code class="example_class">  
     *          Location: ~modules/whatever/ajax/CallMe.php ->Line: 208  
     *          array( [1] => "whatever");  
     *      </code>  
     *
     *  @endcode
     *
     */
    static function display_dev(
        mixed       $something_to_display = "",
        string      $tag = "pre",
        string|null $css_class = null,
        bool|null   $useVarDump = null
    ): string
    {
        if (is_null($something_to_display))
        {
            $something_to_display = "The value is NULL!";
        }
        // [0] useVarDump?
        $useVarDumpParam = is_bool($useVarDump)
            ? (bool)$useVarDump
            : false
            ;
        
        // [1] get 'caller'
        $backtrace = debug_backtrace();
       //var_dump($backtrace[7]['args'][0]);
        $sOriginInfo = "<none>";
        if (isset($backtrace[0]['file']))
        {
            $sFormated = "<b>Location: %s ->Line %s</b>\n<br>";
            $sOriginInfo = sprintf(
                    $sFormated,
                    str_replace(LEPTON_PATH, "~", $backtrace[0]['file']),
                    $backtrace[0]['line']
                );
            
        }
        // [2] start return string
        $s = "\n<".$tag.(null === $css_class ? "" : " class='".$css_class."'").">\n";
        $s .= $sOriginInfo;
        ob_start();
            ((true === self::$use_var_dump) || (true === $useVarDumpParam))
            ? var_dump($something_to_display)
            : print_r($something_to_display)
            ;
        $s .= ob_get_clean();
        $s .= "\n</".$tag.">\n";

        return $s;
    }
}
