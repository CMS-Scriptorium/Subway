<?php

/**
 *  [0] Basics
 *      PHPUnit 13.0.5
 *
 *  to get phpunit use
 *
 *  wget -O phpunit https://phar.phpunit.de/phpunit-13.phar
 *
 *  @example
 *
 *   cd /Applications/MAMP/htdocs/projekte/wbce_git/wbce/modules/Subway/tests
 *   php phpunit.phar --colors='always' --display-warnings RequestTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings RequestTest.php
 *
 *   phpcs --colors --standard=PSR12 RequestTest.php
 *   phpcbf --standard=PSR12 RequestTest.php
 *
 *   php phpstan.phar analyse  /Applications/MAMP/htdocs/projekte/LEPTON_VII/tests/LeptonBasicsTest.php
 *
 *  @notice
 *   To use a spezific php version, e.g. under MacOS e.g. MAMP you will have to export like
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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Subway\core\Request;

// [4]
require_once \dirname(__DIR__) . "/core/traits/Singleton.php";
require_once \dirname(__DIR__) . "/core/traits/RequestNumbers.php";
require_once \dirname(__DIR__) . "/core/traits/RequestStrings.php";
require_once \dirname(__DIR__) . "/core/Request.php";

//  [5] Here we go
class RequestTest extends TestCase
{
    protected $oREQUEST = null;

    public function setUp(): void
    {
        $this->oREQUEST = Request::getInstance();
    }

    public function setUpTestValues(string $name, array $values): void
    {
        switch (strToLower($values['where'])) {
            case 'post':
                $_POST[$name] = $values['value'];
                break;

            case 'get':
                $_GET[$name] = $values['value'];
                break;

            case 'session':
                $_SESSION[$name] = $values['value'];
                break;

            default:
                break;
        }
    }

    #[DataProvider('getValueTestData')]
    public function testGetValue(
        array $setup,
        string $where,
        string $name,
        string $type,
        mixed $default,
        array $options,
        mixed $expected
    ): void {
        if (!empty($setup)) {
            $this->setUpTestValues($name, $setup);
        }

        $actual = $this->oREQUEST->getValue(
            $where,
            $name,
            $type,
            $default,
            $options
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test data for the test above
     * @return array
     */
    public static function getValueTestData(): array
    {
        return [
            'first run' => [
                'setup' => [
                    'where' => "post",
                    'value' => 12345
                ],
                'where' => Request::USE_POST,
                'name' => 'testvalue',
                'type' => 'integer',
                'default' => 60,
                'options' => [
                    'min' => 50,
                    'max' => 90,
                    'default' => 50
                ],
                'expected' => 50
            ],
            'simple str' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "Aladin his wonderlamp"
                ],
                'where' => Request::USE_POST,
                'name' => "test_string",
                'type' => 's',
                'default' => "Aladin",
                'options' => [
                    'max' => 10
                ],
                'expected' => 'Aladin his'
            ],
            'str min' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "Aladin his wonderlamp."
                ],
                'where' => Request::USE_POST,
                'name' => "test_string",
                'type' => 's',
                'default' => "Aladin",
                'options' => [
                    'min' => 32,
                    'fill' => "_",
                    'prepend' => true   // !
                ],
                'expected' => '__________Aladin his wonderlamp.'
            ],
            'regExpr' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "Aladin his wonderlamp."
                ],
                'where' => Request::USE_POST,
                'name' => "test_string",
                'type' => 'regexpr',
                'default' => "",
                'options' => [
                    'pattern' => "~^[a-z\. ]{1,}$~iU",
                    'default' => "*"
                ],
                'expected' => "Aladin his wonderlamp."
            ],
            'strip tags' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "<p>Aladin <em>his</em> wonderlamp.</p>"
                ],
                'where' => Request::USE_POST,
                'name' => "test_strip",
                'type' => 'strip',
                'default' => "",
                'options' => [
                    'allowed' => "<p><a><i>"
                ],
                'expected' => "<p>Aladin his wonderlamp.</p>"
            ],
            'e-mail ok' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "aladin.gibtesnicht@none.tld"
                ],
                'where' => Request::USE_POST,
                'name' => "test_mail",
                'type' => 'email',
                'default' => "",
                'options' => [
                    'default' => "no valid"
                ],
                'expected' => "aladin.gibtesnicht@none.tld"
            ],
            'string' => [
                'setup' => [
                    'where' => Request::USE_POST,
                    'value' => "<p>ein <em>einfacher</em> Text.</p>"
                ],
                'where' => Request::USE_POST,
                'name' => "test_str",
                'type' => 's',
                'default' => "",
                'options' => [
                    'min' => 1,
                    'max' => 128
                ],
                'expected' => "<p>ein <em>einfacher</em> Text.</p>"
            ]
        ];
    }
}
