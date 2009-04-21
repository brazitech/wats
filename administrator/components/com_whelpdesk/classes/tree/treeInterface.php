<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Administrator
 */
interface WTreeInterface {

    /**
     * Adds a new group, the name of the group must be unique. If the group
     * already exists no changes will occur.
     *
     * @param String $name Name of the new group
     * @param String $description Optional description
     */
    public function addGroup($group, $description=null);

    /**
     * Removes a group. Essentially this is a very simple method, it should
     * remove all refernces from the persitant access data that refernces the
     * group.
     *
     * @param String $name Name of group to remove
     */
    public function removeGroup($group);

    /**
     * Gets a session object for the specified group
     *
     * @param String $group
     * @return TreeSessionInterface
     */
    public function getSession($group);

    /**
     * Gets the global instance of the implemnting class
     *
     * @return TreeInterface
     */
    public function getInstance();
    
}

?>
