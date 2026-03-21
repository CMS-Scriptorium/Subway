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

namespace Subway\core\traits;

trait QueryTools
{
    protected ?object $mysqli = null;

    public function getMysqlHandle(): bool
    {
        $this->mysqli = $this->database->db_handle;

        return true;
    }

    /**
     * Returns a string with the fieldnams capsulated by backticks.
     *
     * @param array     $aFields    A linear array within the names.
     * @return string   The result string, or "*" for an empty array.
     */
    public function buildFields(array $aFields = []): string
    {
        return (empty($aFields))
            ? "*"
            : "`" . implode("`, `", $aFields) . "`"
            ;
    }

    // Here we go
    /**
     *  Public "shortcut" for executing a single mySql-query without passing values.
     *
     *
     *  @param    string  $aQuery A valid mySQL query.
     *  @param    bool    $bFetch Fetching the result - default is false.
     *  @param    array   $aStorage A storage array for the fetched results. Pass by reference!
     *  @param    bool    $bFetchAll Try to get all entries. Default is true.
     *  @return   int     If success number of affected rows.
     *
     *  @example
     *      $results_array = [];
     *      $database->execute_query(
     *          "SELECT * from ".TABLE_PREFIX."pages WHERE page_id = ".$page_id." ",
     *          true,
     *          $results_array,
     *          false
     *      );
     *
     */
    public function executeQuery(string $aQuery="", bool $bFetch=false, array &$aStorage=[], bool $bFetchAll=true ) : int
    {
        try{
            $oStatement=$this->mysqli->prepare($aQuery);

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
            return -1;
        }
    }

}
