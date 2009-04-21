<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined("_JEXEC") or die("");

hdimport("access.accessInterface");
hdimport("access.standard.accessSession");

/**
 * Description of StandardAccess
 *
 * @author Administrator
 */
class StandardAccess implements AccessInterface {

    /**
     * Gets a session object for the specified group
     *
     * @param String $group
     * @return AccessSessionInterface
     */
    public function getSession($group) {
        return StandardAccessSession::getInstance($group);
    }

    public function addGroup($group, $description=null) {
        // nothing to see here... move along
    }

    /**
     * Removes a group.
     *
     * @param String $group Name of group to remove
     */
    public function removeGroup($group) {
        $db = JFactory::getDBO();

        // prepare for query loop
        $safeGroup = $db->Quote($group);
        $tables = array();
        $tables[] = "access_map";
        $tables[] = "access_controls";

        // itterate over tables array and delete records from the tables
        for ($i = count($tables) - 1; $i >= 0; $i --) {
            $query = "DELETE FROM " .dbTable() . " " .
                     " WHERE " . dbField("grp") . " = " . $safeGroup;
            $db->setQuery($query);
            $db->query();
        }
    }
}
?>
