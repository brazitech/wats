<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined('_JEXEC') or die('');

/**
 * Description of HDTreeSession
 *
 * @author James Kennard
 */
class StandardTreeSession {

    /**
     * Group that the session is dealign with. A session can only deal
     * with one group. To deal with more than one group create a new
     * session object.
     *
     * @var string
     */
    private $group;

    /**
     * Instances of this class that deal with sessions for the various groups
     *
     * @var array
     * @static
     */
    private static $instances = array();

    /**
     * Key based array of boolean values, caches known and unknown nodes
     *
     * @var array
     */
    private $nodeExists = array();

    /**
     * Array of names of known types
     *
     * @var array
     */
    private $cache = null;

    /**
     * Should only be instantiated from within this itself. To get a instance of
     * this class use the getInstance method.
     *
     * @param string $group Group to handle in session
     * @see HDTree::getInstance()
     * @throws WException
     */
    public function __construct($group) {
        // check group is valid
        if (!WTree::groupExists($group)) {
            throw new WException('TREE SESSION INSTANTIATION FAILED', $group);
        }

        $this->group = $group;
    }

    /**
     * Gets an instance of this class specifically for dealing with a given
     * group.
     *
     * @param String $group The group the session deals with
     * @return TreeSessionInterface
     */
    public static function getInstance($group) {
        if (!array_key_exists($group, self::$instances)) {
            $className = get_class();
            self::$instances[$group] = new $className($group);
        }

        return self::$instances[$group];
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
     * @throws WException
     */
    public function addNode($type, $identifier, $description=null,
                                     $parentType=null, $parentIdentifier=null) {
        // are we trying to add a root node?
        if ($parentType === null) {
            $this->setRootNode($type, $identifier);
            return;
        }

        // check node does not already exist
        if ($this->nodeExists($type, $identifier)) {
            throw new WException('NODE EXISTS', $type, $identifier,
                                                                  $this->group);
        }

        // get the parent and check that parent exists
        $parentNode = $this->getNode($parentType, $parentIdentifier);
        if ($parentNode == null) {
            throw new WException('NODE DOES NOT EXIST', $parentType,
                                               $parentIdentifier, $this->group);
        }

        // get ready for some database action
        $db = JFactory::getDBO();

        // make a hole in the tree
        $parentNode['rgt'] = $this->makeHole($parentNode['rgt'], 1);

        // insert the new node
        $query = 'INSERT INTO ' . dbTable('tree') . ' ' .
                 'SET ' . dbName('grp') . ' = ' . $db->Quote($this->group) .
                 ', ' . dbName('type') . ' = ' . $db->Quote($type) .
                 ', ' . dbName('identifier') . ' = ' . $db->Quote($identifier) .
                 ', ' . dbName('description') . ' = ' . $db->Quote($description) .
                 ', ' . dbName('parent_type') . ' = ' . $db->Quote($parentType) .
                 ', ' . dbName('parent_identifier') . ' = ' . $db->Quote($parentIdentifier) .
                 ', ' . dbName('rgt') . ' = ' . ($parentNode['rgt'] - 1) .
                 ', ' . dbName('lft') . ' = ' . ($parentNode['rgt'] - 2);
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('COULD NOT UPDATE TREE', $db->getErrorMsg());
        }

        // update the cache
        $this->nodeExists[$type . '#' . $identifier] = true;
    }

    /**
     * Creates a hole in the tree, this is a dangerous method as it could 
     * potentially corrupt the tree. For this reason, it is declared private.
     *
     * @param int $before
     * @param int $numberOfNodes Number of nodes to make room for
     * @return int New position of $before, i.e. $before + ($numberOfNodes * 2)
     */
    private function makeHole($before, $numberOfNodes) {
        // sanity parse
        $before   = (int)$before;
        $holeSize = ((int)$numberOfNodes) * 2;

        // update the database
        $db = JFactory::getDBO();
        $query = 'UPDATE ' . dbTable('tree') . ' ' .
                 'SET ' . dbName('rgt') . ' = ' . dbName('rgt') . ' + ' . $holeSize . ' ' .
                 // only update lft if it needs to be... hence the IF expression
                 ', ' . dbName('lft') . ' = ' . dbName('lft') . ' + IF(' . dbName('lft') . ' > ' . (int)$before . ', ' . $holeSize . ', 0) ' .
                 // do not need to include lft in WHERE clause, dealt with in line above
                 // lft WHERE clause would be a subset of rgt clause anyway
                 'WHERE ' . dbName('rgt') . ' >= ' . (int)$before . ' ' .
                 'AND ' . dbName('grp') . ' = ' . $db->Quote($this->group);
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('COULD NOT UPDATE TREE', $db->getErrorMsg());
        }

        // return the new position of $before
        return $before + $holeSize;
    }

    /**
     * Fills a hole in the tree by bunching up the entire tree. $start and $end 
     * are inclusive of the hole itself, for example a hole for one node such as
     * [2,3] would be representative of $start == 2 && $end == 3. Note that the
     * existance of the hole is not verified, this is the responsibility of the
     * whatever is calling the method. Filling a hole that does not exist will
     * corrupt the tree.
     *
     * @param int $start Position where hole begins
     * @param int $end Position where hole ends
     */
    private function fillHole($start, $end) {
        $db = JFactory::getDBO();

        $holeSize = $end - $start + 1;
        $query = 'UPDATE ' . dbTable('tree') . ' ' .
                 'SET ' . dbName('rgt') . ' = ' . dbName('rgt') . ' - ' . $holeSize .
                 ', ' . dbName('lft') . ' = ' . dbName('lft') . ' - IF(' . dbName('lft') . ' > ' . $end . ', ' . $holeSize . ', 0) ' .
                 // WHERE clause is based on rgt, modifications to lft are
                 // conditional. Note that $node is a snapshot prior to the
                 // move, thus it now identifies the hole
                 // axiom: aNode.lft < aNode.rgt
                 'WHERE ' . dbName('rgt') . ' > ' . $end . ' ' .
                 'AND ' . dbName('grp') . ' = ' . $db->Quote($this->group);
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('COULD NOT UPDATE TREE', $db->getErrorMsg());
        }
    }

    /**
     * Sets the root node to the tree. Note that there can only be one root node
     * per tree and a root node can only be set when the tree is empty.
     *
     * @param String $type
     * @param String $identifier
     * @throws WException
     */
    private function setRootNode($type, $identifier) {
        // check the type is valid
        if (!$this->typeExists($type)) {
            throw new WException('TYPE DOES NOT EXIST', $type, $this->group);
        }

        // check the tree is empty
        $db = JFactory::getDBO();
        $query = 'SELECT COUNT(' . dbName('grp') . ') AS count '.
                 'FROM ' . dbTable('tree') . ' '.
                 'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group);
        $db->setQuery($query);
        if ($db->loadResult() != 0) {
            throw new WException('TREE IS NOT EMPTY', $this->group);
        }

        // OK to continue, add root node!
        $db = JFactory::getDBO();
        $query = 'INSERT INTO ' . dbTable('tree') . ' ' .
                 'SET ' . dbName('grp'). ' = ' . $db->Quote($this->group) .
                 ', ' . dbName('type'). ' = ' . $db->Quote($type) .
                 ', ' . dbName('identifier'). ' = ' . $db->Quote($identifier) .
                 ', ' . dbName('lft'). ' = 1' .
                 ', ' . dbName('rgt'). ' = 2';
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('SET ROOT NODE FAILED', $db->getErrorMsg(),
                                              $this->group, $type, $identifier);
        }

        // update the cache
        $this->nodeExists[$type . '#' . $identifier] = true;
    }

    /**
     * Removes an existing node from the tree. Sub nodes can either be removed
     * simultaneously, or they can be relocated into the parent container. Care
     * should be taken when removing the root node. The root node can only be
     * removed if recursive is true, or if the tree only consists of the root
     * node.
     *
     * @param String $type Type of node to remove
     * @param String $identifier Node ID
     * @param boolean $recursive delete sub nodes
     */
    public function removeNode($type, $identifier, $recursive=false) {
        // prepare some bits and pieces
        $node = $this->getNode($type, $identifier);
        $db = JFactory::getDBO();

        // if this is not recursive and there are sub nodes, we need to move
        // the sub nodes.
        if ($recursive == false && ($node['rgt'] - $node['lft'] > 1)) {
            if ($node['parent_type'] == null &&
                                           $node['parent_identifier'] == null) {
                // we cannot remove the root node if recursive is false because
                // this could screw up the tree, remeber this only applies if
                // the node has sub nodes, i.e. it is OK if the tree only
                // consists of a root node.
                throw new WException('REMOVE NODE FAILED');
            }

            // OK, time to relocate the sub nodes
            // we only need the immediate children, relocation of their sub
            // nodes will be handled by the move() method
            $typeSafe = $db->Quote($node['type']);
            $identifierSafe = $db->Quote($node['identifier']);
            $query = 'SELECT ' . dbName('type') . ', ' . dbName('identifier') . ' ' .
                     'FROM ' . dbTable('tree') . ' ' .
                     'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group) . ' ' .
                     'AND ' . dbName('parent_type') . ' = ' . $typeSafe . ' ' .
                     'AND ' . dbName('parent_identifier') . ' = ' . $identifierSafe . ' ';
            $db->setQuery($query);
            $subNodes = $db->loadAssocList();

            // itterate over the subnodes and move each one to its new location
            $subNode = null;
            for ($i = count($subNodes) - 1; $i >= 0; $i --) {
                $subNode = $subNodes[$i];
                $this->moveNode($subNode['type'], $subNode['identifier'],
                              $node['parent_type'], $node['parent_identifier']);
            }

            // IMPORTANT
            // we must now update the node snapshot to reflect the changes, this
            // will then be used to remove any nodes
            $node = $this->getNode($type, $identifier);
        }

        // now it's time to remove the node/nodes
        $query = 'DELETE FROM ' . dbTable('tree') . ' ' .
                 'WHERE ' . dbName('lft') . ' >= ' . $node['lft'] . ' ' .
                 'AND ' . dbName('rgt') . ' <= ' . $node['rgt'];
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('REMOVE NODE FAILED');
        }

        // close the hole in the tree
        $this->fillHole($node['lft'], $node['rgt']);

        // all done!
    }

    /**
     * Moves a node from its existing position in a tree to a new position. Note
     * that the sub nodes will retain their relationship with the node being 
     * moved.
     *
     * @param String $type Type of node to move
     * @param String $identifier Node ID
     * @param String $newParentType Type of parent node to move to
     * @param String $newParentIdentifier Parent node ID to move to
     */
    public function moveNode($type, $identifier, $newParentType,
                                                         $newParentIdentifier) {

        // get the node to move
        $node = $this->getNode($type, $identifier);
        if (!$node) {
            throw new WException('NODE DOES NOT EXIST', $type, $identifier,
                                                                  $this->group);
        }

        // get the new parent node
        $parentNode = $this->getNode($newParentType, $newParentIdentifier);
        if (!$parentNode) {
            throw new WException('NODE DOES NOT EXIST', $type, $identifier,
                                                                  $this->group);
        }

        // make a hole in the tree
        $branchNodes = (int)(($node['rgt'] - $node['lft'] + 1) / 2);
        $parentNode['rgt'] = $this->makeHole($parentNode['rgt'], $branchNodes);

        // get DBO
        $db = JFactory::getDBO();

        // move the node and sub nodes
        $offset = ($parentNode['rgt'] - $node['rgt']) - 1;
        $query = 'UPDATE ' . dbTable('tree') . ' ' .
                 'SET ' . dbName('rgt') . ' = ' . dbName('rgt') . ' + ' . $offset .
                 ', ' . dbName('lft') . ' = ' . dbName('lft') . ' + ' . $offset . ' ' .
                 'WHERE ' . dbName('rgt') . ' <= ' . $node['rgt'] . ' ' .
                 'AND ' . dbName('lft') . ' >= ' . $node['lft'] . ' ' .
                 'AND ' . dbName('grp') . ' = ' . $db->Quote($this->group);
        $db->setQuery($query);
        if (!$db->query()) {
            throw new WException('COULD NOT UPDATE TREE', $db->getErrorMsg());
        }

        // close gap in the tree
        $this->fillHole($node['lft'], $node['rgt']);

        // all done!
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
        // create unique key
        $key = $type . '#' . $identifier;

        // process data if not already accounted for
        if (!array_key_exists($key, $this->nodeExists)) {
            $db = JFactory::getDBO();
            $query = 'SELECT COUNT(' . dbName('identifier'). ') AS count ' .
                     'FROM ' . dbTable('tree') . ' ' .
                     'WHERE ' . dbName('grp'). ' = ' . $db->Quote($this->group) .
                     '  AND ' . dbName('type'). ' = ' . $db->Quote($type) .
                     '  AND ' . dbName('identifier'). ' = ' . $db->Quote($identifier);
            $db->setQuery($query);
            $this->nodeExists[$key] = (boolean)$db->loadResult();
        }

        // return the result
        return $this->nodeExists[$key];
    }

    /**
     * Gets a node from the database. The retruned array is only a snapshot so
     * will not reflect any changes made to the database. If the specified node
     * does not exist, returns null.
     *
     * @param String $type
     * @param String $identifier
     * @return [array] Associative array that represents a node at the time of retrival
     */
    public function getNode($type, $identifier) {
        // create unique key
        $key = $type . '#' . $identifier;

        // check cache in case we already know it does not exist
        if (array_key_exists($key, $this->nodeExists)) {
            if ($this->nodeExists[$key] == false) {
                return null;
            }
        }

        // get the node data, note we cannot cache this because of update issues
        $db = JFactory::getDBO();
        $query = 'SELECT * ' .
                 'FROM ' . dbTable('tree') . ' ' .
                 'WHERE ' . dbName('grp'). ' = ' . $db->Quote($this->group) .
                 '  AND ' . dbName('type'). ' = ' . $db->Quote($type) .
                 '  AND' . dbName('identifier'). ' = ' . $db->Quote($identifier);
        $db->setQuery($query);

        // process the result and return it
        $res = $db->loadAssoc();
        $this->nodeExists[$key] = (boolean)$res;
        return $res ? $res : null;
    }

    /**
     * Gets the path from a node to the root node. This is represented as an
     * array of arrays. Each inner array includes the key pair values, type and
     * identifier. The outter array is order from the leaf node to the root
     * node.
     * 
     * @param string $type
     * @param string $identifier
     * @return array
     */
    public function getNodePath($type, $identifier) {
        $db = JFactory::getDBO();
        $query = 'SELECT ' . dbName('tree.type') . ', '  . dbName('tree.identifier')
               . ' FROM ' . dbTable('tree') . ' AS ' . dbName('tree')
               . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('node')
               . ' WHERE ' . dbName('tree.grp') . ' = ' . $db->Quote($this->group)
               . ' AND ' . dbName('node.grp') . ' = ' . $db->Quote($this->group)
               . ' AND ' . dbName('node.type') . ' = ' . $db->Quote($type)
               . ' AND ' . dbName('node.identifier') . ' = ' . $db->Quote($identifier)
               . ' AND ' . dbName('tree.lft') . ' <= ' . dbName('node.lft')
               . ' AND ' . dbName('tree.rgt') . ' >= ' . dbName('node.rgt')
               . ' ORDER BY ' . dbName('tree.lft') . ' DESC';
        $db->setQuery($query);

        // return the result!
        return $db->loadAssocList();
    }

    /**
     * Adds a new type access database
     *
     * @param <type> $group
     * @param <type> $type
     * @param <type> $description
     * @return <type>
     * @throws WException
     */
    public function addType($type, $description='') {
        // preliminary check
        if ($this->typeExists($type)) {
            // it's OK, the type already exists
            return;
        }

        $db = JFactory::getDBO();

        // prepare query
        $query = 'INSERT INTO ' . dbTable('tree_types') . ' ' .
                 'SET ' . dbName('grp'). ' = ' . $db->Quote($this->group).
                 ', ' . dbName('type'). ' = ' . $db->Quote($type) .
                 ', ' . dbName('description'). ' = ' . $db->Quote($description);
        $db->setQuery($query);

        // execute query
        if (!$db->query()) {
            throw new WException('ADD TYPE FAILED', $db->getErrorMsg());
        }
    }

    /**
     * Determines if a specified type exists for the current session group.
     *
     * @param String $type Type to check for
     * @return boolean
     */
    public function typeExists($type) {
        // initialise type cache
        if ($this->cache == null) {
            $db =& JFactory::getDBO();

            // prepare query
            $query = 'SELECT ' . dbName('type') . ' '.
                     'FROM ' . dbTable('tree_types') . ' '.
                     'WHERE ' . dbName('grp') . ' = ' . $db->Quote($this->group);
            $db->setQuery($query);

            // populate cache
            $this->cache = $db->loadResultArray(0);
        }

        // do the business
        // type must be restricted to 100 characters as per the database setup
        // note we only deal with UTF-8 compatible MySQL servers, hence characters not bytes
        $type = JString::substr($type, 0, 100);
        return in_array($type, $this->cache);
    }
}
?>