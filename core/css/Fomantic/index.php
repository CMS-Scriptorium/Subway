<?php

/**
 *  @module      	Library Fomantic
 *  @version        see info.php of this module
 *  @author         LEPTON project
 *	@copyright      2010-2026 LEPTON Project
 *  @license        https://opensource.org/licenses/MIT
 *  @license terms  see info.php of this addon
 *  @platform       see info.php of this addon
 */

// include secure.php to protect this file and the whole CMS!
if(!defined("SEC_FILE")){define("SEC_FILE",'/framework/secure.php' );}
if (defined('LEPTON_PATH')) {	
	include LEPTON_PATH.SEC_FILE;
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.SEC_FILE))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.SEC_FILE)) { 
		include $root.SEC_FILE;   
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include secure file
