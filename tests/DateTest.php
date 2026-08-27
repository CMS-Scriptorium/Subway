<?php

/**
 *  [0] Basics
 *      PHPUnit 13.2.4
 *      -- last test local: 2026-08-24 (Aldus)
 *
 *  to get phpunit use
 *
 *  wget -O phpunit https://phar.phpunit.de/phpunit-13.phar
 *
 *  @example
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/
 *   php phpunit.phar --colors='always' --display-warnings wbce/modules/Subway/tests/DateTest.php
 *
 *   Variation: asumee you are inside the "Subway" directory
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/wbce/modules/Subway
 *   php ../../../phpunit.phar --colors='always' --display-warnings tests/DateTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings wbce/modules/Subway/tests/DateTest.php
 *   php ../../../phpunit.phar --colors='always' --display-deprecations --display-warnings tests/DateTest.php
 *
 *  @notice
 *   To use a spezific php version, e.g. under MacOS e.g. MAMP you will have to export like
 *
 *       export PATH=/Applications/MAMP/bin/php/php8.4.1/bin:$PATH
 *
 *   to get the correct PHP version to run.
 */

declare(strict_types=1);

namespace Subway\tests;

use PHPUnit\Framework\TestCase;
use Subway\core\Date;
use Subway\core\sql\Database;

require_once dirname(__DIR__) . '/core/traits/DateFormattingTrait.php';
require_once dirname(__DIR__) . '/core/Date.php';
require_once dirname(__DIR__) . '/core/sql/Database.php';

final class DateTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset singletons between tests
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Date::$instance = null;
        Database::$instance = null;
    }

    public function testBuildLanguageKey_fullForm(): void
    {
        $this->assertSame('de_DE', Date::buildLanguageKey('de_DE'));
    }

    public function testBuildLanguageKey_twoChars(): void
    {
        $this->assertSame('de_DE', Date::buildLanguageKey('de'));
    }

    public function testBuildLanguageKey_singleChar(): void
    {
        $this->assertStringStartsWith('Error:', Date::buildLanguageKey('d'));
    }

    public function testGetTimezones_containsEuropeBerlin(): void
    {
        $zones = Date::getTimezones();
        $this->assertIsArray($zones);
        $this->assertContains('Europe/Berlin', $zones);
    }

    public function testGetLocaleList_returnsArray(): void
    {
        $o = Date::getInstance();
        $list = $o->getLocaleList();
        $this->assertIsArray($list);
        $this->assertArrayHasKey('de_DE', $list);
    }

    public function testCalendarToTimestamp_ddmmyyyy_and_empty_and_slash(): void
    {
        $o = Date::getInstance();

        // dd.mm.yyyy
        $ts1 = $o->calendarToTimestamp('01.01.1971');
        $this->assertSame(strtotime('1971-01-01'), $ts1);

        // mm/dd/yyyy
        $ts2 = $o->calendarToTimestamp('12/31/1999');
        $this->assertSame(strtotime('1999-12-31'), $ts2);

        // empty or '0' returns 0
        $this->assertSame(0, $o->calendarToTimestamp(''));
        $this->assertSame(0, $o->calendarToTimestamp('0'));

        // with offset
        $offset = 1000;
        $expected = strtotime('1971-01-01', $offset);
        $this->assertSame($expected, $o->calendarToTimestamp('01.01.1971', $offset));
    }

    public function testSetFormat_switchesBetweenIntlAndNormal(): void
    {
        // non-INTL
        $o = Date::getInstance(false);
        $this->assertFalse($o->useINTL);
        $o->setFormat('Y-m-d');
        $this->assertSame('Y-m-d', $o->format);

        // reset singleton
        Date::$instance = null;

        // with INTL enabled
        $o2 = Date::getInstance(true);
        $this->assertTrue($o2->useINTL);
        // setFormat should store into sINTLFormat when useINTL is true
        $o2->setFormat('d.M.yyyy');
        $this->assertSame('d.M.yyyy', $o2->sINTLFormat);
    }

    public function testDetectPageLanguage_default(): void
    {
        // ensure no global wb defined
        unset($GLOBALS['wb']);

        $o = Date::getInstance();
        $res = $o->detectPageLanguage();
        $this->assertIsArray($res);
        $this->assertSame('en_EN', $res[0]);
        $this->assertSame('en_EN.UTF-8', $res[1]);
    }

    public function testFormatWithMySQL_and_GetWeekdayAndMonthNames_withMockedDatabase(): void
    {
        // Create a fake database object that collects queries and returns predictable values
        $fakeDb = new class {
            public array $queries = [];

            public function query(string $q): array
            {
                // store query and return empty array as some code expects array
                $this->queries[] = $q;
                return [];
            }

            public function __call(string $name, array $args)
            {
                if ($name == "get_one")
                {
                    return $this->getOne($args[0]);
                }
            }

            public function getOne(string $q): string
            {
                $lookFor = "";      // SonarQube
                $replaceValue = ""; // SonarQube

                // Return different values depending on requested MySQL format
                $testMap = [
                    "%a"    => "Mo",
                    "%W"    => "Montag",
                    "%M"    => "Januar",
                    "%b"    => "Jan"
                ];

                foreach ($testMap as $lookFor => $replaceValue)
                {
                    if (str_contains($q, $lookFor))
                    {
                        return $replaceValue;
                    }
                }

                return "VALUE";
            }
        };

        // Inject fake database instance into Database singleton
        \Subway\core\sql\Database::$instance = $fakeDb;

        // Reset Date singleton and use instance
        Date::$instance = null;
        $o = Date::getInstance();

        // Call formatWithMySQL directly (pass explicit format to avoid DEFAULT_DATE_FORMAT constant)
        $res = $o->formatWithMySQL('%M', 1600000000, 'de');
        $this->assertSame('Januar', $res);

        // Test getWeekdayNames abbreviated
        $daysAbbrev = $o->getWeekdayNames('de', true);
        $this->assertCount(7, $daysAbbrev);
        $this->assertSame('Mo', $daysAbbrev[1]);

        // Test getWeekdayNames full
        $daysFull = $o->getWeekdayNames('de', false);
        $this->assertCount(7, $daysFull);
        $this->assertSame('Montag', $daysFull[1]);

        // Test getMonthNames abbreviated and full
        $monthsFull = $o->getMonthNames('de', false);
        $this->assertCount(12, $monthsFull);
        $this->assertSame('Januar', $monthsFull[1]);

        $monthsAbbrev = $o->getMonthNames('de', true);
        $this->assertCount(12, $monthsAbbrev);
        $this->assertSame('Jan', $monthsAbbrev[1]);

        // Ensure that the fake DB recorded lc_time_names SET queries
        $this->assertNotEmpty($fakeDb->queries, 'Database->query should have been called to set lc_time_names');
        $foundSet = false;
        foreach ($fakeDb->queries as $q) {
            if (str_starts_with(trim($q), 'SET lc_time_names')) {
                $foundSet = true;
                break;
            }
        }
        $this->assertTrue($foundSet, 'Expected Database->query to include a SET lc_time_names statement');
    }
}
