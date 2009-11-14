<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined('_JEXEC') or die('');

wimport('tree.treeInterface');
wimport('tree.treeSession');

/**
 * Concrete implementation of AccessInterface
 *
 * @author Administrator
 */
class WTree implements WTreeInterface {

    /**
     * Array of names of known groups
     * 
     * @var array
     * @static
     */
    private static $cache = null;

    /**
     * Adds a new group to the access database
     *
     * @param String $name Name of the new group
     * @param String $description Optional description
     * @throws WException Occurs when group is not successfully added
     */
    public function addGroup($group, $description=null) {
        // preliminary check
        if (self::groupExists($group)) {
            // it's OK, the group already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = 'INSERT INTO ' . dbTable('tree_groups') . ' ' .
                 'SET ' . dbName('grp'). ' = ' . $db->Quote($group);
        if ($description != null) {
            $query .= ', ' . dbName('description'). ' = ' . $db->Quote($description);
        }
        $db->setQuery($query);

        // execute query
        if (!$db->query()) {
            throw new WException('ADD GROUP FAILED', $db->getErrorMsg());
        }
    }

    /**
     * Removes a group from the access database
     *
     * @param String $group Group to remove
     */
    public function removeGroup($group) {
        // preliminary check
        if (!self::groupExists($group)) {
            // group does not exist, no need to continue
            return;
        }

        // tables from which records must be removed
        // in reverse order of importance, i.e. groups table is processed last
        $tables = array('tree_groups');
        $tables[] = 'tree_types';
        $tables[] = 'tree';

        // delete associated group records from tables
        $db = JFactory::getDBO();
        $query = '';
        $queryPartOne = ' DELETE FROM ';
        $queryPartTwo = ' WHERE ' . dbName('grp') . ' = ' . $db->Quote($group);
        for ($i = count($tables) - 1; $i >= 0; $i--) {
            $query = $queryPartOne . dbTable($tables[$i]) . $queryPartTwo;
            $db->setQuery();
            $db->query();
        }
    }

    /**
     * Determines if the specified group exists
     *
     * @param String $group Group in which to look for type
     * @return boolean
     * @static
     */
    public static function groupExists($group) {
        // initialise cache
        if (self::$cache == null) {
            $db =& JFactory::getDBO();

            // prepare query
            $query = 'SELECT ' . dbName('grp') . ' '
                   . 'FROM ' . dbTable('tree_groups');
            $db->setQuery($query);

            // populate cache
            self::$cache = $db->loadResultArray(0);
        }

        // do the business
        // group must be restricted to 100 characters as per the database setup
        // note we only deal with UTF-8 compatible MySQL servers, hence characters not bytes
        $group = JString::substr($group, 0, 100);
        return in_array($group, self::$cache);
    }

    /**
     * Gets a session object for the specified group
     *
     * @param String $group
     * @return AccessSessionInterface
     * @throws WException
     */
    public function getSession($group) {
        return StandardTreeSession::getInstance($group);
    }

    /**
     * Global WTree object
     *
     * @var WTree
     */
    private static $instance = array();

    /**
     * Gets the global instance of WAccess
     *
     * @return WTree
     */
    public static function getInstance() {
        // create object if it does not exist
        if (!self::$instance) {
            self::$instance = new WTree();
        }

        // all done, send it home!
        return self::$instance;
    }
}
?>
