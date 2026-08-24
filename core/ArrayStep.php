<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.2.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x
 */

namespace Subway\core;

use InvalidArgumentException;
use Iterator;

/**
 * ArrayStep - Iterates through array elements with various stepping modes.
 * Supports LOOP, HOLD, TOGGLE, RANDOM, and STILL modes.
 */
class ArrayStep implements Iterator
{
    public const int MODE_STILL  = 0;
    public const int MODE_LOOP   = 1;
    public const int MODE_HOLD   = 2;
    public const int MODE_TOGGLE = 4;
    public const int MODE_RANDOM = 8;

    // Maximum attempts to find a different random index
    private const int MAX_RANDOM_ATTEMPTS = 100;

    // Valid mode bitmask for quick validation
    private const int VALID_MODES =
        self::MODE_STILL | self::MODE_LOOP | self::MODE_HOLD |
        self::MODE_TOGGLE | self::MODE_RANDOM;

    private int $place = 0;
    private int $max = 0;
    private array $values = [];
    private int $mode = self::MODE_LOOP;
    private int $direction = 1; // 1 = forward, -1 = backward
    // -1 means "not set" (no previous-last place recorded yet)
    private int $prevLastPlace = -1;
    private int $iteratorPosition = 0; // For Iterator interface

    /**
     * Constructor
     *
     * @param array $givenValues An indexed array with at least one element
     * @param int   $mode        Initial mode (default: MODE_LOOP)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $givenValues, int $mode = self::MODE_LOOP)
    {
        if (empty($givenValues))
        {
            throw new InvalidArgumentException(
                self::class . ' requires a non-empty array in constructor!'
            );
        }

        if (!$this->isValidMode($mode))
        {
            throw new InvalidArgumentException(
                "Unsupported mode: {$mode}. Valid modes: " .
                self::MODE_STILL . ', ' . self::MODE_LOOP . ', ' .
                self::MODE_HOLD . ', ' . self::MODE_TOGGLE . ', ' .
                self::MODE_RANDOM
            );
        }

        // Re-index the array to ensure numeric keys
        $this->values = array_values($givenValues);
        $this->max = count($this->values) - 1;
        $this->mode = $mode;

        // Single element always uses MODE_HOLD
        if ($this->max === 0)
        {
            $this->mode = self::MODE_HOLD;
        }

        // Initialize random position if in RANDOM mode
        if ($this->mode === self::MODE_RANDOM)
        {
            $this->place = random_int(0, $this->max);
        }
    }

    /**
     * Get current value without advancing
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->values[$this->place];
    }

    /**
     * Advance and return new value
     *
     * @return mixed
     */
    public function step(): mixed
    {
        $this->advance();
        return $this->values[$this->place];
    }

    /**
     * Get current value, then advance
     *
     * @return mixed
     */
    public function getAndStep(): mixed
    {
        $retVal = $this->values[$this->place];
        $this->advance();
        return $retVal;
    }

    /**
     * Set iteration direction
     *
     * @param int $newDirection Direction: 1 (forward) or -1 (backward)
     *
     * @throws InvalidArgumentException
     */
    public function setDirection(int $newDirection): void
    {
        if ($newDirection !== 1 && $newDirection !== -1)
        {
            throw new InvalidArgumentException(
                "Direction must be 1 (forward) or -1 (backward), got: {$newDirection}"
            );
        }
        $this->direction = $newDirection;
    }

    /**
     * Change stepping mode
     *
     * @param int $newMode New mode
     *
     * @return bool True if mode changed, false if invalid
     */
    public function setMode(int $newMode): bool
    {
        if (!$this->isValidMode($newMode))
        {
            return false;
        }

        $this->mode = $newMode;
        return true;
    }

    /**
     * Reset position to start
     *
     * @return void
     */
    public function reset(): void
    {
        $this->place = 0;
        $this->prevLastPlace = -1;
        if ($this->mode === self::MODE_RANDOM)
        {
            $this->place = random_int(0, $this->max);
        }
    }

    /**
     * Get current position index
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->place;
    }

    /**
     * Set position to specific index
     *
     * @param int $index Position index (0 to count-1)
     *
     * @throws InvalidArgumentException
     */
    public function setPosition(int $index): void
    {
        if ($index < 0 || $index > $this->max)
        {
            throw new InvalidArgumentException(
                "Position must be between 0 and {$this->max}, got: {$index}"
            );
        }
        $this->place = $index;
    }

    /**
     * Get total number of elements
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->values);
    }

    /**
     * ========== ITERATOR INTERFACE IMPLEMENTATION ==========
     * Allows usage in foreach loops
     */

    public function current(): mixed
    {
        return $this->values[$this->iteratorPosition] ?? null;
    }

    public function key(): int
    {
        return $this->iteratorPosition;
    }

    public function next(): void
    {
        ++$this->iteratorPosition;
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    public function valid(): bool
    {
        return isset($this->values[$this->iteratorPosition]);
    }

    /**
     * ========== PRIVATE/PROTECTED METHODS ==========
     */

    /**
     * Advance position based on mode and direction
     *
     * @return void
     */
    private function advance(): void
    {
        if ($this->mode === self::MODE_STILL)
        {
            return;
        }

        if ($this->mode === self::MODE_RANDOM)
        {
            $this->advanceRandom();
            return;
        }

        $this->place += $this->direction;

        // Handle boundary conditions
        if ($this->place > $this->max || $this->place < 0)
        {
            $this->handleBoundary();
        }
    }

    /**
     * Advance to next random index (with safeguards)
     *
     * @return void
     */
    private function advanceRandom(): void
    {
        if ($this->max < 2)
        {
            // Single element or two elements: just pick any
            $this->place = random_int(0, $this->max);
            return;
        }

        $oldPlace = $this->place;
        $attempts = 0;

        // Try to find a different position (avoid repeats)
        do {
            $this->place = random_int(0, $this->max);
            $attempts++;
        } while (
            $attempts < self::MAX_RANDOM_ATTEMPTS &&
            (
                $this->place === $oldPlace
                || ($this->prevLastPlace !== -1 && $this->place === $this->prevLastPlace)
            )
        );

        $this->prevLastPlace = $oldPlace;
    }

    /**
     * Handle position boundary crossing
     *
     * @return void
     */
    private function handleBoundary(): void
    {
        match ($this->mode) {
            self::MODE_LOOP => $this->place = ($this->direction > 0) ? 0 : $this->max,
            self::MODE_HOLD => $this->place = ($this->direction > 0) ? $this->max : 0,
            self::MODE_TOGGLE => $this->handleToggle(),
            default => $this->place = 0,
        };
    }

    /**
     * Handle TOGGLE mode: bounce direction and continue
     *
     * @return void
     */
    private function handleToggle(): void
    {
        $this->place = ($this->direction > 0) ? $this->max : 0;
        $this->direction *= -1;
        $this->advance(); // Recursively advance in new direction
    }

    /**
     * Validate if mode is supported (using bitwise check)
     *
     * @param int $mode Mode to validate
     *
     * @return bool
     */
    private function isValidMode(int $mode): bool
    {
        // Only allow exactly one of the predefined mode constants.
        // This prevents combined bitmasks (e.g. MODE_LOOP | MODE_TOGGLE) from being considered valid.
        $validModes = [
            self::MODE_STILL,
            self::MODE_LOOP,
            self::MODE_HOLD,
            self::MODE_TOGGLE,
            self::MODE_RANDOM
        ];
        return in_array($mode, $validModes, true);
    }
}
