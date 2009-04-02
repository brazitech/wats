<?php
/**
 * Help Desk for Joomla!
 * www.webamoeba.co.uk
 */

defined("_JEXEC") or die("");

/**
 * Objects that implement this interface deal with access control based on the
 * component tree. This is a session based interface because it deals with just
 * one group per instance. The group corresponds to the tree groups.
 *
 * @author Administrator
 */
interface AccessSessionInterface {

    /**
     * Instantiates a new AccessSessionInterface for the specified group. Note
     * that the group never changes during the life of the object.
     *
     * @param String $group
     */
    public function __construct($group);

    /**
     * Sets access to a target node from a request node for a specific type of 
     * control. This enables the ability to explicitly allow ($hasAccess == true)
     * and deny ($hasAccess == false) access.
     *
     * @param String $requestType
     * @param String $requestIdentifier
     * @param String $targetType
     * @param String $targetIdentifier
     * @param String $type
     * @param String $control
     * @param boolean $hasAccess
     */
    public function setAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control,
                              $hasAccess);

    /**
     * Clears any explicit existing access between a target and request node.
     * $control can be used to determine which access to clear. If $control == 
     * null, all access will be cleared. Note that clearing access is not the 
     * same as revoking access. Access may still be granted based on 
     * inheritance from the tree.
     *
     * @param String $requestType
     * @param String $requestIdentifier
     * @param String $targetType
     * @param String $targetIdentifier
     * @param String $control Optional
     */
    public function clearAccess($requestType, $requestIdentifier,
                                $targetType, $targetIdentifier,
                                $control=null);

    /**
     * Determines if access to a target node by a request node for a specified
     * control is allowed. This is the most important method as this is what
     * ultimatley grants and denys access to parts of the system.
     *
     * @param String $requestType
     * @param String $requestIdentifier
     * @param String $targetType
     * @param String $targetIdentifier
     * @param String $type
     * @param String $control
     */
    public function hasAccess($requestType, $requestIdentifier,
                              $targetType, $targetIdentifier,
                              $type, $control);

    /**
     * Resets all access to the session's group, i.e. literally removes all
     * rules that exist for the group.
     */
    public function resetAccess();

    /**
     * Adds a new control for a specific type of node. Note that the type of
     * control determines the target nodes that the control is associated with.
     * Controls are unique as expressed by [type,control].
     *
     * @param String $type
     * @param String $control
     * @param String $description
     */
    public function addControl($type, $control, $description=null);

    /**
     * Removes a control and all associated access rules.
     *
     * @param String $type
     * @param String $control
     */
    public function removeControl($type, $control);

    /**
     * Gets a list of controls. If type is specified, the list is restricted to
     * controls of that type.
     * 
     * @param String $type 
     */
    public function getControls($type=null);
}
?>
