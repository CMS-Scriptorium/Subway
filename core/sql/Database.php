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

use Exception;

/**
 * Keep in mind that we can only use static methods/properties here!
 *
 */
class Database
{

    public const string DO_UPDATE = "update";
    public const string DO_INSERT = "insert";
    
    /**
     * Singleton instance of the class.
     *
     * @var object
     */
    public static ?object $instance = null;

    public static function getInstance()
    {
        if (null === static::$instance)
        {
            // Using WBCE-database here!
            static::$instance = $GLOBALS['database'];
        }
        return static::$instance;
    }

    /**
     *  Public "shortcut" for executing a single mySql-query.
     *
     *  @param    string  $aQuery    A valid mySQL query.
     *  @param    bool    $bFetch    Fetching the result - default is false.
     *  @param    array   $aStorage  A storage array for the fetched results. Pass by reference!
     *  @param    bool    $bFetchAll Try to get all entries. Default is true.
     *
     *  @return   int     If success number of affected rows.
     *
     *  @example
     *      $results_array = [];
     *      Subway\core\sql\Database::execute_query(
     *          "SELECT * from ".TABLE_PREFIX."pages WHERE page_id = ".$page_id." ",
     *          true,
     *          $results_array,
     *          false
     *      );
     *
     */
    public static function executeQuery(string $aQuery="", bool $bFetch=false, array &$aStorage=[], bool $bFetchAll=true ): int
    {
        if (is_null(self::$instance))
        {
            self::getInstance();
        }

        self::handleTableprefix($aQuery);

        $oTempHandle = self::$instance->db_handle;
        
        try {
            $oStatement = $oTempHandle->prepare($aQuery);

            $oStatement->execute();

            $oResult = $oStatement->get_result();

            if (($oResult->num_rows > 0) && (true === $bFetch))
            {
                $aStorage = (true === $bFetchAll)
                    ? $oResult->fetch_all(MYSQLI_ASSOC)
                    : $oResult->fetch_assoc()
                    ;
            }

            return $oResult->num_rows;
        } catch(Exception $error) {
            trigger_error(sprintf('EXCEPTION: %s', mysqli_error($oTempHandle)));
            trigger_error(sprintf('STATEMENT: %s', preg_replace('/\s+/', ' ', $aQuery)));
            self::$instance->set_error(sprintf('EXCEPTION: %s', mysqli_error($oTempHandle)));
            return -1;
        }
    }
    
    /**
     *  Performs a simple query and returns the result as an assoc. array.
     *
     *  @param  string   A (simple)query.
     *  @return array    A two dimensional assoc. array with the results.
     */
    public static function query(string $query): array
    {
        $result = [];
        self::executeQuery(
            $query,
            true,
            $result
        );

        return $result;
    }

    public static function update(string $what, string $table, array $values, string $where = ""): bool
    {
        if (is_null(self::$instance))
        {
            self::getInstance();
        }

        switch (strtolower($what))
        {
            case self::DO_UPDATE:
                $query = "UPDATE `".$table."` SET ";
                foreach ($values as $field => $value)
                {
                    $query .= "`" . $field . "`= ?, ";
                }
                $query = substr($query, 0, -2) . (($where != "") ? " WHERE " . $where : "");
                break;

            case self::DO_INSERT:
                $keys = array_keys($values);
                $query = "INSERT into `" . $table . "` (`";
                $query .= implode("`,`", $keys) . "`) VALUES (";
                $query .= substr(str_repeat("?, ", count($values)), 0, -2).")";
                break;

            default:
                die("[2004] Not correct job in ".__CLASS__." in ".__LINE__.". Passed: ".$what);
                break;
        }
 
        self::handleTableprefix($query);

        $oTempHandle = self::$instance->db_handle;
        
        try {
            $oStatement = $oTempHandle->prepare($query);

            $oStatement->execute(array_values($values));

            return true;
        } catch(Exception $error) {
            trigger_error(sprintf('EXCEPTION: %s', mysqli_error($oTempHandle)));
            trigger_error(sprintf('STATEMENT: %s', preg_replace('/\s+/', ' ', $query)));
            self::$instance->set_error(sprintf('EXCEPTION: %s', mysqli_error($oTempHandle)));
            return false;
        }
    }

    public static function drop(string $table): bool
    {
        self::handleTableprefix($table);

        self::query("DROP table `".$table."` IF EXISTS;");

        return true;
    }

    public static function handleTableprefix(string &$source): void
    {
        $source = str_replace(
            ['{TP}', '{TABLE_PREFIX}'],
            TABLE_PREFIX,
            $source
        );
    }
    
    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        // Nothing here
    }
}
