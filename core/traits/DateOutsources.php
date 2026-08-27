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

namespace Subway\core\traits;

/**
 * Trait for date formatting operations
 * Handles conversion between different date format standards
 */
trait DateOutsources
{
    /**
     *  Public function to add a Language-Setting-Flag.
     *
     *  Only added if not found inside the lang-array.
     *
     */
    public function addLanguage (string $language = ""): bool
    {
        if ($language == "") {
            return false;
        }
        if (false === in_array($language, $this->lang)) {
            $this->lang[] = $language;
        }
        return true;
    }

    /**
     *  Public function to set up the format-string
     *
     *  @param  string  $theFormat The formatString, even empty.
     *
     */
    public function setFormat(string $theFormat = ""): bool
    {
        if ($this->useINTL)
        {
            $this->sINTLFormat = $theFormat;
        } else {
            $this->format = $theFormat;
        }
        return true;
    }

    /**
     *  Public function to get the format-string.
     *
     *  @param int|null $iTimestamp A valid Timestamp
     *                              If no timestamp is given the local time will be used.
     *
     *  @return string  The formatted date string
     *
     */
    public function toHTML(int|null $iTimestamp = 0): string
    {
        if (is_null($iTimestamp))
        {
            $iTimestamp = time();
        }

        $retValue = "";
        
        $aTempLocale = setlocale(LC_ALL, $this->lang);
        if (false === $aTempLocale)
        {
            $aTempLocale = setlocale(LC_ALL, 0);
        }

        if ((true === $this->useINTL) && (true === $this->intlInstalled))
        {
            $fmt = datefmt_create(
                $aTempLocale, // 'de-DE',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                DEFAULT_TIMEZONE_STRING, // 'Europe/Berlin',
                IntlDateFormatter::GREGORIAN,
                $this->sINTLFormat
            );
            if (is_null($fmt))
            {
                // Huston: we've got a problem!
                $this->useINTL = false;
                $retValue = date($this->format, $iTimestamp);
            }
            else
            {
                // @see     https://php.watch/versions/8.1/strftime-gmstrftime-deprecated
                $retValue = datefmt_format($fmt,  $iTimestamp );
            }
        } else {
            /**
             * Aldus:   2022-01-25
             * @notice  In PHP 8.1.1 the strftime function produce a deprecated warning and will be removed in PHP 9.0
             */
            if (class_exists("IntlDateFormatter"))
            {
                $retValue = new IntlDateFormatter(
                    $aTempLocale, // 'en_US',
                    IntlDateFormatter::LONG,
                    IntlDateFormatter::NONE,
                    DEFAULT_TIMEZONE_STRING, // 1 timezone
                    IntlDateFormatter::GREGORIAN, // 2 calendar
                    $this->sINTLFormat              // 3 format
                )->format($iTimestamp);
            }
            else
            {
                // Aldus 2025-01-19: Aldus fix for the "%"-char in format!
                // This is a more thoretical problem at all.
                trigger_error("ERROR: Houson we've a problem here. [100347]");
                $retValue = date(str_replace("%", "",$this->format), $iTimestamp);
            }
        }
        
        return $retValue;
    }

    /**
     *  Public function to set up the language at once
     *
     *  @param  array   $aArray A simple Array with the strings
     *
     *  @return bool    Always true.
     */
    public function setLanguage(array $aArray = []): bool
    {
        $this->lang = [];
        foreach ($aArray as $a)
        {
            $this->lang[] = $a;
        }
        return true;
    }

        /**
     * Private function to force a given string into an internal
     * dot-based format: "MM.DD.YY" (month, day, year).
     *
     *  @param    string    $aDateString the DateString
     *
     *  @return    void    Param is called by reference!
     *
     * @code
     *  $date = "11-03-1988";
     *  $this->forceDate($date);
     *  echo $date;
     *
     *  results in: "11.03.1966"
     */
    private function forceDate(string &$aDateString ): void
    {
        $pattern = ["~[\\/|.|-]+~"];
        $replace = ["."];

        $aDateString = preg_replace($pattern, $replace, $aDateString);
    }

    /**
     *  Private function to force the format-string used in/for "transform"
     *
     *  @param  string  $aFormat The transform-format-string - called by reference!
     *
     *  @see    transform
     *
     */
    private function forceFormat(string &$aFormat): void
    {

        $aFormat = strtolower ($aFormat);

        $pattern = ["~[\\/|.|-]+~", "~[ |%]+~"];
        $replace = ["", ""];

        $aFormat = preg_replace($pattern, $replace, $aFormat);
    }

    /**
     *  private function that force a "short" Year to a "long" year
     *
     *  @param  string      $givenYearStr    The year - called by reference!
     *  @see    forceYear
     *
     *    If the year is future oriented more than two years by default at runtime,
     *    19xx is assumed.
     */
    private function doForceYear(string|int &$givenYearStr = "1971"): void
    {
        $aYearStr = (string) $givenYearStr;
        
        if (strlen($aYearStr) == 2)
        {
            $aYearStr = (((int) $aYearStr > $this->forceYear + (int) date("y", time())) ? "19" : "20").$aYearStr;
        }
        if (strlen($aYearStr) > 4)
        {
            $aYearStr = substr($aYearStr, 0, 4);
        }
        
        $givenYearStr = intval($aYearStr);
    }

    /**
     *  Public function to transform the date inside a given string
     *
     *  @param  string    $aStr     The string within the dates. Pass by reference.
     *  @param  string    $aPattern Own patter/regexp for other formats.
     *                              default is "dd.mm.yyyy" e.g. 11.03.1966
     */
    public function parseString (string &$aStr = "", string $aPattern = "/([0-3][0-9].[01]{0,1}[0-9].[0-9]{2,4})/s"): void
    {
        $found=[];
        preg_match_all($aPattern, $aStr, $found );
        foreach ($found[1] as $a)
        {
            $aStr = str_replace($a, $this->transform($a), $aStr);
        }
    }

    /**
     *  Setting up the language via a single key,
     *  e.g. inside LEPTON-CMS
     *
     *  @param  string  $aKeyStr The language-key-str, e.g. "EN"...
     *  @return bool    True if the key is known, false if failed.
     *
     */
    public function setCoreLanguage(string $languageKey = ""): bool
    {

        $return_value = true;

        switch ($languageKey)
        {
            case "DE":
                $this->lang = ['de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge'];
                break;

            case "EN":
                $this->lang = ['en_EN@euro', 'en_EN', 'en', 'EN', 'en_UK', 'UK', 'en_US', 'en_GB', 'en_CA'];
                break;

            case "FR":
                $this->lang = ['FR', 'fr_FR.UTF-8', 'fr_FR', 'fr_FR@euro', 'fr'];
                break;

            case "IT":
                $this->lang = ['it_IT@euro', 'it_IT', 'it'];
                break;

            case "NL":
                $this->lang = ['nl_NL@euro', 'nl_NL', 'nl', 'Dutch', 'nld_nld'];
                break;

            case "RU":
                $this->lang = ['RU', 'ru_RU.UTF-8', 'ru_RU', 'ru_RU@euro', 'ru'];
                break;

            case "ZH":
                $this->lang = ['zh_CN','zh_CN.eucCN','zh_CN.GB18030','zh_CN.GB2312','zh_CN.GBK','zh_CN.UTF-8','zh_HK','zh_HK.Big5HKSCS','zh_HK.UTF-8','zh_TW','zh_TW.Big5','zh_TW.UTF-8'];
                break;

            default:
                $this->testLocale($languageKey);
                break;
        }
        return $return_value;
    }

    /**
     *  Public function to test a given LanguageKey
     *  against the server-implanted ones using "locale -a".
     *  If one or more are found the internal "lang" will be set.
     *
     *  @param  string    $aKey the LanguageKey, e.g. "EN", "fr_FR"
     *                    If only two chars are given, the rest will be
     *                    automatically formatted as "uu_LL".
     *
     *  @param  bool    $use_it If the key is found - use it inside the class.
     *
     *  @return array    all matches; could be empty.
     *
     */
    public function testLocale(string $aKey = "de_DE", bool $use_it = true): array
    {
        if (strlen($aKey) == 2)
        {
            $aKey = strtolower($aKey)."_".strtoupper($aKey);
        }

        $temp_array = [];
        ob_start();
            exec('locale -a', $temp_array);
        ob_end_flush();
        $all = [];

        foreach($temp_array as $lang_key)
        {
            if (substr($lang_key, 0,5) == $aKey)
            {
                $all[]=$lang_key;
            }
        }

        if (!empty($all) && (true === $use_it))
        {
            $this->lang = $all;
        }
        return $all;
    }

}
