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

namespace Subway\core;

/**
 * That is truly experimental.
 */
class ArrayStep
{
    public const int MODE_STILL  = 0;
    public const int MODE_LOOP   = 1;
    public const int MODE_HOLD   = 2;
    public const int MODE_TOGGLE = 4;
    public const int MODE_RANDOM = 8;

    protected int $place = 0;
    protected int $max = 0;
    protected array $values = [];
    protected int $mode = self::MODE_LOOP;
    protected int $direction = 1; // 1 == up, -1 == down

    protected int $prevLastPlace = 0;

    /**
     * Constructor of the class.
     *
     * @param array $givenValues    An indexed array with one element as a minimum!
     * @param int   $mode           Optional an initial mode.
     */
    public function __construct(array $givenValues, int $mode = self::MODE_LOOP)
    {
        if (empty($givenValues))
        {
            throw new \InvalidArgumentException(__CLASS__ . ' requires a non-empty array in constructor!');
        }
        
        if ($this->testMode($mode))
        {
            $this->mode = $mode;
        }
        
        $this->values = $givenValues;
        $this->max = count($givenValues) -1;
        if ($this->max == 0)
        {
            $this->mode = self::MODE_HOLD;
        }

        if ($this->mode === self::MODE_RANDOM)
        {
            $this->place = random_int(0, $this->max);
        }
    }

    /**
     * Get the current value without stepping forward or backward..
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->values[$this->place];
    }

    /**
     * Step next and returns the value.
     *
     * @return mixed
     */
    public function step(): mixed
    {
        $this->next();
        return $this->values[$this->place];
    }

    /**
     * Get the current value and step forward (belongs to direction)
     *
     * @return mixed
     */
    public function getAndStep(): mixed
    {
        $retVal = $this->values[$this->place];
        $this->next();
        return $retVal;
    }

    /**
     * Set the direction. Normaly 1 (forwards) or -1 (backwards).
     *
     * @param int $newDirection
     */
    public function setDirection(int $newDirection): void
    {
        $this->direction = $newDirection;
    }

    /**
     * Setting a new mode, e.g. MODE_HOLD or MODE_TOGGLE.
     *
     * @param  int  $newMode    The new mode as integer.
     * @return bool             True if success, otherwise false;
     */
    public function setMode(int $newMode): bool
    {
        if ($this->testMode($newMode))
        {
            $this->mode = $newMode;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Next step. Belongs to the mode and direction.
     *
     * @return bool
     */
    protected function next(): bool
    {
        if ($this->mode === self::MODE_STILL)
        {
            return true;
        }

        if ($this->mode === self::MODE_RANDOM)
        {
            return $this->getRandomPlace();
        }

        $this->place += $this->direction;

        if (($this->place > $this->max) || ($this->place < 0))
        {
            switch ($this->mode)
            {
                case self::MODE_LOOP:
                    $this->place = ($this->direction > 0) ? 0 : $this->max;
                    break;

                case self::MODE_HOLD:
                    $this->place = ($this->direction > 0) ? $this->max : 0;
                    break;

                case self::MODE_TOGGLE:
                    $this->place = ($this->direction > 0) ? $this->max : 0;
                    $this->direction *= -1;
                    $this->next();
                    break;

                default:
                    // At this time it is not clear to handle this situation!
                    $this->place = 0;
                    break;
            }
        }
        return true;
    }
    
    /**
     * Internal testing the given mode agains the supported ones.
     *
     * @param   int $mode   A given mode as integer.
     * @return  bool        True if the $mode is still supported, false if not.
     */
    protected function testMode(int $mode): bool
    {
        $internalModes = [
            self::MODE_STILL,
            self::MODE_LOOP,
            self::MODE_HOLD,
            self::MODE_TOGGLE,
            self::MODE_RANDOM
        ];
        
        if (in_array($mode, $internalModes))
        {
            return true;
        } else {
            echo "<p>" . __CLASS__ . ":: mode not supported (constructor)!</p>";
            return false;
        }
    }

    /**
     * Looks for the nex random-place in the given array.
     * @return bool At this time always true;
     */
    protected function getRandomPlace(): bool
    {
        if ($this->max < 2)
        {
            $this->place = random_int(0, $this->max);
        } else {
            $oldPlace = $this->place;
            do {
                $this->place = random_int(0, $this->max);
            } while (($this->place == $oldPlace) || ($this->place == $this->prevLastPlace));
            $this->prevLastPlace = $oldPlace;
        }
        return true;
    }
}
