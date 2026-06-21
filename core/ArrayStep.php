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
    const int MODE_STILL = 0;
    const int MODE_LOOP = 1;
    const int MODE_HOLD = 2;
    const int MODE_TOGGLE = 4;

    protected int $place = 0;
    protected int $max = 0;
    protected array $values = [];
    protected int $mode = 1;
    protected int $direction = 1; // 1 == up, -1 == down

    /**
     * Constructor of the class.
     *
     * @param array $givenValues    An indexed array.
     * @param int   $mode           Optional an initial mode.
     */
    public function __construct(array &$givenValues, int $mode = self::MODE_LOOP)
    {
        $this->values = $givenValues;
        $this->max = count($givenValues) - 1;
        $this->mode = $mode;
    }

    /**
     * Get the current value without stepping forward or backward..
     *
     * @return mixed
     */
    public function get()
    {
        return $this->values[$this->place];
    }

    /**
     * Step next and returns the value.
     *
     * @return mixed
     */
    public function step()
    {
        $this->next();
        return $this->values[$this->place];
    }

    /**
     * Get the current value and step forward (belongs to direction)
     *
     * @return mixed
     */
    public function getAndStep()
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
    public function setDirection(int $newDirection)
    {
        $this->direction = $newDirection;
    }

    /**
     * Next step. Belongs to the mode and direction.
     *
     * @return bool
     */
    protected function next()
    {
        if ($this->mode == self::MODE_STILL)
        {
            return true;
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
                    break;

                default:
                    // At this time it is not clear to handle this situation!
                    $this->place = 0;
                    break;
            }
        }
        return true;
    }
}
