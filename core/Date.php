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

class Date
{
    public const SYSTEM_DEFAULT_STR = 'System Default';

    /**
     *  Default format (dd.mm.YYYY)
     *
     */
    public const DEFAULT_FORMAT = '%d.%m.%Y';
    
    /**
     *  The reference to *Singleton* instance of this class
     *
     *  @var    object
     *  @access private
     *
     */
    public static $instance;

    /**
     *  The (default) format-string.
     *  Default is dd.mm.yyyy
     *
     *  @var    string
     *  @access public
     *
     */
    public string $format = self::DEFAULT_FORMAT;
    
    public string $sINTLFormat = "d.M.yyyy";

    /**
     *  The language-flags as an array.
     *  Default are some settings for German.
     *
     *  @var    array
     *  @access public
     *
     */
    public array $lang = ['de_DE@euro', 'de_DE.UTF-8', 'de_DE', 'de', 'ge'];

    /**
     *  The mode we are using. Default is LC_ALL for all.
     *
     *  @var    string
     *  @access public
     *
     */
    public string $mode = "LC_ALL";

    /**
     *  Used for "forceYear" to determiante if the year belongs to 1900 or 2000.
     *  Default setting is 2, so if the current year is 2008 a value of 10 will be force
     *  to 2010 instead of 11 will be force to 1911.
     *
     *  @var    integer
     *  @see    forceYear
     *  @access public
     *
     */
    public int $forceYear = 2;

    /**
     *  Translation-array for the LEPTON-CMS internal date-formats.
     *
     *  @var    array
     *  @access public
     *
     */
    public array $coreDateFormatsPHP = [
        'l, jS F, Y'=> '%A, %e %B, %Y',
        'jS F, Y'   => '%e %B, %Y',
        'd M Y'     => '%d %a %Y',
        'M d Y'     => '%a %d %Y',
        'D M d, Y'  => '%a %b %d, %Y',  ##
        'd-m-Y'     => '%d-%m-%Y',      #1
        'm-d-Y'     => '%m-%d-%Y',
        'd.m.Y'     => self::DEFAULT_FORMAT,      #2
        'm.d.Y'     => '%m.%d.%Y',
        'd/m/Y'     => '%d/%m/%Y',      #3
        'm/d/Y'     => '%m/%d/%Y',
        'j.n.Y'     => '%e.%n.%Y'       #4! Day in month without leading zero
    ];

    /**
     *  Translation-array for the LEPTON-CMS internal date-formats.
     *
     *  @var    array
     *  @access public
     *  @see    https://dev.mysql.com/doc/refman/5.7/en/date-and-time-functions.html#function_date-format
     */
    public array $coreDateFormatsMYSQL = [
        'l, jS F, Y'=> '%W, %D %M, %Y', // 1
        'jS F, Y'   => '%D %M, %Y',     // 2
        'd M Y'     => '%e. %M %Y',     // 3 e.g. 24. Juli 2022
        'M d Y'     => '%b %e %Y',      // 4
        'D M d, Y'  => '%a %b %e, %Y',  // 5 *
        'd-m-Y'     => '%d-%m-%Y',      // 6
        'm-d-Y'     => '%m-%d-%Y',      // 7
        'd.m.Y'     => self::DEFAULT_FORMAT,      // 8
        'm.d.Y'     => '%m.%d.%Y',      // 9
        'd/m/Y'     => '%d/%m/%Y',      // 10
        'm/d/Y'     => '%m/%d/%Y',      // 11
        'j.n.Y'     => '%e.%c.%Y',       // 12 Day in month without leading zero
        'Y-m-d'     => '%Y-%m-%d',      // L* 7.4
        'Y.m.d'     => '%Y.%m.%d'       // L* 7.4
    ];

    /**
     *  Translation-array for the LEPTON-CMS internal time-formats.
     *
     *  @var    array
     *  @access public
     *
     */
    public array $coreTimeFormatsPHP = [
        'g:i A' => '%I:%M %p',
        'g:i a' => '%I:%M %P',
        'H:i:s' => '%H:%M:%S',
        'H:i'   => '%H:%M'
    ];

    /**
     *  Translation-array for the LEPTON-CMS internal time-formats.
     *
     *  @var    array
     *  @access public
     *  @see    https://dev.mysql.com/doc/refman/5.7/en/date-and-time-functions.html#function_date-format
     */
    public array $coreTimeFormatsMYSQL = [
        'g:i A' => '%l:%i %p',  // Uppercase Ante meridiem and Post meridiem
        'g:i a' => '%r %p',
        'H:i:s' => '%H:%i:%s',  // 3
        'H:i'   => '%H:%i'      // 4
    ];

    /**
     *  Translation-array for the LEPTON-CMS internal date-formats for datepicker(-s).
     *
     * @var    array
     * @access public
     * @see https://api.jqueryui.com/datepicker/#utility-formatDate
     */
    public array $coreDateFormatsDatePicker = [
        'l, jS F, Y'=> 'DD, d. MM yy',  //'A, e B, yy',
        'jS F, Y'   => 'd. MM, yy',      // 1
        'd M Y'     => 'd. MM yy',       // 2
        'M d Y'     => 'M d yy',
        'D M d, Y'  => 'D M d, yy',     // *!
        'd-m-Y'     => 'd-m-yy',        // 3
        'm-d-Y'     => 'm-d-yy',
        'd.m.Y'     => 'd.m.yy',        // 4
        'm.d.Y'     => 'm.d.yy',
        'Y-m-d'     => 'yy-mm-dd',      // lepton 7.4
        'Y.m.d'     => 'yy.mm.dd',      // lepton 7.4
        'd/m/Y'     => 'd/m/yy',        // 5
        'm/d/Y'     => 'm/d/yy',
        'j.n.Y'     => 'd.m.yy'         // 6! Day in month without leading zero
    ];

    /**
     *  Boolean to switch to the IntlDateFormatter
     *
     *  @see: https://www.php.net/manual/de/intldateformatter.format.php
     *  @see: https://unicode-org.github.io/icu/userguide/format_parse/datetime/#formatting-dates
     *
     *  @access public
     *  @var    bool
     *
     */
    public bool $useINTL = false;
    public bool $intlInstalled = false;

    /**
     *  Holds information for some dateFormatter patterns (M.f.i!)
     *  @access public
     *  @var    array
     *
     *  @see    https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
     */
    public array $coreDateFormatsINTL = [
        'l, jS F, Y'=> 'A, e B, yyyy',
        'jS F, Y'   => 'e B, yyyy',     // 1
        'd M Y'     => 'd M yyyy',
        'M d Y'     => 'M d yyyy',
        'D M d, Y'  => 'EE d MM, yyyy', // 'a m d, yy',
        'd-m-Y'     => 'd-M-yy',        // 3
        'm-d-Y'     => 'M-d-yy',
        'd.m.Y'     => 'd.M.yy',        // 4
        'm.d.Y'     => 'M.d.yy',
        'd/m/Y'     => 'd/M/yy',        // 5
        'm/d/Y'     => 'M/d/yy',
        'j.n.Y'     => 'd.M.yyyy'       // 6! Day in month without leading zero
    ];

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/locale-support.html
     * @see https://dev.mysql.com/doc/refman/8.4/en/locale-support.html
     */
    public const MYSQL_LOCALES = [
        "ar_AE"  =>  "Arabic - United Arab Emirates",
        "ar_BH"  =>  "Arabic - Bahrain",
        "ar_DZ"  =>  "Arabic - Algeria",
        "ar_EG"  =>  "Arabic - Egypt",
        "ar_IN"  =>  "Arabic - India",
        "ar_IQ"  =>  "Arabic - Iraq",
        "ar_JO"  =>  "Arabic - Jordan",
        "ar_KW"  =>  "Arabic - Kuwait",
        "ar_LB"  =>  "Arabic - Lebanon",
        "ar_LY"  =>  "Arabic - Libya",
        "ar_MA"  =>  "Arabic - Morocco",
        "ar_OM"  =>  "Arabic - Oman",
        "ar_QA"  =>  "Arabic - Qatar",
        "ar_SA"  =>  "Arabic - Saudi Arabia",
        "ar_SD"  =>  "Arabic - Sudan",
        "ar_SY"  =>  "Arabic - Syria",
        "ar_TN"  =>  "Arabic - Tunisia",
        "ar_YE"  =>  "Arabic - Yemen",
        "be_BY"  =>  "Belarusian - Belarus",
        "bg_BG"  =>  "Bulgarian - Bulgaria",
        "ca_ES"  =>  "Catalan - Spain",
        "cs_CZ"  =>  "Czech - Czech Republic",
        "da_DK"  =>  "Danish - Denmark",
        "de_AT"  =>  "German - Austria",
        "de_BE"  =>  "German - Belgium",
        "de_CH"  =>  "German - Switzerland",
        "de_DE"  =>  "German - Germany",
        "de_LU"  =>  "German - Luxembourg",
        "el_GR"  =>  "Greek - Greece",
        "en_AU"  =>  "English - Australia",
        "en_CA"  =>  "English - Canada",
        "en_GB"  =>  "English - United Kingdom",
        "en_IN"  =>  "English - India",
        "en_NZ"  =>  "English - New Zealand",
        "en_PH"  =>  "English - Philippines",
        "en_US"  =>  "English - United States",
        "en_ZA"  =>  "English - South Africa",
        "en_ZW"  =>  "English - Zimbabwe",
        "es_AR"  =>  "Spanish - Argentina",
        "es_BO"  =>  "Spanish - Bolivia",
        "es_CL"  =>  "Spanish - Chile",
        "es_CO"  =>  "Spanish - Colombia",
        "es_CR"  =>  "Spanish - Costa Rica",
        "es_DO"  =>  "Spanish - Dominican Republic",
        "es_EC"  =>  "Spanish - Ecuador",
        "es_ES"  =>  "Spanish - Spain",
        "es_GT"  =>  "Spanish - Guatemala",
        "es_HN"  =>  "Spanish - Honduras",
        "es_MX"  =>  "Spanish - Mexico",
        "es_NI"  =>  "Spanish - Nicaragua",
        "es_PA"  =>  "Spanish - Panama",
        "es_PE"  =>  "Spanish - Peru",
        "es_PR"  =>  "Spanish - Puerto Rico",
        "es_PY"  =>  "Spanish - Paraguay",
        "es_SV"  =>  "Spanish - El Salvador",
        "es_US"  =>  "Spanish - United States",
        "es_UY"  =>  "Spanish - Uruguay",
        "es_VE"  =>  "Spanish - Venezuela",
        "et_EE"  =>  "Estonian - Estonia",
        "eu_ES"  =>  "Basque - Spain",
        "fi_FI"  =>  "Finnish - Finland",
        "fo_FO"  =>  "Faroese - Faroe Islands",
        "fr_BE"  =>  "French - Belgium",
        "fr_CA"  =>  "French - Canada",
        "fr_CH"  =>  "French - Switzerland",
        "fr_FR"  =>  "French - France",
        "fr_LU"  =>  "French - Luxembourg",
        "gl_ES"  =>  "Galician - Spain",
        "gu_IN"  =>  "Gujarati - India",
        "he_IL"  =>  "Hebrew - Israel",
        "hi_IN"  =>  "Hindi - India",
        "hr_HR"  =>  "Croatian - Croatia",
        "hu_HU"  =>  "Hungarian - Hungary",
        "id_ID"  =>  "Indonesian - Indonesia",
        "is_IS"  =>  "Icelandic - Iceland",
        "it_CH"  =>  "Italian - Switzerland",
        "it_IT"  =>  "Italian - Italy",
        "ja_JP"  =>  "Japanese - Japan",
        "ko_KR"  =>  "Korean - Republic of Korea",
        "lt_LT"  =>  "Lithuanian - Lithuania",
        "lv_LV"  =>  "Latvian - Latvia",
        "mk_MK"  =>  "Macedonian - North Macedonia",
        "mn_MN"  =>  "Mongolia - Mongolian",
        "ms_MY"  =>  "Malay - Malaysia",
        "nb_NO"  =>  "Norwegian(Bokmål) - Norway",
        "nl_BE"  =>  "Dutch - Belgium",
        "nl_NL"  =>  "Dutch - The Netherlands",
        "no_NO"  =>  "Norwegian - Norway",
        "pl_PL"  =>  "Polish - Poland",
        "pt_BR"  =>  "Portugese - Brazil",
        "pt_PT"  =>  "Portugese - Portugal",
        "rm_CH"  =>  "Romansh - Switzerland",
        "ro_RO"  =>  "Romanian - Romania",
        "ru_RU"  =>  "Russian - Russia",
        "ru_UA"  =>  "Russian - Ukraine",
        "sk_SK"  =>  "Slovak - Slovakia",
        "sl_SI"  =>  "Slovenian - Slovenia",
        "sq_AL"  =>  "Albanian - Albania",
        "sr_RS"  =>  "Serbian - Serbia",
        "sv_FI"  =>  "Swedish - Finland",
        "sv_SE"  =>  "Swedish - Sweden",
        "ta_IN"  =>  "Tamil - India",
        "te_IN"  =>  "Telugu - India",
        "th_TH"  =>  "Thai - Thailand",
        "tr_TR"  =>  "Turkish - Turkey",
        "uk_UA"  =>  "Ukrainian - Ukraine",
        "ur_PK"  =>  "Urdu - Pakistan",
        "vi_VN"  =>  "Vietnamese - Vietnam",
        "zh_CN"  =>  "Chinese - China",
        "zh_HK"  =>  "Chinese - Hong Kong",
        "zh_TW"  =>  "Chinese - Taiwan"
    ];

    /**
     *  Return the "internal" instance of this class
     *
     *  @param  boolean $bUseINTL    Optional flag for the use of INTL DateTimeObject. Default is false.
     *  @return object
     *
     */
    public static function getInstance(bool $bUseINTL = false): object
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        static::$instance->useINTL = $bUseINTL;

        if (true === $bUseINTL)
        {
            static::$instance->intl_installed = static::$instance->checkINTLExtensionExists();
        }

        return static::$instance;
    }

    /**
     *  The constructor
     */
    protected function __construct ()
    {
        // nothing
    }

    /**
     *  Public function to add a Language-Setting-Flag.
     *
     *  Only added if not found inside the lang-array.
     *
     */
    public function addLanguage ($aString=""): bool
    {
        if ($aString == "") {
            return false;
        }
        if (false === in_array($aString, $this->lang)) {
            $this->lang[] = $aString;
        }
        return true;
    }

    /**
     *  Public function to set up the format-string
     *
     *  @param  string  $aString The formatString, even empty.
     *
     */
    public function setFormat( string $aString="" ): bool
    {
        if($this->useINTL)
        {
            $this->sINTLFormat = $aString;
        } else {
            $this->format=$aString;
        }
        return true;
    }

    /**
     *  Public function to get the format-string.
     *
     *  @param int|null $aTimestamp A valid Timestamp
     *                              If no timestamp is given the local time will be used.
     *
     *  @return string  The formatted date string
     *
     */
    public function toHTML(int|null $iTimestamp = 0 ): string
    {
        if (is_null($iTimestamp))
        {
            $iTimestamp = TIME();
        }

        $retValue = "";
        
        $aTempLocale = setlocale(LC_ALL, $this->lang);
        if (false === $aTempLocale)
        {
            $aTempLocale = setlocale(LC_ALL, 0);
        }

        if ((true === $this->useINTL) && (true === $this->intl_installed))
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
                echo "HOUSTON";
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
     *  Public function to transform dd.mm.yyyy into the current format
     *
     *  @param  string  $aDateString Date in dd.mm.yyyy
     *  @return string  $aFormat the formatted string
     *
     *  Following date-delimiter are supported
     *  "."     01.01.1971
     *  "-"     01-01-1971
     *  "/"     01/01/1971
     *
     *  Following format-settings are supported, including (space) and/or "." and/or "%".
     *  'dmy'   day - month - year
     *  'mdy'   month - day - year
     *  'ymd'   year - month - day
     *
     */
    public function transform (string $aDateString = "01.01.1971", string $aFormat="dmy"): string
    {
        $this->forceDate ($aDateString);
        $this->forceFormat ($aFormat);

        $temp = explode(".", $aDateString);
        $temp = array_map(
            function ($a) {
                return intval($a);
            },
            $temp
        );
        switch ($aFormat)
        {
            case 'dmy':
                $this->forceYear($temp[2]);
                $temp_time = mktime( 1, 0, 0, $temp[1], $temp[0], $temp[2]);
                break;

            case 'mdy':
                $this->forceYear($temp[2]);
                $temp_time = mktime( 1, 0, 0, $temp[0], $temp[1], $temp[2]);
                break;

            case 'ymd':
                $this->forceYear($temp[0]);
                $temp_time = mktime( 1, 0, 0, $temp[1], $temp[2], $temp[0]);
                break;

            /**
             *  M.f.i!
             *
             *  At this time (0.1.0.0) the default time is
             *  used instead of Error-Handling, thrown by an invalid format-string!
             *
             */
            default:
                $temp_time = time();
                break;
        }
        return $this->toHTML($temp_time);
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
        $pattern = ["*[\\/|.|-]+*"];
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

        $pattern = ["*[\\/|.|-]+*", "*[ |%]+*"];
        $replace = ["", ""];

        $aFormat = preg_replace($pattern, $replace, $aFormat);
    }

    /**
     *  private function that force a "short" Year to a "long" year
     *
     *  @param  string    $aYearStr The year - called by reference!
     *    @see  force_year
     *
     *    If the year is future oriented more than two years by default at runtime,
     *    19xx is assumed.
     */
    private function forceYear(string|int &$aYearStr = "1971"): void
    {
        $aYearStr = (string) $aYearStr;
        
        if (strlen($aYearStr) == 2)
        {
            $aYearStr = (((int) $aYearStr > $this->force_year + (int) DATE("y", TIME())) ? "19" : "20").$aYearStr;
        }
        if (strlen($aYearStr) > 4)
        {
            $aYearStr = substr($aYearStr, 0, 4);
        }
        
        $aYearStr = intval($aYearStr);
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
    public function setCoreLanguage(string $aKeyStr = ""): bool
    {

        $return_value = true;

        switch ($aKeyStr)
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
                $this->testLocale($aKeyStr);
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
    public function testLocale (string $aKey = "de_DE", bool $use_it = true): array
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

    /**
     *  Public function to translate a given internal format string for the datepickers (js).
     *
     *  @param  string  $sFormatString  A valid format string.
     *  @return string  The matching value inside the internal translation array as string or the current DATE_FORMAT or "" (empty) if non match.
     *
     */
    public function formatToDatepicker(string $sFormatString = ""): string
    {
        if (isset($this->coreDateFormatsDatePicker[$sFormatString])) {
            return $this->coreDateFormatsDatePicker[$sFormatString];
        } elseif (isset($this->coreDateFormatsDatePicker[DATE_FORMAT])) {
            return $this->coreDateFormatsDatePicker[DATE_FORMAT];
        } else {
            return "";
        }
    }

    /**
     *  Old jscalendar_to_timestamp function for backward compatibility
     *
     *  @param  string  $str    The given timestring.
     *  @param  integer $offset Optional offset. (timestamp!)
     *
     *  @return int A timestamp
     */
    public function calendarToTimestamp(string $str = "", int $offset = 0): int
    {
        $str = trim($str);
        if ($str == '0' || $str == '')
        {
            return 0;
        }

        // convert to yyyy-mm-dd
        // "dd.mm.yyyy"?
        if(preg_match('/^\d{1,2}\.\d{1,2}\.\d{2}(\d{2})?/', $str)) {
            $str = preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{2}(\d{2})?)/', '$3-$2-$1', $str);
        }

        // "mm/dd/yyyy"?
        if(preg_match('#^\d{1,2}/\d{1,2}/(\d{2}(\d{2})?)#', $str)) {
            $str = preg_replace('#^(\d{1,2})/(\d{1,2})/(\d{2}(\d{2})?)#', '$3-$1-$2', $str);
        }

        // use strtotime()
        if ($offset != 0)
        {
            return strtotime($str, $offset);
        }
        else
        {
            return strtotime($str);
        }
    }

    /**
     * Returns an array of valid "keys" for set_locale.
     *
     * @return array
     */
    public function detectPageLanguage() : array
    {
        // [1] $_GET
        $sTempCurrentPageLanguage = filter_input(INPUT_GET, "lang", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;

        // [1.1] none found - page-language?
        if (is_null($sTempCurrentPageLanguage))
        {
            $sTempCurrentPageLanguage = $GLOBALS['wb']->page['language'] ?? "EN";
        }

        // [2]
        $sBasic = strtolower($sTempCurrentPageLanguage)."_".strtoupper($sTempCurrentPageLanguage);

        // [3]
        return [
            $sBasic,
            $sBasic.".UTF-8",
            $sBasic."@euro"
        ];
    }

    /**
     * Check if INTL is installed.
     *
     * @return bool
     */
    public function checkINTLExtensionExists() : bool
    {
        $aAllExtensions = get_loaded_extensions();
        if (!in_array("intl", $aAllExtensions))
        {
            return false;
        }
        return true;
    }

    /**
     * Format via MySQL
     * @param   string      $format
     * @param   int         $timestamp
     * @param   string      $optionalLang
     * @return  string|NULL
     *
     * @see https://dev.mysql.com/doc/refman/5.7/en/date-and-time-functions.html#function_date-format
     * @see https://dev.mysql.com/doc/refman/8.4/en/date-and-time-functions.html#function_date-format
     * @see https://dev.mysql.com/doc/refman/8.4/en/locale-support.html
     *
     */
    public function formatWithMySQL(string $format = DEFAULT_DATE_FORMAT, int|null $timestamp = null, string $optionalLang = "" ): string|null
    {
        if ($timestamp === null)
        {
            $timestamp = time();
        }

        $sRealFormat = $this->coreDateFormatsMYSQL[$format] ?? $format;

        $tempLang = self::buildLanguageKey(
                (empty($optionalLang)
                    ? LANGUAGE
                    : $optionalLang
                )
        );

        $database = \Subway\core\sql\Database::getInstance();

        if ($tempLang !== "en_EN")
        {
            if($tempLang == "dk_DK")
            {
                $database->query("SET lc_time_names = 'da_DK';");
            }
            elseif($tempLang == "cz_CZ")
            {
                $database->query("SET lc_time_names = 'cs_CZ';");
            }
            else
            {
                $database->query("SET lc_time_names = '".$tempLang."';");
            }
        }
        else
        {
            $database->query("SET lc_time_names = 'en_gb';");
        }

        // see: https://www.epochconverter.com/programming/mysql
        $sQuery = ($timestamp < 0)
            ? "SELECT DATE_FORMAT( DATE_ADD(FROM_UNIXTIME(0), interval " . $timestamp . " second),'" . $sRealFormat . "');"
            : "SELECT DATE_FORMAT( FROM_UNIXTIME(" . $timestamp . "),'" . $sRealFormat . "');"
            ;
            
        return $database->get_one($sQuery);
    }

    /**
     * Forms a given string to the form "bb_BB", even if only "bb" is given.
     *
     * @param   string $key
     * @return  string
     *
     * @see https://database.guide/full-list-of-locales-in-mysql/
     *
     */
    public static function buildLanguageKey(string $key = LANGUAGE): string
    {
        if (preg_match("~^[a-z]{2}_[A-Z]{2}$~", $key)) {
            // given string is "in correct form"
            return $key;
        } elseif (strlen($key) >= 2) {
            // more than two chars - use the first two ones
            $key = substr(trim($key), 0, 2);
            return strtolower($key) . "_" . strtoupper($key);
        } else {
            return "Error: need at least two chars for key! One given!";
        }
    }
    
    /**
     *  @param string $sLanguage     A valid language-key, e.g. "de", "de_DE"
     *  @param bool   $bAbbreviated  Use abbreviated names. Default is "false".
     *
     *  @return Linear array whith the weekday-names. Index starts with 1 (Monday)!
     *
     *  @see https://dev.mysql.com/doc/refman/8.4/en/locale-support.html
     *  @see https://database.guide/full-list-of-locales-in-mysql/
     *
     *  @usage/e.g.
     *    // [1]
     *    $oTOOL = $oTOOL = LEPTON_date::getInstance();
     *    $test = $oTOOL->getWeekdayNames("de", true);
     *
     *    will result an array with abbr. weekday names:
     *
     *    Array
     *    (
     *        [1] => Mo
     *        [2] => Di
     *        [3] => Mi
     *        [4] => Do
     *        [5] => Fr
     *        [6] => Sa
     *        [7] => So
     *    )
     *
     *    // [2]
     *    $oTOOL = LEPTON_date::getInstance();
     *    $test = $oTOOL->getWeekdayNames("da_DK");
     *
     *    will result an array with full weekday names:
     *
     *    Array
     *    (
     *        [1] => mandag
     *        [2] => tirsdag
     *        [3] => onsdag
     *        [4] => torsdag
     *        [5] => fredag
     *        [6] => lørdag
     *        [7] => søndag
     *    )
     */
    public function getWeekdayNames(string $sLanguage, bool $bAbbreviated = false): array
    {
        $returnValue = [];
        
        // Der 5. Januar 1970 war ein Montag.
        $tag     = 5;
        $monat   = 1;
        $jahr    = 1970;

        $stunde  = 1;
        $minute  = 0;
        $sekunde = 0;

        for ($i = 0; $i < 7; $i++)
        {
            $iTimestamp = mktime($stunde, $minute, $sekunde, $monat, $tag+$i, $jahr);
            $returnValue[$i+1] = $this->formatWithMySQL(($bAbbreviated ? "%a" : "%W"), $iTimestamp, $sLanguage);
        }

        return $returnValue;
    }
    
    /**
     *  @param string $sLanguage     A valid language-key, e.g. "de", "de_DE"
     *  @param bool   $bAbbreviated  Use abbreviated names. Default is "false".
     *
     *  @return Linear array whith the month-names. Index starts with 1!
     *
     *  @see https://dev.mysql.com/doc/refman/8.4/en/locale-support.html
     *  @see https://database.guide/full-list-of-locales-in-mysql/
     *
     */
    public function getMonthNames(string $sLanguage, bool $bAbbreviated = false): array
    {
        $returnValue = [];

        // Der 5. Januar 1970 war ein Montag.
        $tag     = 5;

        $jahr    = 1970;

        $stunde  = 1;
        $minute  = 0;
        $sekunde = 0;

        for ($monat = 1; $monat <= 12; $monat++)
        {
            $iTimestamp = mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
            $returnValue[$monat] = $this->formatWithMySQL(($bAbbreviated ? "%b" : "%M"), $iTimestamp, $sLanguage);
        }

        return $returnValue;
    }
    
    public function getLocaleList(): array
    {
        return self::MYSQL_LOCALES;
    }

    /**
     * Get the date formats.
     *
     * @return array   An assoc, array with the date-formats as key, and the current time as value.
     *
     * @throws Exception
     */
    public static function getDateformats(): array
    {
        global $user_time;
        global $TEXT;
        
        // Get the current time (in the users timezone if required)
        $actual_time = time();

        // Get "System Default"
        $sSystemDefault = "";
        if (isset($user_time) && $user_time === true)
        {
            $sSystemDefault = date(DEFAULT_DATE_FORMAT, $actual_time).' (';
            $sSystemDefault .= ($TEXT['SYSTEM_DEFAULT'] ?? self::SYSTEM_DEFAULT_STR).')';
        }

        // Add values to list
        $dateFormats = [
            'system_default' => $sSystemDefault,
            'j.n.Y' => date('j.n.Y', $actual_time).' (j.n.Y)',
            'm/d/Y' => date('m/d/Y', $actual_time).' (M/D/Y)',
            'd/m/Y' => date('d/m/Y', $actual_time).' (D/M/Y)',
            'm.d.Y' => date('m.d.Y', $actual_time).' (M.D.Y)',
            'd.m.Y' => date('d.m.Y', $actual_time).' (D.M.Y)',
            'm-d-Y' => date('m-d-Y', $actual_time).' (M-D-Y)',
            'd-m-Y' => date('d-m-Y', $actual_time).' (D-M-Y)',
            'Y-m-d' => date('Y-m-d', $actual_time).' (Y-M-D)', // new in L* 7.4
            'Y.m.d' => date('Y.m.d', $actual_time).' (Y.M.D)', // new in L* 7.4
        ];

        /**
         * [1.1] We need this for the correct language (-terms) for the months- and weekday-names!
         * Keep in mind that 'date' will always display them in english!
         *
         */
        $oThis = self::getInstance();
        $oThis->setCoreLanguage(DEFAULT_LANGUAGE);
        
        /**
         * [1.2] A list with the terms (months names/weekdays) inside the formatted date.
         */
        $aFormatList = [
            'D M d, Y',     // [1] Fri Jan 17, 2025
            'M d Y',        // [2] Jan 17 2025
            'd M Y',        // [3] 17. January 2025
            'jS F, Y',      // [4] 17th January, 2025
            'l, jS F, Y'    // [5] Friday, 17th January, 2025
        ];

        /**
         * [1.3] Here we go
         */
        if (LANGUAGE == 'EN')
        {
            foreach ($aFormatList as &$format)
            {
                $dateFormats[$format] = $oThis->formatWithMySQL($format, $actual_time);
            }
        } else
        {
            // 1
            $patterns = [
                "/([0-9]{1,2})th/i",
                "/([0-9]{1,2})rd/i",
                "/([0-9]{1,2})st/i",
            ];
            
            foreach ($aFormatList as &$format)
            {
                $sFormatedTime = $oThis->formatWithMySQL($format, $actual_time);
                if (str_contains($format, "S"))
                {
                    $sFormatedTime = preg_replace(
                        $patterns,
                        "$1".(LANGUAGE == "NL" ? "" : "."),
                        $sFormatedTime
                    );
                }
                $dateFormats[$format] = $sFormatedTime;
            }
        }
        unset($format);
        return $dateFormats;
    }

    /**
     *  Get the time formats.
     *
     *  @return array   An assoc. array with the time-formats as key, and the current time as value.
     *
     */
    public static function getTimeformats(): array
    {
        global $user_time;
        global $TEXT;
        
        // Get the current time (in the users timezone if required)
        $actual_time = time();

        // Get "System Default"
        $sSystemDefault = "";

        if ((isset($user_time)) && ($user_time === true))
        {
            $sSystemDefault = date(DEFAULT_TIME_FORMAT, $actual_time).' (';
            $sSystemDefault .= ($TEXT['SYSTEM_DEFAULT'] ?? self::SYSTEM_DEFAULT_STR).')';
        }

        return [
            'system_default'    => $sSystemDefault,
            'H:i'   => date('H:i',   $actual_time),
            'H:i:s' => date('H:i:s', $actual_time),
            'g:i a' => date('g:i a', $actual_time), // Lowercase Ante meridiem and Post meridiem
            'g:i A' => date('g:i A', $actual_time)  // Uppercase Ante meridiem and Post meridiem
        ];
    }
 
    /**
     *  Get an index array of days, started with 1!
     *
     *  @param  string $lang    A Language key. None given, "en_EN" is used per default.
     *                          See: ~/modules/lib_lepton/datetools/constants.php for details.
     *
     *  @param  bool   $abbr    True use the 'shortnames'. Default is false: full name.
     *
     *  @return array   An array with days.
     *
     */
    public static function getDays(string $lang= "en_EN", bool $abbr= false): array
    {
        return self::getInstance()->getWeekdayNames($lang, $abbr);  // Wochentagsnamen ausgeschrieben
    }

    /**
     *  Get months.
     *
     *  @param  string $lang    A Language key. None given, "en_EN" is used per default.
     *                          See: ~/modules/lib_lepton/datetools/constants.php for details.
     *
     *  @param  bool   $abbr    True use the 'shortnames'. Default is false: full name.
     *
     *  @return array   An array with months.
     *
     */
    public static function getMonths(string $lang= "en_EN", bool $abbr= false): array
    {
        return self::getInstance()->getMonthNames($lang, $abbr); // Monatsnamen ausgeschrieben
    }

    /**
     *  Get the time zones.
     *
     *  @return array   A linear array with the basics timezones.
     *
     */
    public static function getTimezones(): array
    {
        return [
            "Pacific/Kwajalein",
            "Pacific/Samoa",
            "Pacific/Honolulu",
            "America/Anchorage",
            "America/Los_Angeles",
            "America/Phoenix",
            "America/Mexico_City",
            "America/Lima",
            "America/Caracas",
            "America/Halifax",
            "America/Buenos_Aires",
            "Atlantic/Reykjavik",
            "Atlantic/Azores",
            "Europe/London",
            "Europe/Berlin",
            "Europe/Kaliningrad",
            "Europe/Moscow",
            "Asia/Tehran",
            "Asia/Baku",
            "Asia/Kabul",
            "Asia/Tashkent",
            "Asia/Calcutta",
            "Asia/Colombo",
            "Asia/Bangkok",
            "Asia/Hong_Kong",
            "Asia/Tokyo",
            "Australia/Adelaide",
            "Pacific/Guam",
            "Etc/GMT+10",
            "Pacific/Fiji"
        ];
    }
}
