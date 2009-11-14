<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Administrator
 */
interface WTreeSessionInterface {

    /**
     *
     * @param String $group Group ID for this session
     */
    public function __construct($group);

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
                                      $parentType=null, $parentIdentifier=null);

    /**
     * Removes an existing node from the tree. Sub nodes can either be removed
     * simultaneously, or they can be relocated into the parent container.
     *
     * @param String $type Type of node to remove
     * @param String $identifier Node ID
     * @param boolean $recursive delete sub nodes
     */
    public function removeNode($type, $identifier, $recursive=false);

    /**
     *
     * @param String $type Type of node to move
     * @param String $identifier Node ID
     * @param String $newParentType Type of parent node to move to
     * @param String $newParentIdentifier Parent node ID to move tp
     */
    public function moveNode($type, $identifier, $newParentType, $newParentIdentifier);

    /**
     * Determines if a node exists in the tree, uses temporary caching to
     * increase speed.
     *
     * @param String $type Type of node to look for
     * @param String $identifier Node identifier
     * @return <type>
     */
    public function nodeExists($type, $identifier);

    /**
     * Adds a new type. Note that types cannot be removed.
     *
     * @param String $type Type to add, must be unique to group
     * @param String $description Optional description
     */
    public function addType($type, $description='');
}
?>
