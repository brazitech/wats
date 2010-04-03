<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');
jimport('joomla.utilities.date');

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'documentcontainer.php');

/**
 * 
 */
class DocumentcontainerDeleteWController extends DocumentcontainerWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('delete');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT CONTAINER EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('documentcontainer');
        $table->load($this->getAccessTargetIdentifier());

        // make sure no one is playing silly buggers... we cannot delete the root document container
        if ($table->id == 1) {
            JError::raiseWarning(403, 'WHD YOU CANNOT DELETE THE ROOT DOCUMENT CONTAINER');
            JRequest::setVar('task', 'documentcontainer.display.start');
            return;
        }

        // make sure the record isn't already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('Document container has been checked out by another user');
            JError::raiseWarning('500', 'WHD DOCUMENT CONTAINER IS ALREADY CHECKED OUT');
            return;
        }

        // attempt to delete the node and all sub nodes
        $treeSession = WTree::getInstance()->getSession('component');
        try {
            $treeSession->removeNode('documentcontainer', $table->id, true);
            JRequest::setVar('id', $table->parent);
        } catch (Exception $e) {
            JError::raiseWarning('WHD DELETE DOCUMENT CONTAINER FAILED');
        }

        // diplsay the parent container on success or the original on fail
        JRequest::setVar('task', 'documentcontainer.display.start');
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // make sure ID and the parent are integers
        $post['id']     = intval($post['id']);
        $post['parent'] = intval($post['parent']);

        // set the updated date and time to now
        $now = new JDate();
        $post['modified'] = $now->toMySQL();

        return parent::commit($post);
    }

}

?>