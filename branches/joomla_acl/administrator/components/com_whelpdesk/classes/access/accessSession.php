<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined('_JEXEC') or die('');

/**
 * Description of WAccessSession
 *
 * @author Administrator
 */
class WAccessSession {
    
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
    private $componentTreeSession = null;

    /**
     *
     * @var TreeSessionInterface
     */
    private $accessTreeSession = null;

    /**
     * Last known control path. Acts as a cache allowing other classes to see
     * the most recent path. Usage outside of the WAccessSession class should be
     * implemented cautiously. This will always contain the latest path as
     * defined by {@link WAccessSession::hasAccess()}.
     *
     * @var array
     */
    private $controlPath;

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

    public function __construct($group = 'component') {
        $this->group = $group;
        $this->componentTreeSession = WTree::getInstance()->getSession($group);
        $this->accessTreeSession   = WTree::getInstance()->getSession($group.'-access');
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
        if (!$this->componentTreeSession->nodeExists($requestType, $requestIdentifier)) {
            throw new WException('NODE DOES NOT EXIST', $requestType,
                                              $requestIdentifier, $this->group);
        }

        // check target node exists
        if (!$this->componentTreeSession->nodeExists($targetType, $targetIdentifier)) {
            throw new WException('NODE DOES NOT EXIST %s, %s', $targetType,
                                               $targetIdentifier, $this->group);
        }

        // check control exists
        if (!$this->controlExists($type, $control)) {
            throw new WException('CONTROL DOES NOT EXIST',
                                                 $type, $control, $this->group);
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
    public function addControl($type, $control, $description='',
                                        $parentType=null, $parentControl=null) {
        try {
            // delegate to control tree
            $this->accessTreeSession->addNode($type, $control, $description,
                                                   $parentType, $parentControl);
        } catch (WException $e) {
            //throw new WException('ADD CONTROL FAILED', $this->group, $type, $control);
            throw $e;
        }
    }

    /**
     * Removes a control from the access database
     *
     * @param String $type
     * @param String $control
     */
    public function removeControl($type, $control, $recursive=false) {
        try {
            // delegate to control tree
            $this->accessTreeSession->deleteNode($type, $control, $recursive);
        } catch (WException $e) {
            throw new WException('REMOVE CONTROL FAILED', $this->group, $type, $control);
        }

        $db = JFactory::getDBO();

        // delete map entries
        $query = 'DELETE FROM ' .dbTable('access_map') . ' ' .
                 'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                 ' AND ' . dbName('type') . ' = ' . $db->Quote($type) . ' ' .
                 ' AND ' . dbName('control') . ' = ' . $db->Quote($control);
        $db->setQuery($query);
        $db->query();
    }

    /**
     *
     * @param String $type Type of control to move
     * @param String $identifier Control ID
     * @param String $newParentType Type of parent control to move to
     * @param String $newParentIdentifier Parent control ID to move to
     */
    public function moveControl($type, $identifier, $newParentType,
                                                         $newParentIdentifier) {
        // delegate method
        $this->accessTreeSession->moveNode(
            $type,
            $identifier,
            $newParentType,
            $newParentIdentifier
        );
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
        // delegate to control tree
        return $this->accessTreeSession->nodeExists($type, $control);
    }

    /**
     * @todo
     */
    public function clearAccess($requestType, $requestIdentifier,
                                $targetType, $targetIdentifier,
                                $controlType=null, $control=null) {
        $database = JFactory::getDBO();

        $query = 'DELETE FROM ' . dbTable('access_map')
               . ' WHERE ' . dbName('grp') . ' = ' . $database->Quote($this->group)
               . ' AND '  . dbName('request_type')       . ' = ' . $database->Quote($requestType)
               . ' AND '  . dbName('request_identifier') . ' = ' . $database->Quote($requestIdentifier)
               . ' AND '  . dbName('target_type')        . ' = ' . $database->Quote($targetType)
               . ' AND '  . dbName('target_identifier')  . ' = ' . $database->Quote($targetIdentifier)
               . (($controlType != null) ? ' AND '  . dbName('type')    . ' = ' . $database->Quote($controlType) : '')
               . (($control != null)     ? ' AND '  . dbName('control') . ' = ' . $database->Quote($control) : '');
        $database->setQuery($query);
        return $database->query();
    }
    
    /**
     * @todo
     */
    public function hasAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control) {
        // log the request    
        WFactory::getOut()->log($requestType . ', ' . $requestIdentifier
                                . ' is looking for access to '
                                . $targetType . ', ' . $targetIdentifier
                                . ' for '
                                . $type . ', ' . $control);
        
        // get the control path
        $this->controlPath = $this->accessTreeSession->getNodePath($type, $control);
        if (!count($this->controlPath)) {
            WFactory::getOut()->log('Access denied control '
                                    . $type . ', '
                                    . $control . ' does not exist');
            throw new WException('CONTROL DOES NOT EXIST', $type, $control);
        }

        // get the request node
        $request = $this->componentTreeSession->getNode($requestType, $requestIdentifier);
        if ($request == null) {
            // unknown node
            WFactory::getOut()->log('Access denied request object '
                                    . $requestType . ', '
                                    . $requestIdentifier . ' does not exist');
            throw new WException('HAS ACCESS FAILED NODE DOES NOT EXIST', $requestType, $requestIdentifier);
        }

        // get the target node
        $target = $this->componentTreeSession->getNode($targetType, $targetIdentifier);
        if ($target == null) {
            // unknown node
            WFactory::getOut()->log('Access denied target object '
                                    . $targetType . ', '
                                    . $targetIdentifier . ' does not exist');
            throw new WException('HAS ACCESS FAILED NODE DOES NOT EXIST', $targetType, $targetIdentifier);
        }

        // itterate over the request path and look for rules
        // get the DBO
        $db = JFactory::getDBO();
        for($i = count($this->controlPath) - 1; $i >= 0; $i--) {
            $controlNode = $this->controlPath[$i];

            // get the mappings
            $query = 'SELECT ' . dbName('allow')
                   . ' FROM ' . dbTable('tree')        . ' AS ' . dbName('target')
                   . ' JOIN ' . dbTable('access_map')  . ' AS ' . dbName('map') . ' ON '
                   . '('
                   .      dbName('target.type')        . ' = ' . dbName('map.target_type') . ' AND '
                   .      dbName('target.identifier')  . ' = ' . dbName('map.target_identifier')
                   . ')'
                   . ' JOIN ' . dbTable('tree')        . ' AS ' . dbName('request') . ' ON '
                   . '('
                   .      dbName('request.type')       . ' = ' . dbName('map.request_type') . ' AND '
                   .      dbName('request.identifier') . ' = ' . dbName('map.request_identifier')
                   . ')'
                   . ' WHERE ' . dbName('request.lft')            . ' <= ' . $db->Quote($request['lft'])
                   . ' AND '   . dbName('request.rgt')            . ' >= ' . $db->Quote($request['rgt'])
                   . ' AND '   . dbName('target.lft')             . ' <= ' . $db->Quote($target['lft'])
                   . ' AND '   . dbName('target.rgt')             . ' >= ' . $db->Quote($target['rgt'])
                   . ' AND '   . dbName('map.type')               . ' = '  . $db->Quote($controlNode['type'])
                   . ' AND '   . dbName('map.control')            . ' = '  . $db->Quote($controlNode['identifier'])
                   . ' ORDER BY ' . dbName('target.rgt') . ' ASC, ' . dbName('request.rgt') . ' ASC';
            $db->setQuery($query);
            $allow = $db->loadResult();

            // did we win ???
            if ($allow === '0') {
                // access restrcited
                WFactory::getOut()->log('Access denied at control '
                                        . $controlNode['type'] . ', '
                                        . $controlNode['identifier']);
                return false;
            } elseif ($allow === '1' && $i == 0) {
                // access allowed and we are at the leaf node!
                WFactory::getOut()->log('Access allowed');
                return true;
            }
        }

        // no rules found, assume no access!
        WFactory::getOut()->log('Access denied no rules found');
        return false;
    }

    public function getRule($requestType, $requestIdentifier,
                            $targetType, $targetIdentifier,
                            $type, $control) {
        $database = JFactory::getDBO();

        // prepare the query
        $query = 'SELECT ' . dbName('allow')
               . ' FROM ' . dbTable('access_map')
               . ' WHERE ' . dbName('grp') . ' = ' . $database->Quote($this->group)
               . ' AND ' . dbName('request_type') . ' = ' . $database->Quote($requestType)
               . ' AND ' . dbName('request_identifier') . ' = ' . $database->Quote($requestIdentifier)
               . ' AND ' . dbName('target_type') . ' = ' . $database->Quote($targetType)
               . ' AND ' . dbName('target_identifier') . ' = ' . $database->Quote($targetIdentifier)
               . ' AND ' . dbName('control') . ' = ' . $database->Quote($control)
               . ' AND ' . dbName('type') . ' = ' . $database->Quote($type);

        // execute the query and in doing so populate the cache
        $database->setQuery($query);
        $status = $database->loadResult();

        switch ($status) {
            case '1':
                $status = 'allow';
                break;

            case '0':
                $status = 'deny';
                break;

            default:
                $status = 'inherit';
        }
        
        return $status;
    }

    /**
     * Resets all access to the session's group, i.e. literally removes all
     * rules that exist for the group.
     */
    public function resetAccess() {
        $db = JFactory::getDBO();

        // prepare the query
        $query = 'DELETE '
               . ' FROM ' . dbTable('access_map')
               . ' WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group);

        // execute the query and in doing so populate the cache
        $db->setQuery($query);
        $db->query();
    }

    /**
     *
     * @param <type> $type
     * @param <type> $control
     * @param <type> $requireType
     * @param <type> $requireControl
     * @todo tidy this up
     */
    public function addControlRequirement($type, $control, 
                                                $requireType, $requireControl) {
        // check control exists
        if (!$this->controlExists($type, $control)) {
            throw new WException('CONTROL DOES NOT EXIST',
                                                 $type, $control, $this->group);
        }
        
        // check require control exists
        if (!$this->controlExists($type, $control)) {
            throw new WException('CONTROL DOES NOT EXIST',
                                                 $type, $control, $this->group);
        }

        // check if requirement already exists
        $db = JFactory::getDBO();
        $query = 'SELECT ' . dbName('requireType')    . ' AS ' . dbName('require') . ',  '
               .             dbName('requireControl') . ' AS ' . dbName('control') . '  '
               . ' WHERE ' . dbName('type') . ' = ' . $db->Quote($type)
               . ' AND ' .   dbName('control') . ' = ' . $db->Quote($control);
        $db->setQuery($query);

    }

    /**
     *
     * @param <type> $type
     * @param <type> $control
     * @return <type>
     * @todo tidy this up
     */
    public function getControlRequirements($type, $control) {
        // query the database
        $db = JFactory::getDBO();
        $query = 'SELECT ' . dbName('requireType')    . ' AS ' . dbName('require') . ',  '
               .             dbName('requireControl') . ' AS ' . dbName('control') . '  '
               . ' WHERE ' . dbName('type') . ' = ' . $db->Quote($type)
               . ' AND ' .   dbName('control') . ' = ' . $db->Quote($control);
        $db->setQuery($query);

        return $db->loadAssocList();
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
    public function addNode($type, $identifier, $description=null,
                                     $parentType=null, $parentIdentifier=null) {
        // delegate method
        $this->componentTreeSession->addNode($type, $identifier, $description,
                                                $parentType, $parentIdentifier);
    }

    /**
     * Removes an existing node from the tree. Sub nodes can either be removed
     * simultaneously, or they can be relocated into the parent container.
     *
     * @param String $type Type of node to remove
     * @param String $identifier Node ID
     * @param boolean $recursive delete sub nodes
     * @throws WTreeException
     */
    public function deleteNode($type, $identifier, $recursive=false) {
        // delegate method
        $this->componentTreeSession->deleteNode($type, $identifier, $recursive);
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
        $this->componentTreeSession->moveNode($type, $identifier, 
                                          $newParentType, $newParentIdentifier);
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
        return $this->componentTreeSession->nodeExists($type, $identifier);
    }

    /**
     * Adds a new type. Note that types cannot be removed.
     *
     * @param String $type Type to add, must be unique to group
     * @param String $description Optional description
     */
    public function addType($type, $description='') {
        // delegate method
        $this->componentTreeSession->addType($type, $description);
        $this->accessTreeSession->addType($type, $description);
    }

    public function getControlPath() {
        return $this->controlPath;
    }

    public function getNodes($branchType, $branchIdentifier) {
        return $this->componentTreeSession->getNodes($branchType, $branchIdentifier);
    }

    public function getAccessNodes($branchType, $branchIdentifier) {
        return $this->accessTreeSession->getNodes($branchType, $branchIdentifier);
    }

}
?>
