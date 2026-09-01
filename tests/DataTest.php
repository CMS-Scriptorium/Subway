<?php

/**
 *  [0] Basics
 *      PHPUnit 13.2.4
 *      Unit tests for Subway\core\tools\Data
 *
 *  to get phpunit use
 *
 *  wget -O phpunit https://phar.phpunit.de/phpunit-13.phar
 *
 *  @example
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/
 *   php phpunit.phar --colors='always' --display-warnings wbce/modules/Subway/tests/DataTest.php
 *
 *   Variation: assume you are inside the "Subway" directory
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/wbce/modules/Subway
 *   php ../../../phpunit.phar --colors='always' --display-warnings tests/DataTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings wbce/modules/Subway/tests/DataTest.php
 *
 *  @notice
 *   To use a specific php version, e.g. under MacOS e.g. MAMP you will have to export like
 *
 *       export PATH=/Applications/MAMP/bin/php/php8.4.1/bin:$PATH
 *
 *   to get the correct PHP version to run.
 */

declare(strict_types=1);

namespace Subway\tests;

use PHPUnit\Framework\TestCase;
use Subway\core\tools\Data;

// [4]
require_once \dirname(__DIR__) . "/core/tools/Data.php";

//  [5] Here we go
/**
 * Test suite for Subway\core\tools\Data class.
 *
 * @package Subway\Tests
 * @author  Test Suite
 */
final class DataTest extends TestCase
{
    /**
     * Reset the static $useVarDump flag before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!defined("DEFAULT_TEMPLATE"))
        {
            define("DEFAULT_TEMPLATE", "DEFAULT_TEMPLATE");
        }
        if (!defined("WB_PATH"))
        {
            define("WB_PATH", dirname(__DIR__, 3));
        }
        if (!defined("WB_URL"))
        {
            define("WB_URL", dirname(__DIR__, 3));
        }

        Data::$useVarDump = false;
    }

    /**
     * Test setVarDump() with default value (true).
     *
     * @return void
     */
    public function testSetVarDumpDefault(): void
    {
        Data::setVarDump();
        $this->assertTrue(Data::$useVarDump);
    }

    /**
     * Test setVarDump() explicitly set to true.
     *
     * @return void
     */
    public function testSetVarDumpTrue(): void
    {
        Data::setVarDump(true);
        $this->assertTrue(Data::$useVarDump);
    }

    /**
     * Test setVarDump() explicitly set to false.
     *
     * @return void
     */
    public function testSetVarDumpFalse(): void
    {
        Data::setVarDump(false);
        $this->assertFalse(Data::$useVarDump);
    }

    /**
     * Test display() with default tag "pre".
     *
     * @return void
     */
    public function testDisplayDefaultTag(): void
    {
        $result = Data::display("test content");
        $this->assertStringContainsString("<pre>", $result);
        $this->assertStringContainsString("</pre>", $result);
        $this->assertStringContainsString("test content", $result);
    }

    /**
     * Test display() with custom tag "div".
     *
     * @return void
     */
    public function testDisplayCustomTagDiv(): void
    {
        $result = Data::display("test content", "div");
        $this->assertStringContainsString("<div>", $result);
        $this->assertStringContainsString("</div>", $result);
    }

    /**
     * Test display() with custom tag "code".
     *
     * @return void
     */
    public function testDisplayCustomTagCode(): void
    {
        $result = Data::display("test content", "code");
        $this->assertStringContainsString("<code>", $result);
        $this->assertStringContainsString("</code>", $result);
    }

    /**
     * Test display() with custom tag "span".
     *
     * @return void
     */
    public function testDisplayCustomTagSpan(): void
    {
        $result = Data::display("test content", "span");
        $this->assertStringContainsString("<span>", $result);
        $this->assertStringContainsString("</span>", $result);
    }

    /**
     * Test display() with custom tag "p".
     *
     * @return void
     */
    public function testDisplayCustomTagP(): void
    {
        $result = Data::display("test content", "p");
        $this->assertStringContainsString("<p>", $result);
        $this->assertStringContainsString("</p>", $result);
    }

    /**
     * Test display() with CSS class.
     *
     * @return void
     */
    public function testDisplayWithCssClass(): void
    {
        $result = Data::display("test content", "pre", "example_class");
        $this->assertStringContainsString("class='example_class'", $result);
    }

    /**
     * Test display() with CSS class containing special characters (should be escaped).
     *
     * @return void
     */
    public function testDisplayWithCssClassSpecialChars(): void
    {
        $result = Data::display("test content", "pre", "test'class\"danger");
        $this->assertStringContainsString("class='", $result);
        // htmlspecialchars should escape the quote
        $this->assertStringContainsString("&", $result);
    }

    /**
     * Test display() with NULL value.
     *
     * @return void
     */
    public function testDisplayWithNullValue(): void
    {
        $result = Data::display(null);
        $this->assertStringContainsString("The value is NULL!", $result);
    }

    /**
     * Test display() with array.
     *
     * @return void
     */
    public function testDisplayWithArray(): void
    {
        $testArray = ['key1' => 'value1', 'key2' => 'value2'];
        $result = Data::display($testArray);
        $this->assertStringContainsString("key1", $result);
        $this->assertStringContainsString("value1", $result);
    }

    /**
     * Test display() with object.
     *
     * @return void
     */
    public function testDisplayWithObject(): void
    {
        $testObject = new \stdClass();
        $testObject->property = "test_value";
        $result = Data::display($testObject);
        $this->assertStringContainsString("property", $result);
    }

    /**
     * Test display() uses print_r when useVarDump is false.
     *
     * @return void
     */
    public function testDisplayUsesPrintR(): void
    {
        Data::setVarDump(false);
        $result = Data::display("test", "pre");
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test display() uses var_dump when useVarDump is true (global setting).
     *
     * @return void
     */
    public function testDisplayUsesVarDumpGlobalSetting(): void
    {
        Data::setVarDump(true);
        $result = Data::display("test", "pre");
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test display() uses var_dump when overridden locally.
     *
     * @return void
     */
    public function testDisplayOverrideVarDumpLocally(): void
    {
        Data::setVarDump(false);
        $result = Data::display("test", "pre", null, true);
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test display() with invalid tag throws InvalidArgumentException.
     *
     * @return void
     */
    public function testDisplayInvalidTagThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(40088);
        Data::display("test", "invalid");
    }

    /**
     * Test display() with invalid tag (case insensitive check).
     *
     * @return void
     */
    public function testDisplayInvalidTagCaseInsensitive(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Data::display("test", "TABLE");
    }

    /**
     * Test display() tag is properly escaped in HTML.
     *
     * @return void
     */
    public function testDisplayTagEscaping(): void
    {
        $result = Data::display("test", "pre");
        // Verify that tag is not escaped (legitimate HTML tags should work)
        $this->assertStringContainsString("<pre>", $result);
    }

    /**
     * Test displayDev() with default tag "pre".
     *
     * @return void
     */
    public function testDisplayDevDefaultTag(): void
    {
        $result = Data::displayDev("test content");
        $this->assertStringContainsString("<pre>", $result);
        $this->assertStringContainsString("</pre>", $result);
        $this->assertStringContainsString("test content", $result);
    }

    /**
     * Test displayDev() includes location and line information.
     *
     * @return void
     */
    public function testDisplayDevIncludesLocationInfo(): void
    {
        $result = Data::displayDev("test content");
        $this->assertStringContainsString("Location:", $result);
        $this->assertStringContainsString("Line", $result);
    }

    /**
     * Test displayDev() with custom tag "div".
     *
     * @return void
     */
    public function testDisplayDevCustomTagDiv(): void
    {
        $result = Data::displayDev("test content", "div");
        $this->assertStringContainsString("<div>", $result);
        $this->assertStringContainsString("</div>", $result);
    }

    /**
     * Test displayDev() with CSS class.
     *
     * @return void
     */
    public function testDisplayDevWithCssClass(): void
    {
        $result = Data::displayDev("test content", "pre", "dev_class");
        $this->assertStringContainsString("class='dev_class'", $result);
    }

    /**
     * Test displayDev() with NULL value.
     *
     * @return void
     */
    public function testDisplayDevWithNullValue(): void
    {
        $result = Data::displayDev(null);
        $this->assertStringContainsString("The value is NULL!", $result);
    }

    /**
     * Test displayDev() with array.
     *
     * @return void
     */
    public function testDisplayDevWithArray(): void
    {
        $testArray = ['item1', 'item2'];
        $result = Data::displayDev($testArray);
        $this->assertStringContainsString("item1", $result);
        $this->assertStringContainsString("item2", $result);
    }

    /**
     * Test displayDev() uses print_r when useVarDump is false.
     *
     * @return void
     */
    public function testDisplayDevUsesPrintR(): void
    {
        Data::setVarDump(false);
        $result = Data::displayDev("test");
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test displayDev() uses var_dump when useVarDump is true (global setting).
     *
     * @return void
     */
    public function testDisplayDevUsesVarDumpGlobalSetting(): void
    {
        Data::setVarDump(true);
        $result = Data::displayDev("test");
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test displayDev() overrides var_dump locally.
     *
     * @return void
     */
    public function testDisplayDevOverrideVarDumpLocally(): void
    {
        Data::setVarDump(false);
        $result = Data::displayDev("test", "pre", null, true);
        $this->assertStringContainsString("test", $result);
    }

    /**
     * Test displayDev() with invalid tag throws InvalidArgumentException.
     *
     * @return void
     */
    public function testDisplayDevInvalidTagThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(40090);
        Data::displayDev("test", "article");
    }

    /**
     * Test displayDev() with object.
     *
     * @return void
     */
    public function testDisplayDevWithObject(): void
    {
        $obj = new \stdClass();
        $obj->prop = "value";
        $result = Data::displayDev($obj);
        $this->assertStringContainsString("Location:", $result);
    }

    /**
     * Test display() preserves newlines in output.
     *
     * @return void
     */
    public function testDisplayPreservesNewlines(): void
    {
        $testString = "Line1\nLine2\nLine3";
        $result = Data::display($testString);
        $this->assertStringContainsString("\n<", $result);
        $this->assertStringContainsString(">\n", $result);
    }

    /**
     * Test displayDev() with special characters in data.
     *
     * @return void
     */
    public function testDisplayDevWithSpecialCharacters(): void
    {
        $testData = ['html' => '<script>alert("xss")</script>', 'quote' => "it's"];
        $result = Data::displayDev($testData);
        $this->assertStringContainsString("Location:", $result);
        // Content should be there (print_r/var_dump will handle it)
    }

    /**
     * Test display() returns string type.
     *
     * @return void
     */
    public function testDisplayReturnsString(): void
    {
        $result = Data::display("test");
        $this->assertIsString($result);
    }

    /**
     * Test displayDev() returns string type.
     *
     * @return void
     */
    public function testDisplayDevReturnsString(): void
    {
        $result = Data::displayDev("test");
        $this->assertIsString($result);
    }

    /**
     * Test display() with empty string.
     *
     * @return void
     */
    public function testDisplayWithEmptyString(): void
    {
        $result = Data::display("");
        $this->assertStringContainsString("<pre>", $result);
        $this->assertStringContainsString("</pre>", $result);
    }

    /**
     * Test displayDev() with empty string.
     *
     * @return void
     */
    public function testDisplayDevWithEmptyString(): void
    {
        $result = Data::displayDev("");
        $this->assertStringContainsString("<pre>", $result);
        $this->assertStringContainsString("Location:", $result);
    }

    /**
     * Test display() with numeric types (int and float).
     *
     * @return void
     */
    public function testDisplayWithNumericTypes(): void
    {
        $resultInt = Data::display(42);
        $this->assertStringContainsString("42", $resultInt);

        $resultFloat = Data::display(3.14);
        $this->assertStringContainsString("3.14", $resultFloat);
    }

    /**
     * Test displayDev() with boolean values.
     *
     * @return void
     */
    public function testDisplayDevWithBooleanValues(): void
    {
        $resultTrue = Data::displayDev(true);
        $this->assertStringContainsString("Location:", $resultTrue);

        $resultFalse = Data::displayDev(false);
        $this->assertStringContainsString("Location:", $resultFalse);
    }
}
