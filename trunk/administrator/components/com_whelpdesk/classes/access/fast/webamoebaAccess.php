<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

hdimport("access.webamoeba.webamoebaSessionAccess");

/**
 * Concrete implementation of AccessInterface
 *
 * @author Administrator
 */
class WebamoebaAccess implements AccessInterface {

    private static $cache = null;

    /**
     * Adds a new group to the access database
     *
     * @param String $name Name of the new group
     * @param String $description Optional description
     * @throws HDException Occurs when group is not successfully added
     */
    public function addGroup($group, $description=null) {
        // preliminary check
        if (self::groupExists($group)) {
            // it's OK, the group already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = "INSERT INTO " . dbTable("access_groups") . " " .
                 "SET " . dbField("grp"). " = " . $db->Quote($group);
        if ($description != null) {
            $query .= ", " . dbField("description"). " = " . $db->Quote($description);
        }
        $db->setQuery($query);

        // execute query
        if (!$db->query()) {
            throw new HDException("ADD GROUP FAILED", $db->getErrorMsg());
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
        $tables = array("access_groups");
        $tables[] = "access_types";
        $tables[] = "access_tree";
        $tables[] = "access_map";
        $tables[] = "access_controls";

        // delete associated group records from tables
        $db = JFactory::getDBO();
        $query = "";
        $queryPartOne = " DELETE FROM ";
        $queryPartTwo = " WHERE " . dbField("grp") . " = " . $db->Quote($group);
        for ($i = count($tables) - 1; $i >= 0; $i--) {
            $query = $queryPartOne . dbTable($tables[$i]) . $queryPartTwo;
            $db->setQuery();
            $db->query();
        }
    }

    /**
     * Adds a new type access database
     *
     * @param <type> $group
     * @param <type> $type
     * @param <type> $description
     * @return <type>
     * @throws HDException
     */
    public function addType($group, $type, $description=null) {
        // preliminary check
        if (!self::groupExists($group)) {
            throw new HDException("GROUP DOES NOT EXIST", $group);
        }
        if (self::typeExists($group, $type)) {
            // it's OK, the type already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = "INSERT INTO " . dbTable("access_types") . " " .
                 "SET " . dbField("grp"). " = " . $db->Quote($group).
                 ", " . dbField("type"). " = " . $db->Quote($type);
        if ($description != null) {
            $query .= ", " . dbField("description"). " = " . $db->Quote($description);
        }
        $db->setQuery($query);
        echo $query;

        // execute query
        if (!$db->query()) {
            throw new HDException("ADD TYPE FAILED", $db->getErrorMsg());
        }
    }

    /**
     * Determines if the specified group exists
     *
     * @param String $group Group in which to look for type
     * @return boolean
     */
    public static function groupExists($group) {

        // initialise cache
        if (self::$cache == null) {
            $db =& JFactory::getDBO();

            // prepare query
            $query = "SELECT " . dbField("grp") . " ".
                     "FROM " . dbTable("access_groups");
            $db->setQuery($query);

            // populate cache
            $result = $db->loadResultArray(0);
            self::$cache = array();
            for ($i = count($result) - 1; $i >= 0; $i--) {
                $key = $result[$i];
                self::$cache[$key] = null;
            }
        }

        // do the business
        // group must be restricted to 100 characters as per the database setup
        // note we do not deal with non UTF-8 compatible MySQL servers, hence characters not bytes
        $group = JString::substr($group, 0, 100);
        return array_key_exists($group, self::$cache);
    }

    public static function typeExists($group, $type) {
        // initialise cache
        if (self::$cache == null) {
            if(!self::groupExists($group)) {
                // group does not exist, no point in continuing!
                return false;
            }
        }

        // initialise type cache
        if (self::$cache[$group] == null) {
            $db =& JFactory::getDBO();

            // prepare query
            $query = "SELECT " . dbField("type") . " ".
                     "FROM " . dbTable("access_types") . " ".
                     "WHERE " . dbField("grp") . " = " . $db->Quote($group);
            $db->setQuery($query);

            // populate cache
            $result = $db->loadResultArray(0);
            self::$cache[$group] = array();
            for ($i = count($result) - 1; $i >= 0; $i--) {
                $cacheType = $result[$i];
                self::$cache[$group][] = $cacheType;
            }
        }

        // do the business
        // type must be restricted to 100 characters as per the database setup
        // note we do not deal with non UTF-8 compatible MySQL servers, hence characters not bytes
        $type = JString::substr($type, 0, 100);
        return in_array($type, self::$cache[$group]);
    }

    /**
     * Gets a session object for the specified
     *
     * @param String $group
     * @return AccessSessionInterface
     * @throws HDException
     */
    public function getSession($group) {
        return WebamoebaSessionAccess::getInstance($group);
    }
}
?>
