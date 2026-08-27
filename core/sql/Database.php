<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.1
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace Subway\core\sql;

use Exception;
use InvalidArgumentException;
use const TABLE_PREFIX;

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

    protected const string STR_EXCEPTION = "EXCEPTION: %s";
    protected const string STR_STATEMENT = "STATEMENT: %s";
    
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
     *          "SELECT * from `{TP}pages` WHERE page_id = " . $page_id . " ",
     *          true,
     *          $results_array,
     *          false
     *      );
     *
     */
    public static function executeQuery(
        string  $aQuery     = "",
        bool    $bFetch     = false,
        array   &$aStorage  = [],
        bool    $bFetchAll  = true
    ): int
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

            if (!$oResult)
            {
                return -1;
            }
            
            if (($oResult->num_rows > 0) && (true === $bFetch))
            {
                $aStorage = (true === $bFetchAll)
                    ? $oResult->fetch_all(MYSQLI_ASSOC)
                    : $oResult->fetch_assoc()
                    ;
            }

            return $oResult->num_rows;
        } catch(Exception $error) {
            trigger_error(sprintf(self::STR_EXCEPTION . " [1]", $error->getMessage()));
            trigger_error(sprintf(self::STR_EXCEPTION, mysqli_error($oTempHandle)));
            trigger_error(sprintf(self::STR_STATEMENT, preg_replace('/\s+/', ' ', $aQuery)));
            self::$instance->set_error(sprintf(self::STR_EXCEPTION, mysqli_error($oTempHandle)));
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

    /**
     * Update database entry with an given statement.
     *
     * @param string $what      Only accepted self::DO_UPDATE or self::DO_INSERT.
     * @param string $table     Any valid tablename (with {TP} or {TABLENAME})
     * @param array  $values    Assoc. array within the values.
     * @param string $where     Opt. a WHERE as a string! (Ignored by INSERT)
     * @return bool             True on success, false on faild.
     *
     * @throws InvalidArgumentException
     */
    public static function update(string $what, string $table, array $values, string $where = ""): bool
    {
        if (is_null(self::$instance))
        {
            self::getInstance();
        }
        
        // Handle the {TP} in the tablename
        self::handleTableprefix($table);
        
        // Is the tablename still valid?
        self::testTablename($table);

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
                throw new InvalidArgumentException(
                    "[Subway!] Not correct job in " . __CLASS__ . " in " . __LINE__ . ". Passed: " . $what,
                    40067
                );
        }

        $oTempHandle = self::$instance->db_handle;
        
        try {
            $oStatement = $oTempHandle->prepare($query);

            $oStatement->execute(array_values($values));

            return true;
        } catch(Exception $error) {
            trigger_error(sprintf(self::STR_EXCEPTION . "[2]", $error->getMessage()));
            trigger_error(sprintf(self::STR_EXCEPTION, mysqli_error($oTempHandle)));
            trigger_error(sprintf(self::STR_STATEMENT, preg_replace('/\s+/', ' ', $query)));
            self::$instance->set_error(sprintf(self::STR_EXCEPTION, mysqli_error($oTempHandle)));
            return false;
        }
    }

    /**
     * Drop the given table from the database.
     *
     * @param string $table
     * @return bool
     */
    public static function drop(string $table): bool
    {
        // Handle {TP} in the tablename.
        self::handleTableprefix($table);
        
        // Is the tblename still valid? throw exception.
        self::testTablename($table);

        self::query("DROP table `".$table."` IF EXISTS;");

        return true;
    }

    /**
     * Is the given tablename valid? (injection)
     *
     * @param   string  $table  A given tablename.
     * @return  bool
     * @throws  InvalidArgumentException
     */
    public static function testTablename(string $table): bool
    {
        if (!\preg_match('/^[\w]+$/i', $table))
        {
            throw new InvalidArgumentException(
                "[Subway!] Invalid table name: " . $table,
                40067
            );
        }
        return true;
    }

    /**
     * Handle some queries in an array.
     *
     * @param array $jobs
     * @return void
     */
    public static function handleJobs(array $jobs = []): void
    {
        foreach ($jobs as $queryStr)
        {
            self::query($queryStr);
        }
    }

    /**
     * Replaces {TP} in the given table name.
     *
     * @param  string   $tablename The tablemame as reference.
     * @return void
     */
    public static function handleTableprefix(string &$tablename): void
    {
        $tablename = str_replace(
            ['{TP}', '{TABLE_PREFIX}'],
            TABLE_PREFIX,
            $tablename
        );
    }

    /**
     * Make a (mySQL) string from an index array.
     *
     * @param  array  $fields   An assoc. array with the filednames.
     * @param  string $prefix   An optional prefix.
     *
     * @return string
     */
    public static function prepareFields(array $fields = [], string|null $prefix = ""): string
    {
        $prefixWithDot = ((empty($prefix) || (is_null($prefix))) ? "" : $prefix.".");
        if (empty($fields))
        {
            return $prefixWithDot."*";
        }

        return $prefixWithDot . "`" . implode("`, " . $prefixWithDot . "`", $fields) . "`";
    }
    
    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        // Nothing here
    }
}
