<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */


/**
 *
 * @static
 */
class WTableHelper {

    /**
	 * Gets an instance of a WTable
	 *
	 * @param $type string
	 * @return JTable
	 */
    function getInstance($type) {
	    return JTable::getInstance($type, "WTable", array());
	}

}

?>