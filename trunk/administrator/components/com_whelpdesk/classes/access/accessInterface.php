<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined("_JEXEC") or die("");

/**
 * Description of accesInterface.php
 *
 * @author Administrator
 */
interface AccessInterface {

    /**
     * Adds a new group, the name of the group must be unique. If the group
     * already exists no changes will occur. This should only ever really be
     * called by a TreeInterface object. For some implementations no work will
     * actually be required here - depends on the system...
     *
     * @param String $name Name of the new group
     * @param String $description Optional description
     */
    public function addGroup($group, $description=null);

    /**
     * Removes a group. This should only ever be called by a TreeInterface
     * object. Essentially this is a very simplified event, it should remove all
     * refernces from the persitant access data that refernces the group. If the
     * access handler records groups, it will be necessary to remove these.
     *
     * @param String $name Name of group to remove
     */
    public function removeGroup($group);

    /**
     * Gets a session object for the specified group
     *
     * @param String $group
     * @return AccessSessionInterface
     */
    public function getSession($group);

}
?>
