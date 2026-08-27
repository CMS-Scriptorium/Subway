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
 *   php phpunit.phar --colors='always' --display-warnings wbce/modules/Subway/tests/DatabaseTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings wbce/modules/Subway/tests/DatabaseTest.php
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

use PHPUnit\Framework\TestCase;
use Subway\core\sql\Database;
use InvalidArgumentException;

//  [3]
use PHPUnit\Framework\Attributes\DataProvider;

// [4]
// Ensure the class file is loaded (or rely on Composer autoload if available)
require_once __DIR__ . '/../core/sql/Database.php';

//  [5] Here we go
/**
 * Test suite for Subway\core\sql\Database class.
 *
 * @package Subway\Tests
 * @author  Test Suite
 */
class DatabaseTest extends TestCase
{
    /**
     * Mock database connection object.
     *
     * @var object
     */
    private $mockDatabase;

    /**
     * Mock database handle (mysqli).
     *
     * @var object
     */
    private $mockHandle;

    /**
     * Mock prepared statement.
     *
     * @var object
     */
    private $mockStatement;

    /**
     * Mock result object.
     *
     * @var object
     */
    private $mockResult;

    /**
     * Set up test fixtures before each test method.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects
        $this->mockResult = $this->createMock(\stdClass::class);
        $this->mockStatement = $this->createMock(\stdClass::class);
        $this->mockHandle = $this->createMock(\stdClass::class);
        $this->mockDatabase = $this->createMock(\stdClass::class);

        // Set up mock behavior
        $this->mockHandle->prepare = $this->mockStatement;
        $this->mockStatement->get_result = $this->mockResult;
        $this->mockDatabase->db_handle = $this->mockHandle;

        // Set the global database instance
        Database::$instance = $this->mockDatabase;

        // Mock table prefix constant if not defined
        if (!defined('TABLE_PREFIX')) {
            define('TABLE_PREFIX', 'test_');
        }
    }

    /**
     * Clean up after each test method.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Database::$instance = null;
        parent::tearDown();
    }

    /**
     * Test getInstance() returns the singleton instance.
     *
     * @return void
     */
    public function testGetInstance(): void
    {
        $instance = Database::getInstance();
        $this->assertNotNull($instance);
        $this->assertSame($this->mockDatabase, $instance);
    }

    /**
     * Test getInstance() initializes from global $database if instance is null.
     *
     * @return void
     */
    public function testGetInstanceInitializesFromGlobal(): void
    {
        Database::$instance = null;
        $GLOBALS['database'] = $this->mockDatabase;

        $instance = Database::getInstance();
        
        $this->assertSame($this->mockDatabase, $instance);
        $this->assertSame($this->mockDatabase, Database::$instance);
    }

    /**
     * Test handleTableprefix() replaces {TP} with TABLE_PREFIX.
     *
     * @return void
     */
    public function testHandleTableprefix(): void
    {
        $tableName = '{TP}users';
        Database::handleTableprefix($tableName);
        $this->assertSame('test_users', $tableName);
    }

    /**
     * Test handleTableprefix() replaces {TABLE_PREFIX} with TABLE_PREFIX.
     *
     * @return void
     */
    public function testHandleTablePrefixAlternative(): void
    {
        $tableName = '{TABLE_PREFIX}posts';
        Database::handleTableprefix($tableName);
        $this->assertSame('test_posts', $tableName);
    }

    /**
     * Test testTablename() accepts valid table names.
     *
     * @return void
     */
    public function testTestTablenameValid(): void
    {
        $this->assertTrue(Database::testTablename('users'));
        $this->assertTrue(Database::testTablename('test_users'));
        $this->assertTrue(Database::testTablename('test123'));
        $this->assertTrue(Database::testTablename('_table'));
    }

    /**
     * Test testTablename() rejects invalid table names.
     *
     * @return void
     */
    #[DataProvider('invalidTableNameProvider')]
    public function testTestTablenameInvalid($tableName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(40067);
        Database::testTablename($tableName);
    }

    /**
     * Data provider for invalid table name tests.
     *
     * @return array
     */
    public static function invalidTableNameProvider(): array
    {
        return [
            ['users;'],
            ['users OR 1=1'],
            ["users'; DROP TABLE users;--"],
            ['users`'],
            ['users-name'],
            ['users.table'],
            ['users table'],
            [''],
        ];
    }

    /**
     * Test prepareFields() returns all fields with asterisk.
     *
     * @return void
     */
    public function testPrepareFieldsEmpty(): void
    {
        $result = Database::prepareFields([]);
        $this->assertSame('*', $result);
    }

    /**
     * Test prepareFields() returns properly formatted field list.
     *
     * @return void
     */
    public function testPrepareFieldsWithFields(): void
    {
        $fields = ['id', 'name', 'email'];
        $result = Database::prepareFields($fields);
        $this->assertSame('`id`, `name`, `email`', $result);
    }

    /**
     * Test prepareFields() with prefix.
     *
     * @return void
     */
    public function testPrepareFieldsWithPrefix(): void
    {
        $fields = ['id', 'name'];
        $result = Database::prepareFields($fields, 'u');
        $this->assertSame('u.`id`, u.`name`', $result);
    }

    /**
     * Test prepareFields() with empty prefix returns asterisk with prefix.
     *
     * @return void
     */
    public function testPrepareFieldsEmptyWithPrefix(): void
    {
        $result = Database::prepareFields([], 'users');
        $this->assertSame('users.*', $result);
    }

    /**
     * Test executeQuery() returns -1 on exception.
     *
     * @return void
     */
    public function testExecuteQueryException(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willThrowException(new \Exception('Database error'));

        $result = Database::executeQuery('SELECT * FROM test_users');
        $this->assertSame(-1, $result);
    }

    /**
     * Test executeQuery() returns -1 when result is false.
     *
     * @return void
     */
    public function testExecuteQueryNoResult(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $this->mockStatement
            ->method('get_result')
            ->willReturn(false);

        $result = Database::executeQuery('SELECT * FROM test_users');
        $this->assertSame(-1, $result);
    }

    /**
     * Test executeQuery() with fetch=false returns row count.
     *
     * @return void
     */
    public function testExecuteQueryNoFetch(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $this->mockResult->num_rows = 5;
        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $result = Database::executeQuery('SELECT * FROM test_users', false);
        $this->assertSame(5, $result);
    }

    /**
     * Test executeQuery() with fetch=true and bFetchAll=true.
     *
     * @return void
     */
    public function testExecuteQueryFetchAll(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $mockData = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];

        $this->mockResult->num_rows = 2;
        $this->mockResult
            ->method('fetch_all')
            ->with(\MYSQLI_ASSOC)
            ->willReturn($mockData);

        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $storage = [];
        $result = Database::executeQuery('SELECT * FROM test_users', true, $storage, true);
        
        $this->assertSame(2, $result);
        $this->assertSame($mockData, $storage);
    }

    /**
     * Test executeQuery() with fetch=true and bFetchAll=false.
     *
     * @return void
     */
    public function testExecuteQueryFetchOne(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $mockData = ['id' => 1, 'name' => 'John'];

        $this->mockResult->num_rows = 1;
        $this->mockResult
            ->method('fetch_assoc')
            ->willReturn($mockData);

        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $storage = [];
        $result = Database::executeQuery('SELECT * FROM test_users', true, $storage, false);
        
        $this->assertSame(1, $result);
        $this->assertSame($mockData, $storage);
    }

    /**
     * Test query() calls executeQuery and returns results.
     *
     * @return void
     */
    public function testQuery(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $mockData = [['id' => 1, 'name' => 'Test']];
        $this->mockResult->num_rows = 1;
        $this->mockResult
            ->method('fetch_all')
            ->willReturn($mockData);

        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $result = Database::query('SELECT * FROM test_users');
        $this->assertSame($mockData, $result);
    }

    /**
     * Test update() with DO_UPDATE operation.
     *
     * @return void
     */
    public function testUpdateDoUpdate(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $values = ['name' => 'Jane', 'email' => 'jane@example.com'];
        $result = Database::update(Database::DO_UPDATE, 'users', $values, 'id = 1');
        
        $this->assertTrue($result);
    }

    /**
     * Test update() with DO_INSERT operation.
     *
     * @return void
     */
    public function testUpdateDoInsert(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $values = ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'];
        $result = Database::update(Database::DO_INSERT, 'users', $values);
        
        $this->assertTrue($result);
    }

    /**
     * Test update() returns false on exception.
     *
     * @return void
     */
    public function testUpdateException(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willThrowException(new \Exception('Database error'));

        $values = ['name' => 'Jane'];
        $result = Database::update(Database::DO_UPDATE, 'users', $values);
        
        $this->assertFalse($result);
    }

    /**
     * Test update() with invalid operation terminates.
     *
     * @return void
     */
    public function testUpdateInvalidOperation(): void
    {
        $this->expectExceptionCode(40067);
        $values = ['name' => 'Jane'];
        Database::update('INVALID', 'users', $values);
    }

    /**
     * Test drop() calls query and returns true.
     *
     * @return void
     */
    public function testDrop(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $this->mockResult->num_rows = 0;
        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $result = Database::drop('{TP}users');
        $this->assertTrue($result);
    }

    /**
     * Test drop() with invalid table name throws exception.
     *
     * @return void
     */
    public function testDropInvalidTableName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(40067);
        
        Database::drop("users'; DROP TABLE users;--");
    }

    /**
     * Test handleJobs() executes multiple queries.
     *
     * @return void
     */
    public function testHandleJobs(): void
    {
        $this->mockHandle
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->method('execute')
            ->willReturn(true);

        $this->mockResult->num_rows = 0;
        $this->mockStatement
            ->method('get_result')
            ->willReturn($this->mockResult);

        $jobs = [
            'CREATE TABLE test_users (id INT)',
            'INSERT INTO test_users VALUES (1)',
        ];

        Database::handleJobs($jobs);
        
        $this->assertTrue(true); // No exception thrown
    }

    /**
     * Test that Database constructor is protected.
     *
     * @return void
     */
    public function testConstructorIsProtected(): void
    {
        $reflection = new \ReflectionClass(Database::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertTrue($constructor->isProtected());
    }

    /**
     * Test constants are properly defined.
     *
     * @return void
     */
    public function testConstants(): void
    {
        $this->assertSame('update', Database::DO_UPDATE);
        $this->assertSame('insert', Database::DO_INSERT);
    }
}
