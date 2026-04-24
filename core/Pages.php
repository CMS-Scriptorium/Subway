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

use Subway\core\sql\Database;
use Subway\core\traits\Singleton;
use const PAGE_EXTENSION;
use const PAGES_DIRECTORY;

class Pages
{

    use Singleton;

    public static $instance;

    public array $allPages = [];

    // Avoid using "new" for a new instance.
    protected function __construct()
    {
        // nothing here to do right now.
    }


    /**
     *  Generates a page-tree (array) by given parameters (see below).
     *
     *  @param  int    $root_id        Any root-(page) id. Default = 0.
     *  @param  array  $fields         A linear list of field-names to collect. As default
     *                                 'page_id', 'page_title', 'menu_title', 'parent','position','visibility', 'admin_groups' are
     *                                 collected in the result-array.
     *                      Keep in mind that also 'subpages' is generated!
     *
     *  @return   array   Two dim. array with the result/page-tree-values.
     *
     */
    public function getPageTree(
        int $root_id = 0,
        array $fields = ['page_id', 'page_title', 'menu_title', 'parent','position', 'visibility', 'admin_groups']
        ): array
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
        Database::executeQuery(
            "SELECT ".$select_fields." FROM `{TP}pages` ORDER BY `parent`,`position`",
            true,
            $this->allPages,
            true
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

        $page_storage = [];
        $this->makeList($root_id, $page_storage);

        return $page_storage;
    }

    /**
     *  Internal Sub-function for "page_tree" to build the page-tree via recursive calls.
     *
     *  @param  int     $aNum       Root-Id
     *  @param  array   $aRefArray  Result-Storage (call-by-reference!)
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
