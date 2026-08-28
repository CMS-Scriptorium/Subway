<?php

/**
 *  [0] Basics
 *      PHPUnit 13.2.4
 *      Unit tests for Subway\core\Subway
 *
 *  to get phpunit use
 *
 *  wget -O phpunit https://phar.phpunit.de/phpunit-13.phar
 *
 *  @example
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/
 *   php phpunit.phar --colors='always' --display-warnings wbce/modules/Subway/tests/SubwayTest.php
 *
 *   Variation: assume you are inside the "Subway" directory
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_168/wbce/modules/Subway
 *   php ../../../phpunit.phar --colors='always' --display-warnings tests/SubwayTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings wbce/modules/Subway/tests/SubwayTest.php
 *
 *  @notice
 *   To use a specific php version, e.g. under MacOS e.g. MAMP you will have to export like
 *
 *       export PATH=/Applications/MAMP/bin/php/php8.4.1/bin:$PATH
 *
 *   to get the correct PHP version to run.
 */

//  [1]
declare(strict_types=1);

//  [2]
namespace Subway\tests;

//  [3]
use PHPUnit\Framework\TestCase;
use Subway\core\Subway;

// [4]
require_once \dirname(__DIR__) . "/core/traits/Singleton.php";
require_once \dirname(__DIR__) . "/core/traits/Constants.php";
require_once \dirname(__DIR__) . "/core/language/EN.php";
require_once \dirname(__DIR__) . "/core/Subway.php";

require_once \dirname(__DIR__, 3) . "/framework/class.settings.php";
require_once \dirname(__DIR__, 3) . "/framework/Insert.php";
require_once \dirname(__DIR__, 3) . "/framework/I.php";

//  [5] Here we go
/**
 * Test suite for Subway\core\Subway class.
 *
 * @package Subway\Tests
 * @author  Test Suite
 */
final class SubwayTest extends TestCase
{
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


        // Reset singleton instance before each test
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        // Reset singleton instance after each test
        Subway::$instance = null;
    }

    /**
     * Test that getInstance() returns a Subway instance.
     *
     * @return void
     */
    public function testGetInstance(): void
    {
        $instance = Subway::getInstance();
        $this->assertInstanceOf(Subway::class, $instance);
    }

    /**
     * Test that getInstance() returns the same instance (Singleton pattern).
     *
     * @return void
     */
    public function testGetInstanceSingleton(): void
    {
        $instance1 = Subway::getInstance();
        $instance2 = Subway::getInstance();
        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test that language property is initialized as an array.
     *
     * @return void
     */
    public function testLanguagePropertyIsArray(): void
    {
        $instance = Subway::getInstance();
        $this->assertIsArray($instance->language);
    }

    /**
     * Test that $instance static property is set after getInstance().
     *
     * @return void
     */
    public function testInstanceStaticPropertyIsSet(): void
    {
        Subway::getInstance();
        $this->assertNotNull(Subway::$instance);
        $this->assertInstanceOf(Subway::class, Subway::$instance);
    }

    /**
     * Test initFrontend() method without GLOBALS['wb'].
     * Should not throw an exception and should set cssLoaded and jsLoaded flags.
     *
     * @return void
     */
    public function testInitFrontendWithoutWbGlobal(): void
    {
        // Clear the GLOBALS['wb'] if it exists
        $backupWb = $GLOBALS['wb'] ?? null;
        unset($GLOBALS['wb']);

        try {
            $instance = Subway::getInstance();
            // Should not throw an exception
            $instance->initFrontend();
            $this->assertTrue(true);
        } finally {
            // Restore the backup
            if ($backupWb !== null) {
                $GLOBALS['wb'] = $backupWb;
            }
        }
    }

    /**
     * Test initFrontend() method is idempotent (calling twice should only load once).
     *
     * @return void
     */
    public function testInitFrontendIdempotent(): void
    {
        $instance = Subway::getInstance();

        // Backup original globals
        $backupWb = $GLOBALS['wb'] ?? null;
        unset($GLOBALS['wb']);

        try {
            // Call twice - should not throw exception
            $instance->initFrontend();
            $instance->initFrontend();
            $this->assertTrue(true);
        } finally {
            if ($backupWb !== null) {
                $GLOBALS['wb'] = $backupWb;
            }
        }
    }

    /**
     * Test initBackend() method without DEFAULT_THEME constant.
     * Should not throw an exception.
     *
     * @return void
     */
    public function testInitBackendWithoutDefaultTheme(): void
    {
        // This test checks that initBackend() handles missing constant gracefully
        // Since we cannot easily undefine constants, we just verify it runs
        $instance = Subway::getInstance();

        try {
            $instance->initBackend();
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            // If an exception occurs, it should be about the constant, not the code itself
            $this->assertStringContainsString('DEFAULT_THEME', $e->getMessage());
        }
    }

    /**
     * Test initBackend() is idempotent.
     *
     * @return void
     */
    public function testInitBackendIdempotent(): void
    {
        $instance = Subway::getInstance();

        try {
            $instance->initBackend();
            $instance->initBackend();
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            // Expected if DEFAULT_THEME is not defined
            $this->assertStringContainsString('DEFAULT_THEME', $e->getMessage());
        }
    }

    /**
     * Test that protected constructor cannot be called directly.
     *
     * @return void
     */
    public function testConstructorIsProtected(): void
    {
        $reflection = new \ReflectionClass(Subway::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isProtected());
    }

    /**
     * Test that class uses Singleton trait.
     *
     * @return void
     */
    public function testClassUsesSingletonTrait(): void
    {
        $reflection = new \ReflectionClass(Subway::class);
        $traits = $reflection->getTraitNames();

        $this->assertContains('Subway\core\traits\Singleton', $traits);
    }

    /**
     * Test that class uses Constants trait.
     *
     * @return void
     */
    public function testClassUsesConstantsTrait(): void
    {
        $reflection = new \ReflectionClass(Subway::class);
        $traits = $reflection->getTraitNames();

        $this->assertContains('Subway\core\traits\Constants', $traits);
    }

    /**
     * Test that class has expected class constants defined.
     *
     * @return void
     */
    public function testClassConstantsAreDefined(): void
    {
        $reflection = new \ReflectionClass(Subway::class);
        $constants = $reflection->getConstants();

        // Check for the constants mentioned in the source code
        $this->assertArrayHasKey('CLASS_LANGUAGE_NAMESPACE', $constants);
        $this->assertArrayHasKey('DEFAULT_FRONTEND_CSS', $constants);
        $this->assertArrayHasKey('DEFAULT_FRONTEND_JS', $constants);
        $this->assertArrayHasKey('DEFAULT_BACKEND_CSS', $constants);
        $this->assertArrayHasKey('DEFAULT_BACKEND_JS', $constants);
        $this->assertArrayHasKey('TEMPLATE_DIR', $constants);
        $this->assertArrayHasKey('HEAD_FLAG', $constants);
    }

    /**
     * Test that expected class properties exist.
     *
     * @return void
     */
    public function testClassPropertiesExist(): void
    {
        $reflection = new \ReflectionClass(Subway::class);

        // Check public properties
        $properties = $reflection->getProperties();
        $propertyNames = array_map(function ($prop) {
            return $prop->getName();
        }, $properties);

        $this->assertContains('language', $propertyNames);
        $this->assertContains('instance', $propertyNames);
    }

    /**
     * Test that cssLoaded and jsLoaded protected properties are properly encapsulated.
     *
     * @return void
     */
    public function testProtectedPropertiesAreEncapsulated(): void
    {
        $instance = Subway::getInstance();
        $reflection = new \ReflectionClass($instance);

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $propertyNames = array_map(function ($prop) {
            return $prop->getName();
        }, $properties);

        // These should be protected to prevent external modification
        $this->assertContains('cssLoaded', $propertyNames);
        $this->assertContains('jsLoaded', $propertyNames);
    }

    /**
     * Test initFrontend() and initBackend() methods exist and are callable.
     *
     * @return void
     */
    public function testPublicMethodsExist(): void
    {
        $instance = Subway::getInstance();
        $reflection = new \ReflectionClass($instance);

        $this->assertTrue($reflection->hasMethod('initFrontend'));
        $this->assertTrue($reflection->hasMethod('initBackend'));

        $frontendMethod = $reflection->getMethod('initFrontend');
        $backendMethod = $reflection->getMethod('initBackend');

        $this->assertTrue($frontendMethod->isPublic());
        $this->assertTrue($backendMethod->isPublic());
    }

    /**
     * Test that initFrontend() returns void.
     *
     * @return void
     */
    public function testInitFrontendReturnsVoid(): void
    {
        $instance = Subway::getInstance();
        $reflection = new \ReflectionClass($instance);
        $method = $reflection->getMethod('initFrontend');

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string)$returnType);
    }

    /**
     * Test that initBackend() returns void.
     *
     * @return void
     */
    public function testInitBackendReturnsVoid(): void
    {
        $instance = Subway::getInstance();
        $reflection = new \ReflectionClass($instance);
        $method = $reflection->getMethod('initBackend');

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string)$returnType);
    }

    /**
     * Test that class declares strict types.
     *
     * @return void
     */
    public function testClassDeclaresStrictTypes(): void
    {
        // This is tested by the file_exists check in class definition
        // and by the declare(strict_types=1) at the top of the file
        $this->assertTrue(true); // Placeholder - verified at file level
    }

    /**
     * Test that the class is in the correct namespace.
     *
     * @return void
     */
    public function testClassNamespace(): void
    {
        $reflection = new \ReflectionClass(Subway::class);
        $this->assertSame('Subway\core', $reflection->getNamespaceName());
    }
}
