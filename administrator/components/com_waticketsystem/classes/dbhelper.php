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
class WDBHelper {

    /**
	 * Quotes an identifier
	 *
	 * @param $identifier string
	 * @param $db JDatabase
	 */
    function nameQuote($identifier, $db = null) {
	    // prepapre DBO
		if (is_object($db)) {
			$db =& $db;
        } else {
            $db =& JFactory::getDBO();
		}
		
		// split the identifier up into names
		$names = explode(".", $identifier);
		
		// build the return value
		foreach($names AS $position => $name) {
		    $names[$position] = $db->nameQuote($name);
		}
		
		return implode(".", $names);
	}

}

?>