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

/**
 * Keep in mind that we can only use static methods/properties here!
 * 
 */
class Database
{

    /**
     * Singleton instance of the class.
     * 
     * @var object
     */
    public static $instance;

    /**
     * Internal shortcut to the MySqli-handle.
     * 
     * @var object|null
     */
    protected static ?object $mysqli = NULL;

    public static function getInstance()
    {
        if (null === static::$instance)
        {
            // Using WBCE-database here!
            static::$instance = $GLOBALS['database'];
            static::$mysqli = static::$instance->db_handle;
        }
        return static::$instance;
    }

    /**
     *  Public "shortcut" for executing a single mySql-query without passing values.
     *
     *
     *  @param    string  $aQuery A valid mySQL query.
     *  @param    bool    $bFetch Fetching the result - default is false.
     *  @param    array   $aStorage A storage array for the fetched results. Pass by reference!
     *  @param    bool    $bFetchAll Try to get all entries. Default is true.
     *  @return   int 	  If success number of affected rows.
     *
     *  @example
     *      $results_array = [];       
     *      Database::execute_query( 
     *          "SELECT * from ".TABLE_PREFIX."pages WHERE page_id = ".$page_id." ",
     *          true, 
     *          $results_array, 
     *          false 
     *      );
     *        
     *
     */
    public static function execute_query(string $aQuery="", bool $bFetch=false, array &$aStorage=[], bool $bFetchAll=true ) : int
    {
        // $this->error = "";
        try{
            $oStatement = self::getInstance()->mysqli->prepare($aQuery);

            $oStatement->execute();
            
            $oResult = $oStatement->get_result();
            
            if (($oResult->num_rows > 0) && (true === $bFetch))
            {
                $aStorage = (true === $bFetchAll)
                    ? $oResult->fetch_all(MYSQLI_ASSOC)
                    : $oResult->fetch(MYSQLI_ASSOC)
                    ;
            
            }
            return $oResult->num_rows;
        } catch(\mysqli_sql_exception $error) {
            die("E: " . $error->getMessage() );
            // $this->error = $error->getMessage();
            // $this->HandleDisplayError("10");
            return -1;
        }
    }

    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        
    }
}
