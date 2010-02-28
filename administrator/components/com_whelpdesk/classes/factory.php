<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

wimport('command');
wimport('config');
wimport('utilities.out');

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

    /**
     * Gets the globally available WOut object
     *
     * @return WOut
     */
    public static function getOut() {
        return WOut::getInstance();
    }

    /**
     * Array of JTable objects
     *
     * @var JTable[]
     */
    private static $tables = array();

    /**
     * Gets a cached JTable
     *
     * @param string $table
     * @return JTable
     */
    public static function getTable($table) {
        if (empty(self::$tables[$table])) {
            self::$tables[$table] = JTable::getInstance($table);
        }

        if (self::$tables[$table] == false)
        {
            throw new WException("UNKNOWN TABLE");
        }
        
        return self::$tables[$table];
    }

    /**
     * Gets an AccessInterface object that deals with the component permissions.
     *
     * @return AccessInterface
     */
    public static function getAccess() {
        wimport('access.access');
        return WAccess::getInstance();
    }

    /**
     * Gets a AccessSessionInterface object
     *
     * @param String $group Group that the session will be dealing with
     * @return AccessSessionInterface
     */
    public static function getAccessSession($group = 'component') {
        // return the tree session object
        return self::getAccess()->getSession($group);
    }

}

?>