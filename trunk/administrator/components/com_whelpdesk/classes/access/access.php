<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined('_JEXEC') or die('');

wimport('access.accessInterface');
wimport('tree.tree');

/**
 * Description of StandardAccess
 *
 * @author Administrator
 */
class WAccess implements WAccessInterface {

    /**
     * Delegate used to adhere to the TreeInterface
     *
     * @var WTree
     */
    private $tree;

    public function  __construct() {
        // prepare delegate
        $this->tree = WTree::getInstance();
    }

    /**
     * Gets a session object for the specified group
     *
     * @param String $group
     * @return AccessSessionInterface
     */
    public function getSession($group) {
        wimport('access.accessSession');
        return WAccessSession::getInstance($group);
    }

    public function addGroup($group, $description=null) {
        // delegate method
        $this->tree->addGroup($group, $description);
        $this->tree->addGroup($group.'-access', $description.' (Access Control Tree)');
    }

    /**
     * Removes a group.
     *
     * @param String $group Name of group to remove
     */
    public function removeGroup($group) {
        // prep work
        $db = JFactory::getDBO();
        $group = trim($group);

        // preliminary check
        if (!WTree::groupExists($group)) {
            // group does not exist, no need to continue
            return;
        }

        // update the tree
        $this->tree->removeGroup($group);

        // prepare for query loop
        $safeGroup = $db->Quote($group);
        $tables = array();
        $tables[] = 'access_map';
        $tables[] = 'access_controls';

        // itterate over tables array and delete records from the tables
        for ($i = count($tables) - 1; $i >= 0; $i --) {
            $query = 'DELETE FROM ' .dbTable() . ' '
                   . ' WHERE ' . dbName('grp') . ' = ' . $safeGroup;
            $db->setQuery($query);
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
        // delegate to WTree
        return WTree::groupExists($group);
    }

    /**
     * Global WAccess object
     *
     * @var WAccess
     */
    private static $instance = array();

    /**
     * Gets the global instance of WAccess
     *
     * @return WAccess
     */
    public static function getInstance() {
        // create object if it does not exist
        if (!self::$instance) {
            self::$instance = new WAccess();
        }

        // all done, send it home!
        return self::$instance;
    }
}
?>
