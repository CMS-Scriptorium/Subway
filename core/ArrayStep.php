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

    public function __construct(array &$givenValues, int $mode = self::MODE_LOOP)
    {
        $this->values = $givenValues;
        $this->max = count($givenValues) - 1;
        $this->mode = $mode;
    }

    public function get()
    {
        return $this->values[$this->place];
    }

    public function getAndStep()
    {
        $retVal = $this->values[$this->place];
        $this->next();
        return $retVal;
    }

    protected function next()
    {
        if ($this->mode == self::MODE_STILL)
        {
            return true;
        }

        $this->place += $this->direction;

        if ($this->place > $this->max)
        {
            switch ($this->mode)
            {
                case self::MODE_LOOP:
                    $this->place = ($this->direction > 0) ? 0 : $this->max;
                    break;

                case self::MODE_HOLD:
                    $this->place = ($this->direction > 0) ? $this->max : 0;
                    break;
            }
        }
    }
}
