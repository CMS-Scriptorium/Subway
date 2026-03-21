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

class Pages
{
    use Singleton;
    use QueryTools;

    public static $instance;

    protected ?object $database = null;
    
    public array $allPages = [];

    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        $this->database = $GLOBALS['database'];
        
        // Method is inside QueryTools
        $this->getMysqlHandle();
    }

    /**
     *    Generates a page-tree (array) by given parameters (see below).
     *
     *    @param    int     $root_id        Any root-(page) id. Default = 0.
     *    @param    array   $page_storage   Storage-Array for the results. Pass by reference!
     *    @param    array   $fields         A linear list of field-names to collect. As default
     *                                      'page_id', 'page_title', 'menu_title', 'parent','position','visibility', 'admin_groups' are
     *                                      collected in the result-array.
     *                    Keep in mind that also 'subpages' is generated!
     *
     *    @return    void    As the storage is called by reference.
     *
     */
    public function getPageTree(
        int $root_id = 0,
        array &$page_storage = [],
        array $fields = ['page_id', 'page_title', 'menu_title', 'parent','position', 'visibility', 'admin_groups']
        ): void
    {

        // [1.1] make sure that required fields are in list
        $aRequiredKeys = ['page_id', 'parent', 'visibility', 'admin_groups', 'link'];

        foreach ($aRequiredKeys as $mustBe)
        {
            if (!in_array($mustBe, $fields))
            {
                $fields[] = $mustBe;
            }
        }

        $select_fields = "`".implode("`,`", $fields)."`";

        $this->allPages = [];
        $this->execute_query(
            "SELECT ".$select_fields." FROM `".TABLE_PREFIX."pages` ORDER BY `parent`,`position`",
            true,
            $this->allPages
        );

        // [2.1]
        foreach ($this->allPages as &$ref)
        {
            $ref['admin_groups'] = explode(",", $ref['admin_groups']);
        }
        unset($ref);

        if (in_array("viewing_groups", $fields))
        {
            foreach ($this->allPages as &$ref)
            {
                $ref['viewing_groups'] = explode(",", $ref['viewing_groups']);
            }
        }
        unset($ref);

        $this->makeList($root_id, $page_storage);
    }

    /**
     *    Internal Sub-function for "page_tree" to build the page-tree via recursive calls.
     *
     *    @param    int     $aNum Root-Id
     *    @param    array   $aRefArray Result-Storage. Call by reference!
     *
     */ 
    protected function makeList(int $aNum, array &$aRefArray): void
    {

        foreach($this->allPages as &$aTempPage)
        {

            if ($aTempPage['parent'] > $aNum)
            {
                break;
            }
            

            if ($aTempPage['parent'] == $aNum)
            {
                $aTempPage['subpages'] = [];
                $this->makeList($aTempPage['page_id'], $aTempPage['subpages']);

                if (isset($aTempPage['link']))
                {
                    $aTempPage['link'] = PAGES_DIRECTORY.$aTempPage['link'].PAGE_EXTENSION; // show link also in overview, therefore no additional LEPTON_URL
                }

                $aRefArray[] = &$aTempPage;
            }
        }
    }
}
