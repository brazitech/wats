<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

wimport('command');

/**
 *
 * @static
 */
class WFactory {

    /**
	 * Gets the globally available WConfig object
	 *
	 * @return WConfig
	 */
    public static function getConfig() {
	    return WConfig::getInstance();
	}

    /**
	 * Gets the globally available WCommand object
	 *
	 * @return WCommand
	 */
    public static function getCommand() {
	    return WCommand::getInstance();
	}

}

?>