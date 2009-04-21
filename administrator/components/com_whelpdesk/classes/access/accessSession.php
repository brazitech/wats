<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined('_JEXEC') or die('');

wimport('access.accessSessionInterface');

/**
 * Description of WAccessSession
 *
 * @author Administrator
 */
class WAccessSession implements WAccessSessionInterface {
    
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
        $this->treeSession = WTree::getInstance()->getSession($group);
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
     * @throws WException
     */
    public function setAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control,
                              $hasAccess) {

        // check request node exists
        if (!$this->treeSession->nodeExists($requestType, $requestIdentifier)) {
            throw new WException('NODE DOES NOT EXIST', $requestType,
                                              $requestIdentifier, $this->group);
        }

        // check target node exists
        if (!$this->treeSession->nodeExists($targetType, $targetIdentifier)) {
            throw new WException('NODE DOES NOT EXIST', $targetType,
                                               $targetIdentifier, $this->group);
        }

        // check control exists
        if (!$this->controlExists($targetType, $control)) {
            throw new WException('CONTROL DOES NOT EXIST',
                                           $targetType, $control, $this->group);
        }

        // does the control already exist?
        $db = JFactory::getDBO();

        $query = 'SELECT ' . dbName('allow') . ' ' .
                 'FROM ' . dbTable('access_map') . ' ' .
                 'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                 ' AND ' . dbName('request_type') . ' = ' . $db->Quote($requestType) .
                 ' AND ' . dbName('request_identifier') . ' = ' . $db->Quote($requestIdentifier) .
                 ' AND ' . dbName('target_type') . ' = ' . $db->Quote($targetType) .
                 ' AND ' . dbName('target_identifier') . ' = ' . $db->Quote($targetIdentifier) .
                 ' AND ' . dbName('type') . ' = ' . $db->Quote($type) .
                 ' AND ' . dbName('control') . ' = ' . $db->Quote($control);
        $db->setQuery($query);
        $allow = $db->loadResult();

        // do the leg work
        if ($allow == null) {
            // need to insert

            // prepare query
            $query = 'INSERT INTO ' . dbTable('access_map') . ' ' .
                     'SET ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                     ', ' . dbName('request_type') . ' = ' . $db->Quote($requestType) .
                     ', ' . dbName('request_identifier') . ' = ' . $db->Quote($requestIdentifier) .
                     ', ' . dbName('target_type') . ' = ' . $db->Quote($targetType) .
                     ', ' . dbName('target_identifier') . ' = ' . $db->Quote($targetIdentifier) .
                     ', ' . dbName('type') . ' = ' . $db->Quote($type) .
                     ', ' . dbName('control') . ' = ' . $db->Quote($control) .
                     ', ' . dbName('allow') . ' = ' . ($hasAccess ? 1 : 0);
            $db->setQuery($query);

            // attempt to insert the new rule
            if (!$db->query()) {
                throw new WException('SET ACCESS FAILED');
            }
            
        } elseif ((boolean)$allow != (boolean)$hasAccess) {
            // need to update

            // prepare query
            $query = 'UPDATE ' . dbTable('access_map') . ' ' .
                     'SET ' . dbName('allow') . ' = ' . ($hasAccess ? 1 : 0) . ' ' .
                     'WHERE '. dbName('grp') . ' = ' . $db->Quote($this->group) .
                     ' AND ' . dbName('request_type') . ' = ' . $db->Quote($requestType) .
                     ' AND ' . dbName('request_identifier') . ' = ' . $db->Quote($requestIdentifier) .
                     ' AND ' . dbName('target_type') . ' = ' . $db->Quote($targetType) .
                     ' AND ' . dbName('target_identifier') . ' = ' . $db->Quote($targetIdentifier) .
                     ' AND ' . dbName('type') . ' = ' . $db->Quote($type) .
                     ' AND ' . dbName('control') . ' = ' . $db->Quote($control);

            $db->setQuery($query);

            // attempt to update the existing rule
            if (!$db->query()) {
                throw new WException('SET ACCESS FAILED');
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
     * @throws WException
     */
    public function addControl($type, $control, $description='') {
        if ($this->controlExists($type, $control)) {
            // no need to continue the control already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = 'INSERT INTO ' . dbTable('access_controls') . ' ' .
                 'SET ' . dbName('grp') . ' = ' . $db->Quote($this->group) . ' ' .
                 ', ' . dbName('type') . ' = ' . $db->Quote($type) . ' ' .
                 ', ' . dbName('control') . ' = ' . $db->Quote($control) . ' ' .
                 ', ' . dbName('description') . ' = ' . $db->Quote($description) . ' ';
        
        // insert the new record
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('ADD CONTROL FAILED', $this->group, $type, $control);
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
        $query = 'DELETE FROM ' .dbTable('access_map') . ' ' .
                 'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                 ' AND ' . dbName('type') . ' = ' . $db->Quote($type) . ' ' .
                 ' AND ' . dbName('control') . ' = ' . $db->Quote($control);
        $db->setQuery($query);
        $db->query();

        // delete control entries
        $query = 'DELETE FROM ' .dbTable('access_controls') . ' ' .
                 'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                 ' AND ' . dbName('type') . ' = ' . $db->Quote($type) . ' ' .
                 ' AND ' . dbName('control') . ' = ' . $db->Quote($control);
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
            $query = 'SELECT ' . dbName('control') . ' ' .
                     'FROM ' . dbTable('access_controls') . ' ' .
                     'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                     ' AND ' . dbName('type') . ' = ' . $db->Quote($type);

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
        // get the request node
        $request = $this->treeSession->getNode($requestType, $requestIdentifier);
        if ($request == null) {
            // unknown node
            throw new WException('HAS ACCESS FAILED NODE DOES NOT EXIST', $requestType, $requestIdentifier);
        }

        // get the target node
        $target = $this->treeSession->getNode($targetType, $targetIdentifier);
        if ($target == null) {
            // unknown node
            throw new WException('HAS ACCESS FAILED NODE DOES NOT EXIST', $targetType, $targetIdentifier);
        }

        // get the DBO
        $db = JFactory::getDBO();

        // get the path of the request node
        $query = 'SELECT ' . dbName('type') . ', ' . dbName('identifier')
               . ' FROM ' . dbTable('tree')
               . ' WHERE ' . dbName('lft') . ' <= ' . $db->Quote($request['lft'])
               . ' AND ' . dbName('rgt') . ' >= ' . $db->Quote($request['rgt'])
               . ' ORDER BY ' . dbName('rgt') . ' ASC';
        $db->setQuery($query);
        $requestPath = $db->loadAssocList();

        // itterate over the request path and look for rules
        for($i = 0, $c = count($requestPath); $i < $c; $i++) {
            $requestNode = $requestPath[$i];

            // get the mappings
            $query = 'SELECT ' . dbName('allow')
                   . ' FROM ' . dbTable('tree')       . ' AS ' . dbName('target')
                   . ' JOIN ' . dbTable('access_map') . ' AS ' . dbName('map') . ' ON '
                   . '('
                   .      dbName('target.type')       . ' = ' . dbName('map.target_type') . ' AND '
                   .      dbName('target.identifier') . ' = ' . dbName('map.target_identifier')
                   . ')'
                   . ' WHERE ' . dbName('map.request_type')       . ' = '  . $db->Quote($requestNode['type'])
                   . ' AND '   . dbName('map.request_identifier') . ' = '  . $db->Quote($requestNode['identifier'])
                   . ' AND '   . dbName('target.lft')             . ' <= ' . $db->Quote($target['lft'])
                   . ' AND '   . dbName('target.rgt')             . ' >= ' . $db->Quote($$target['rgt'])
                   . ' AND '   . dbName('map.type')               . ' = '  . $db->Quote($type)
                   . ' AND '   . dbName('map.control')            . ' = '  . $db->Quote($control)
                   . ' ORDER BY ' . dbName('target.rgt') . ' ASC';
            $db->setQuery($query);
            $allow = $db->loadResult();

            // did we win ???
            if ($allow === '0') {
                return false;
            } elseif ($allow === '1') {
                return true;
            }
        }

        // no rules found, assume no access!
        return 'false';
    }

    /**
     * Resets all access to the session's group, i.e. literally removes all
     * rules that exist for the group.
     */
    public function resetAccess() {
        $db = JFactory::getDBO();

        // prepare the query
        $query = 'DELETE '
               . ' FROM ' . dbTable('access_controls')
               . ' WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group);

        // execute the query and in doing so populate the cache
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Adds a new node to the tree. If the parentType and parentIdentifier are
     * null, the node created as the root node, note however that a root node
     * can only be created if the tree is empty.
     *
     * @param String $type Type of node to add
     * @param String $identifier Node ID (unique to this type)
     * @param String $parentType Type of parent node to add
     * @param String $parentIdentifier Parent node ID
     */
    public function addNode($type, $identifier,
                                     $parentType=null, $parentIdentifier=null) {
        // delegate method
        $this->treeSession->addNode($type, $identifier,
                                                $parentType, $parentIdentifier);
    }

    /**
     * Removes an existing node from the tree. Sub nodes can either be removed
     * simultaneously, or they can be relocated into the parent container.
     *
     * @param String $type Type of node to remove
     * @param String $identifier Node ID
     * @param boolean $recursive delete sub nodes
     */
    public function removeNode($type, $identifier, $recursive=false) {
        // delegate method
        $this->treeSession->removeNode($type, $identifier, $recursive);
    }

    /**
     *
     * @param String $type Type of node to move
     * @param String $identifier Node ID
     * @param String $newParentType Type of parent node to move to
     * @param String $newParentIdentifier Parent node ID to move tp
     */
    public function moveNode($type, $identifier, $newParentType, 
                                                         $newParentIdentifier) {
        // delegate method
        $this->treeSession-> moveNode($type, $identifier, $newParentType,
                                                          $newParentIdentifier);
    }

    /**
     * Determines if a node exists in the tree, uses temporary caching to
     * increase speed.
     *
     * @param String $type Type of node to look for
     * @param String $identifier Node identifier
     * @return <type>
     */
    public function nodeExists($type, $identifier) {
        // delegate method
        $this->treeSession->nodeExists($type, $identifier);
    }

    /**
     * Adds a new type. Note that types cannot be removed.
     *
     * @param String $type Type to add, must be unique to group
     * @param String $description Optional description
     */
    public function addType($type, $description='') {
        // delegate method
        $this->treeSession->addType($type, $description);
    }

}
?>
