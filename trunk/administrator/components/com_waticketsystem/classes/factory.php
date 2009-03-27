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