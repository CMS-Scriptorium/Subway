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

namespace Subway\core\sql;

use Subway\core\traits\Singleton;
use Subway\core\traits\QueryTools;

use const TABLE_PREFIX;

class Table
{
    use Singleton;
    use QueryTools;

    public static $instance;
    
    protected ?object $database = null;
    
    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        $this->database = $GLOBALS['database'];
        
        // Method is inside QueryTools
        $this->getMysqlHandle();
    }
    
    /**
     *
     * @return bool
     */
    public function install(): bool
    {
    
        return true;
    }
    
    /**
     *
     * @return bool
     */
    public function uninstall(): bool
    {

        return true;
    }

    /**
     *
     * @return bool
     */
    public function update(): bool
    {

        return true;
    }
}
