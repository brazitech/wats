<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

/**
 * Automaticlaly load WConfig class
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . "classes" . DS . "config.php");

/**
 *
 * @static
 */
class WFactory {

    /**
	 * Gets teh globally available WConfig
	 *
	 * @return JTable
	 */
    function &getConfig() {
	    return WConfig::getInstance();
	}

}

?>