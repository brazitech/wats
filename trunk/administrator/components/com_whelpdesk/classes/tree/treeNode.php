<?php
/**
 * @version $node$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

define('WHD_TREE_NODE_CLASS_PATH', JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'tree' . DS . 'node');

wimport('exceptions.notimplemented');

abstract class TreeNode {

    private static $instances = array();

    public function getInstance($type) {
        $type = ucfirst(strtolower($type));

        // check cache
        if (!array_key_exists($type, self::$instances)) {

            // build class name and load if necessary/if we can
            $class = $type . 'TreeNode';
            if (!class_exists($class)) {
                // class does not exist look for it in the expected file
                $file = WHD_TREE_NODE_CLASS_PATH . DS . strtolower($type) . '.php';
                if (JFile::exists($file)) {
                    require_once($file);
                }
            }

            // check for class and create it, else assume it is not suported
            if (class_exists($class)) {
                self::$instances[$type] = new $class();
            } else {
                self::$instances[$type] = null;
            }
        }

        // all done!
        return self::$instances[$type];
    }

    /**
     * Method which is called when a tree item of this type is being moved
     *
     * @param mixed  $node             Identifier of the node that is being moved
     * @param string $parentType       Node type of the new parent node
     * @param mixed  $parentIdentifier Node identifier of the new parent node
     */
    public function move($node, $parentType, $parentIdentifier) {
        throw new WNotImplementedException();
    }

    public function canDelete($node) {
        return true;
    }

    public function readyToDelete($node) {
       throw new WNotImplementedException();
    }

    /**
     * Method which is called when a tree item of this type is deleted
     *
     * @param mixed $node
     */
    public function delete($node) {
        throw new WNotImplementedException();
    }

    public function redirectOnDeleteSuccess($node) {
        throw new WNotImplementedException();
    }

    public function redirectOnDeleteFail($node) {
         throw new WNotImplementedException();
    }

}