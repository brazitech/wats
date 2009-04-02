<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined("_JEXEC") or die("");

hdimport("access.accessSessionInterface");

/**
 * Description of StandardAccessSession
 *
 * @author Administrator
 */
class StandardAccessSession implements AccessSessionInterface {
    
    /**
     * Group with which the session is dealing
     * 
     * @var String
     */
    private $group = null;

    /**
     *
     * @var TreeSessionInterface
     */
    private $treeSession = null;

    /**
     * Cache of known controls. Array is in the form array[type] == array, the
     * inner arrays contain the names of the controls specific to the type.
     *
     * @var array
     * @static
     */
    private $cache = array();

    /**
     * Instances of this class that deal with sessions for the various groups
     *
     * @var array
     * @static
     */
    private static $instances = array();

    public function __construct($group) {
        $this->group = $group;
        $this->treeSession = HDFactory::getTreeSession($group);
    }

    /**
     * Sets access to a target nodefrom a request node for a specific control.
     *
     * @param String $requestType
     * @param String $requestIdentifier
     * @param String $targetType
     * @param String $targetIdentifier
     * @param String $type
     * @param String $control
     * @param boolean $hasAccess
     * @throws HDExcpetion
     */
    public function setAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control,
                              $hasAccess) {

        // check request node exists
        if (!$this->treeSession->nodeExists($requestType, $requestIdentifier)) {
            throw new HDExcpetion("NODE DOES NOT EXIST", $requestType,
                                              $requestIdentifier, $this->group);
        }

        // check target node exists
        if (!$this->treeSession->nodeExists($targetType, $targetIdentifier)) {
            throw new HDExcpetion("NODE DOES NOT EXIST", $targetType,
                                               $targetIdentifier, $this->group);
        }

        // check control exists
        if (!$this->controlExists($targetType, $control)) {
            throw new HDExcpetion("CONTROL DOES NOT EXIST", 
                                           $targetType, $control, $this->group);
        }

        // does the control already exist?
        $db = JFactory::getDBO();

        $query = "SELECT " . dbField("allow") . " " .
                 "FROM " . dbTable("access_map") . " " .
                 "WHERE " . dbField("grp") . " = " . $db->Quote($this->group) .
                 " AND " . dbField("request_type") . " = " . $db->Quote($requestType) .
                 " AND " . dbField("request_identifier") . " = " . $db->Quote($requestIdentifier) .
                 " AND " . dbField("target_type") . " = " . $db->Quote($targetType) .
                 " AND " . dbField("target_identifier") . " = " . $db->Quote($targetIdentifier) .
                 " AND " . dbField("type") . " = " . $db->Quote($type) .
                 " AND " . dbField("control") . " = " . $db->Quote($control);
        $db->setQuery($query);
        $allow = $db->loadResult();

        // do the leg work
        if ($allow == null) {
            // need to insert

            // prepare query
            $query = "INSERT INTO " . dbTable("access_map") . " " .
                     "SET " . dbField("grp") . " = " . $db->Quote($this->group) .
                     ", " . dbField("request_type") . " = " . $db->Quote($requestType) .
                     ", " . dbField("request_identifier") . " = " . $db->Quote($requestIdentifier) .
                     ", " . dbField("target_type") . " = " . $db->Quote($targetType) .
                     ", " . dbField("target_identifier") . " = " . $db->Quote($targetIdentifier) .
                     ", " . dbField("type") . " = " . $db->Quote($type) .
                     ", " . dbField("control") . " = " . $db->Quote($control) .
                     ", " . dbField("allow") . " = " . ($hasAccess ? 1 : 0);
            $db->setQuery($query);

            // attempt to insert the new rule
            if (!$db->query()) {
                throw new HDException("SET ACCESS FAILED");
            }
            
        } elseif ((boolean)$allow != (boolean)$hasAccess) {
            // need to update

            // prepare query
            $query = "UPDATE " . dbTable("access_map") . " " .
                     "SET " . dbField("allow") . " = " . ($hasAccess ? 1 : 0) . " " .
                     "WHERE ". dbField("grp") . " = " . $db->Quote($this->group) .
                     " AND " . dbField("request_type") . " = " . $db->Quote($requestType) .
                     " AND " . dbField("request_identifier") . " = " . $db->Quote($requestIdentifier) .
                     " AND " . dbField("target_type") . " = " . $db->Quote($targetType) .
                     " AND " . dbField("target_identifier") . " = " . $db->Quote($targetIdentifier) .
                     " AND " . dbField("type") . " = " . $db->Quote($type) .
                     " AND " . dbField("control") . " = " . $db->Quote($control);

            $db->setQuery($query);

            // attempt to update the existing rule
            if (!$db->query()) {
                throw new HDException("SET ACCESS FAILED");
            }
        }

        // all done
    }

    /**
     * Adds a control to the access database
     *
     * @param String $type Type that the control is limited to
     * @param String $control The control identofiter, e.g. delete
     * @param String $description A basic description of the control for semantic purposes
     * @throws HDException
     */
    public function addControl($type, $control, $description="") {
        if ($this->controlExists($type, $control)) {
            // no need to continue the control already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = "INSERT INTO " . dbTable("access_controls") . " " .
                 "SET " . dbField("grp") . " = " . $db->Quote($this->group) . " " .
                 ", " . dbField("type") . " = " . $db->Quote($type) . " " .
                 ", " . dbField("control") . " = " . $db->Quote($control) . " " .
                 ", " . dbField("description") . " = " . $db->Quote($description) . " ";
        
        // insert the new record
        $db->setQuery($query);
        if (!$db->query()) {
            throw new HDException("ADD CONTROL FAILED", $this->group, $type, $control);
        }
    }

    /**
     * Removes a control from the access database
     *
     * @param String $type
     * @param String $control
     */
    public function removeControl($type, $control) {
        if ($this->controlExists($type, $control)) {
            // no need to continue the control does not exists
            return;
        }

        $db = JFactory::getDBO();

        // delete map entries
        $query = "DELETE FROM " .dbTable("access_map") . " " .
                 "WHERE " . dbField("grp") . " = " . $db->Quote($this->group) .
                 " AND " . dbField("type") . " = " . $db->Quote($type) . " " .
                 " AND " . dbField("control") . " = " . $db->Quote($control);
        $db->setQuery($query);
        $db->query();

        // delete control entries
        $query = "DELETE FROM " .dbTable("access_controls") . " " .
                 "WHERE " . dbField("grp") . " = " . $db->Quote($this->group) .
                 " AND " . dbField("type") . " = " . $db->Quote($type) . " " .
                 " AND " . dbField("control") . " = " . $db->Quote($control);
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Gets a list of controls
     *
     * @param String $group
     * @param String $type
     * @return array
     * @todo
     */
    public function getControls($type=null) {
        
    }

    /**
     * Gets an instance of this class specifically for dealing with a given
     * group.
     *
     * @param String $group The group the session deals with
     * @return AccessSessionInterface
     * @static
     */
    public static function getInstance($group) {
        if (!array_key_exists($group, self::$instances)) {
            $className = get_class();
            self::$instances[$group] = new $className($group);
        }

        return self::$instances[$group];
    }

    /**
     * Determines if the specified control exists.
     *
     * @param String $group
     * @param String $type
     * @param String $control
     * @return boolean
     * @static
     */
    public function controlExists($type, $control) {
        // check cache is populated
        if (!array_key_exists($type, $this->cache)) {
            $db = JFactory::getDBO();

            // prepare the query
            $query = "SELECT " . dbField("control") . " " .
                     "FROM " . dbTable("access_controls") . " " .
                     "WHERE " . dbField("grp") . " = " . $db->Quote($this->group) .
                     " AND " . dbField("type") . " = " . $db->Quote($type);

            // execute the query and in doing so populate the cache
            $db->setQuery($query);
            $this->cache[$type] = $db->loadResultArray();
        }

        // return the response
        return in_array($control, $this->cache[$type]);
    }

    /**
     * @todo
     */
    public function clearAccess($requestType, $requestIdentifier,
                                $targetType, $targetIdentifier,
                                $control=null) {

                                }
    
    /**
     * @todo
     */
    public function hasAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control) {

                              }

    /**
     * @todo
     */
    public function resetAccess() {
        
    }

}
?>
