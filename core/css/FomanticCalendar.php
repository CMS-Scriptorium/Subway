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

namespace Subway\core\css;

use Subway\core\Date;
use Subway\core\template\TwigBox;
use Subway\core\traits\Singleton;

/**
 *  @see    https://fomantic-ui.com/modules/calendar.html#/examples
 */
class FomanticCalendar
{
    use Singleton;

    // Own instance for this class!
    public static $instance;
    
    public object|null $oTWIG = null;
    
    public array $defaultEvent = [
        'date'      => '',  // YYYY-MM-DD
        'message'   => '',
        'class'     => 'inverted olive',   // cellclass
        'variation' => 'olive'         // tooltip var
    ];
    
    public array $dayColors = [
        'Summertime'        => ['blue', 'inverted blue tiny'],
        'National_holyday'  => ['orange' , 'inverted orange'],
        'National_day'      => ['yellow' , 'inverted yellow']
    ];

    protected int $displayMonths = 1;
    
    protected array $events = [];
    protected array $disabledDates = [];
    
    public function __construct()
    {
        $this->oTWIG = TwigBox::getInstance();
        $this->oTWIG->registerModule("Subway", "Subway");

        $this->setFixedEventsGer();
        $this->setDisabledDatesGer();
    }
    
    public function addEvent(string|int $sDate, string $sMessage, string $sClass="", string $sVariation = ""): void
    {
        $this->events[] = [
            'date'      => $sDate,     // YYYY-MM-DD
            'message'   => $sMessage,
            'class'     => $sClass,    // cellclass
            'variation' => $sVariation // tooltip var
        ];
    
    }
    
    public function addDisabledDate(string|int $sDate, string $sMessage, string $sClass="", string $sVariation = "", bool $bInverted = false): void
    {
        $this->disabledDates[] = [
            'date'      => $sDate,     // YYYY-MM-DD
            'message'   => $sMessage,
            'class'     => $sClass,    // cellclass
            'variation' => $sVariation,// tooltip var
            'inverted'  => $bInverted
        ];
    
    }

    /**
     * Generates the complete calendar source.
     *
     * @param   string  $additionalCssClass     An optional css-class identifier.
     *
     * @return  string  The generated (HTML-) source(-code).
     */
    public function generate(string $additionalCssClass = ""): string
    {
        $iCurrendSectionID = $GLOBALS['section_id'] ?? random_int(12000, 99999);

        $data = [
            'divID'              => "ModSubWay97_".$iCurrendSectionID, // Must be unique!
            'additionalCSSClass' => $additionalCssClass,
            'initialDate'        => date("Y-m-d", time()+TIMEZONE),
            'daynames'           => $this->getDayNames(), // "['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag']"
            'monthsnames'        => $this->getMonthNames(),
            'events'             => $this->events,
            'disabledDates'      => $this->disabledDates,
            'displayMonths'      => $this->displayMonths
        ];

        return $this->oTWIG->render(
            '@Subway/calendar.lte',
            $data
        );
    }

    public function setDisplayMonths(int $iNumber): void
    {
        if ($iNumber < 1)
        {
            $iNumber = 1;
        }
        
        if ($iNumber > 6)
        {
            $iNumber = 6;
        }
        $this->displayMonths = $iNumber;
    }
    
    protected function setFixedEventsGer(): void
    {
        $year = date("Y"); // Actual year
        
        for ($i = $year-1; $i <= $year+1; $i++)
        {
            // Zeitumstellung
            $temp = strtotime('-1 week sun april' . $i); // Letzter So im März
            $this->addEvent(
                date("Y-m-d", $temp),
                'Sommerzeit (+1)',
                $this->dayColors['Summertime'][0],
                $this->dayColors['Summertime'][1]
            );
            
            $temp2 = strtotime('-1 week sun november' . $i); // Letzter So im Oktober
            $this->addEvent(
                date("Y-m-d", $temp2),
                'Normalzeit (-1)',
                $this->dayColors['Summertime'][0],
                $this->dayColors['Summertime'][1]
            );
        }
    }
    
    protected function setDisabledDatesGer(): void
    {
        $year = date("Y"); // Actual year
        
        for ($i = $year-1; $i <= $year+1; $i++)
        {
            $this->addDisabledDate(
                $i.'-01-01',
                'Neujahr'
            );

            $this->addDisabledDate(
                $i.'-10-03',
                'Tag der deutschen Einheit',
                $this->dayColors['National_day'][0],
                $this->dayColors['National_day'][1],
                true
            );

            $this->addDisabledDate(
                $i.'-12-24',
                'Weihnachten',
                $this->dayColors['National_holyday'][0],
                $this->dayColors['National_holyday'][1],
                true
            );

            $this->addDisabledDate(
                $i.'-12-25',
                '1. Weihnachtsfeiertag',
                $this->dayColors['National_holyday'][0],
                $this->dayColors['National_holyday'][1],
                true
            );

            $this->addDisabledDate(
                $i.'-12-26',
                '2. Weihnachtsfeiertag',
                $this->dayColors['National_holyday'][0],
                $this->dayColors['National_holyday'][1],
                true
            );

            $this->addDisabledDate(
                $i.'-12-31',
                'Sylvester'
            );

            $temp = strtotime('-1 week sun april' . $i);
            $this->addDisabledDate(
                date("Y-m-d", $temp),
                'Sommerzeit (+1)',
                $this->dayColors['Summertime'][0],
                $this->dayColors['Summertime'][1]
            );

            $temp2 = strtotime('-1 week sun november' . $i);
            $this->addDisabledDate(
                date("Y-m-d", $temp2),
                'Normalzeit (-1)',
                $this->dayColors['Summertime'][0],
                $this->dayColors['Summertime'][1]
            );

            // Ostern
            $ostern = strtotime("+ " . (easter_days($i)) . " days", mktime(0, 0, 0, 3, 21, $i));
            $this->addDisabledDate(
                date("Y-m-d", $ostern),
                'Ostersonntag',
                $this->dayColors['National_holyday'][0],
                $this->dayColors['National_holyday'][1]
            );

            // Ostermontag
            $ostermontag = strtotime("+1 day", $ostern);
            $this->addDisabledDate(
                date("Y-m-d", $ostermontag),
                'Ostermontag',
                $this->dayColors['National_holyday'][0],
                $this->dayColors['National_holyday'][1]
            );
        }
    }

    protected function getDayNames(): string
    {
        $oTOOL = Date::getInstance();
        $aNames = $oTOOL->getWeekdayNames("de_DE", true);

        $temp = array_pop($aNames);
        array_unshift($aNames, $temp);

        return "['".implode("','", $aNames)."']";
    }

    protected function getMonthNames(): string
    {
        $oTOOL = Date::getInstance();
        $aNames = $oTOOL->getMonthNames("de_DE", false);
        return "['".implode("','", $aNames)."']";
    }
}
