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

use IntlDateFormatter;

/**
 * Trait for date formatting operations
 * Handles conversion between different date format standards
 */
trait DateFormattingTrait
{
    /**
     * Translation array for PHP date formats to strftime formats
     */
    public array $coreDateFormatsPHP = [
        'l, jS F, Y' => '%A, %e %B, %Y',
        'jS F, Y'    => '%e %B, %Y',
        'd M Y'      => '%d %a %Y',
        'M d Y'      => '%a %d %Y',
        'D M d, Y'   => '%a %b %d, %Y',
        'd-m-Y'      => '%d-%m-%Y',
        'm-d-Y'      => '%m-%d-%Y',
        'd.m.Y'      => '%d.%m.%Y',
        'm.d.Y'      => '%m.%d.%Y',
        'd/m/Y'      => '%d/%m/%Y',
        'm/d/Y'      => '%m/%d/%Y',
        'j.n.Y'      => '%e.%n.%Y'
    ];

    /**
     * Translation array for PHP date formats to MySQL DATE_FORMAT strings
     */
    public array $coreDateFormatsMYSQL = [
        'l, jS F, Y' => '%W, %D %M, %Y',
        'jS F, Y'    => '%D %M, %Y',
        'd M Y'      => '%e. %M %Y',
        'M d Y'      => '%b %e %Y',
        'D M d, Y'   => '%a %b %e, %Y',
        'd-m-Y'      => '%d-%m-%Y',
        'm-d-Y'      => '%m-%d-%Y',
        'd.m.Y'      => '%d.%m.%Y',
        'm.d.Y'      => '%m.%d.%Y',
        'd/m/Y'      => '%d/%m/%Y',
        'm/d/Y'      => '%m/%d/%Y',
        'j.n.Y'      => '%e.%c.%Y',
        'Y-m-d'      => '%Y-%m-%d',
        'Y.m.d'      => '%Y.%m.%d'
    ];

    /**
     * Translation array for time formats (PHP strftime)
     */
    public array $coreTimeFormatsPHP = [
        'g:i A' => '%I:%M %p',
        'g:i a' => '%I:%M %P',
        'H:i:s' => '%H:%M:%S',
        'H:i'   => '%H:%M'
    ];

    /**
     * Translation array for time formats (MySQL DATE_FORMAT)
     */
    public array $coreTimeFormatsMYSQL = [
        'g:i A' => '%l:%i %p',
        'g:i a' => '%r %p',
        'H:i:s' => '%H:%i:%s',
        'H:i'   => '%H:%i'
    ];

    /**
     * Translation array for jQuery DatePicker formats
     */
    public array $coreDateFormatsDatePicker = [
        'l, jS F, Y' => 'DD, d. MM yy',
        'jS F, Y'    => 'd. MM, yy',
        'd M Y'      => 'd. MM yy',
        'M d Y'      => 'M d yy',
        'D M d, Y'   => 'D M d, yy',
        'd-m-Y'      => 'd-m-yy',
        'm-d-Y'      => 'm-d-yy',
        'd.m.Y'      => 'd.m.yy',
        'm.d.Y'      => 'm.d.yy',
        'Y-m-d'      => 'yy-mm-dd',
        'Y.m.d'      => 'yy.mm.dd',
        'd/m/Y'      => 'd/m/yy',
        'm/d/Y'      => 'm/d/yy',
        'j.n.Y'      => 'd.m.yy'
    ];

    /**
     * Translation array for IntlDateFormatter (ICU) formats
     */
    public array $coreDateFormatsINTL = [
        'l, jS F, Y' => 'A, e B, yyyy',
        'jS F, Y'    => 'e B, yyyy',
        'd M Y'      => 'd M yyyy',
        'M d Y'      => 'M d yyyy',
        'D M d, Y'   => 'EE d MM, yyyy',
        'd-m-Y'      => 'd-M-yy',
        'm-d-Y'      => 'M-d-yy',
        'd.m.Y'      => 'd.M.yy',
        'm.d.Y'      => 'M.d.yy',
        'd/m/Y'      => 'd/M/yy',
        'm/d/Y'      => 'M/d/yy',
        'j.n.Y'      => 'd.M.yyyy'
    ];

    /**
     * Public function to translate a given internal format string for datepickers
     *
     * @param string $sFormatString A valid format string
     * @return string The matching datepicker format or empty string
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
     * Get date format translation for specified output format
     *
     * @param string $inputFormat Input format key
     * @param string $outputType Type of output: 'php', 'mysql', 'intl', 'datepicker'
     * @return string|null Translated format or null if not found
     */
    public function getFormatTranslation(string $inputFormat, string $outputType = 'php'): ?string
    {
        $formats = match ($outputType) {
            'php'       => $this->coreDateFormatsPHP,
            'mysql'     => $this->coreDateFormatsMYSQL,
            'intl'      => $this->coreDateFormatsINTL,
            'datepicker'=> $this->coreDateFormatsDatePicker,
            default     => []
        };

        return $formats[$inputFormat] ?? null;
    }
}
