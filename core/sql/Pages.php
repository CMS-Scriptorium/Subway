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

    protected ?object $database = NULL;
    
    public array $WBCE_all_pages = [];

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
    public function page_tree(
        int $root_id = 0,
        array &$page_storage = [],
        array $fields = ['page_id', 'page_title', 'menu_title', 'parent','position', 'visibility', 'admin_groups']
        ): void
    {
        // global $WBCE_all_pages;
        // $database = LEPTON_database::getInstance();

        // [1.0.2]
        // $bUserHasAdminRights = LEPTON_core::userHasAdminRights();
        // [1.0.3]
        // $aUserGroups = LEPTON_CORE::getValue("groups_id", "string", "session", ",");

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

        $this->WBCE_all_pages = [];
        $this->execute_query(
            "SELECT ".$select_fields." FROM `".TABLE_PREFIX."pages` ORDER BY `parent`,`position`",
            true,
            $this->WBCE_all_pages
        );

        // [2.1]
        foreach ($this->WBCE_all_pages as &$ref)
        {
            $ref['admin_groups'] = explode(",", $ref['admin_groups']);
        }
        unset($ref);

        // [2.2]
/*
        if ($bUserHasAdminRights == true)
        {
            foreach ($this->LEPTON_CORE_all_pages as &$ref)
            {
                $ref['userAllowed'] = true;
            }
        }
        else
        {
            foreach ($this->LEPTON_CORE_all_pages as &$ref)
            {
                $ref['userAllowed'] = !empty(array_intersect($aUserGroups, $ref['admin_groups']));
            }
        }
*/
        if (in_array("viewing_groups", $fields))
        {
            foreach ($this->WBCE_all_pages as &$ref)
            {
                $ref['viewing_groups'] = explode(",", $ref['viewing_groups']);
            }
        }
        unset($ref);

        $this->make_list($root_id, $page_storage);
    }

    /**
     *    Internal Sub-function for "page_tree" to build the page-tree via recursive calls.
     *
     *    @param    int     $aNum Root-Id
     *    @param    array   $aRefArray Result-Storage. Call by reference!
     *
     */ 
    protected function make_list(int $aNum, array &$aRefArray): void
    {
        // global $WBCE_all_pages, $TEXT;

        foreach($this->WBCE_all_pages as &$aTempPage)
        {

            if ($aTempPage['parent'] > $aNum)
            {
                // return;
                break;
            }
            

            if ($aTempPage['parent'] == $aNum)
            {
/*
                switch ($aTempPage['visibility'])
                {

                    case 'public':
                        $aTempPage['status_icon'] = "visible_16.png";
                        $aTempPage['status_text'] = $TEXT['PUBLIC'];
                        $aTempPage['status_uiicon'] = 'unhide';
                        break;

                    case 'private':
                        $aTempPage['status_icon'] = "private_16.png";
                        $aTempPage['status_text'] = $TEXT['PRIVATE'];
                        $aTempPage['status_uiicon'] = 'user';
                        break;

                    case 'registered':
                        $aTempPage['status_icon'] = "keys_16.png";
                        $aTempPage['status_text'] = $TEXT['REGISTERED'];
                        $aTempPage['status_uiicon'] = 'sign in';
                        break;

                    case 'hidden':
                        $aTempPage['status_icon'] = "hidden_16.png";
                        $aTempPage['status_text'] = $TEXT['HIDDEN'];
                        $aTempPage['status_uiicon'] = 'hide';
                        break;

                    case 'none':
                        $aTempPage['status_icon'] = "none_16.png";
                        $aTempPage['status_text'] = $TEXT['NONE'];
                        $aTempPage['status_uiicon'] = 'lock';
                        break;

                    case 'deleted':
                        $aTempPage['status_icon'] = "deleted_16.png";
                        $aTempPage['status_text'] = $TEXT['DELETED'];
                        $aTempPage['status_uiicon'] = 'recycle red';
                        break;

                    default:
                        die(LEPTON_tools::display("Error: [20012] ".$aTempPage['visibility']. " unknown!", "pre", "ui message red"));
                        break;

                }
*/
                $aTempPage['subpages'] = [];
                $this->make_list($aTempPage['page_id'], $aTempPage['subpages']);

                if (isset($aTempPage['link']))
                {
                    $aTempPage['link'] = PAGES_DIRECTORY.$aTempPage['link'].PAGE_EXTENSION; // show link also in overview, therefore no additional LEPTON_URL
                }

                $aRefArray[] = &$aTempPage;
            }
        }
    }
}
