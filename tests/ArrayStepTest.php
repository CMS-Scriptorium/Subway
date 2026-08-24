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
 *   php phpunit.phar --colors='always' --display-warnings wbce/modules/Subway/tests/ArrayStepTest.php
 *
 *   php phpunit.phar --colors='always' --display-deprecations --display-warnings wbce/modules/Subway/tests/ArrayStepTest.php
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
use Subway\core\ArrayStep;
use InvalidArgumentException;

// [4]
// Ensure the class file is loaded (or rely on Composer autoload if available)
require_once __DIR__ . '/../core/ArrayStep.php';

//  [5] Here we go
final class ArrayStepTest extends TestCase
{
    
    public function testConstructorEmptyArrayThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $oTemp = new ArrayStep([]);
        $oTemp->get(); // do something
    }

    public function testConstructorInvalidModeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $oTemp = new ArrayStep([1,2,3], 16); // 16 is not a supported mode
        $oTemp->step(); // do something
    }

    public function testGetStepGetAndStepLoopMode(): void
    {
        $as = new ArrayStep([10, 20, 30]); // default MODE_LOOP
        $this->assertSame(10, $as->get());
        $this->assertSame(20, $as->step());       // advance to index 1
        $this->assertSame(20, $as->get());        // current is index 1
        $this->assertSame(20, $as->getAndStep()); // return current (20), then advance to 30
        $this->assertSame(30, $as->get());
    }

    public function testSetDirectionInvalidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $as = new ArrayStep([1,2]);
        $as->setDirection(0);
    }

    public function testSetModeInvalidReturnsFalse(): void
    {
        $as = new ArrayStep([1,2,3]);
        $this->assertFalse($as->setMode(16));
        $this->assertTrue($as->setMode(ArrayStep::MODE_TOGGLE)); // valid
    }
    /**
        // Tricky! Random is difficult
    public function testResetRandomModeUsesMockedRandom(): void
    {
        $as = new ArrayStep([0,1,2], ArrayStep::MODE_RANDOM);
        // Our mocked random_int returns 1 for constructor/reset, so place should be 1
        $this->assertSame(1, $as->getPosition());
        // call reset which uses random_int again (mocked)
        $as->reset();
        $this->assertSame(1, $as->getPosition());
    }
    **/
    public function testToggleModeBouncesDirection(): void
    {
        $as = new ArrayStep([100, 200, 300]);
        $as->setMode(ArrayStep::MODE_TOGGLE);

        // Starting place = 0
        $this->assertSame(200, $as->step()); // place -> 1
        $this->assertSame(300, $as->step()); // place -> 2
        // Next step should toggle and come back to index 1
        $this->assertSame(200, $as->step()); // place -> 1
        $this->assertSame(100, $as->step()); // place -> 0
        // Toggle again when stepping backwards past 0
        $this->assertSame(200, $as->step()); // place -> 1
    }

    public function testHoldModeSticksAtEnd(): void
    {
        $as = new ArrayStep([9, 8, 7]);
        $as->setMode(ArrayStep::MODE_HOLD);

        // move to last element
        $this->assertSame(8, $as->step()); // index 1
        $this->assertSame(7, $as->step()); // index 2
        // further steps must keep last element (hold)
        $this->assertSame(7, $as->step());
        $this->assertSame(7, $as->step());
    }

    public function testStillModeNeverAdvances(): void
    {
        $as = new ArrayStep(['a', 'b', 'c']);
        $as->setMode(ArrayStep::MODE_STILL);

        $this->assertSame('a', $as->get());
        $this->assertSame('a', $as->step());
        $this->assertSame('a', $as->getAndStep());
        $this->assertSame('a', $as->get());
    }

    public function testSetGetPositionAndInvalidSetPositionThrows(): void
    {
        $as = new ArrayStep([0, 1, 2, 3]);
        $as->setPosition(2);
        $this->assertSame(2, $as->getPosition());
        $this->expectException(InvalidArgumentException::class);
        $as->setPosition(99);
    }

    public function testCountAndIteratorBehavior(): void
    {
        $values = ['x', 'y', 'z'];
        $as = new ArrayStep($values);

        $this->assertSame(3, $as->count());

        // Iterator interface: foreach should iterate through original values in order
        $collected = [];
        foreach ($as as $k => $v) {
            $collected[$k] = $v;
        }
        $this->assertSame($values, array_values($collected));
    }
}
