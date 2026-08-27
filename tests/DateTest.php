<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Subway\core\Date;

require_once __DIR__ . '/../core/Date.php';

final class DateTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset singleton state between tests
        Date::$instance = null;
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
}
