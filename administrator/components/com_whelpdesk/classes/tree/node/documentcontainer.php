<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class DocumentcontainerTreeNode extends TreeNode {

    /**
     * Method which is called when a document container is deleted
     * 
     * @param int $id
     */
    public function delete($id) {
        $table = WFactory::getTable('documentcontainer');
        $table->delete($id);
    }

    public function readyToDelete($id) {
        // get the table
        $table = WFactory::getTable('documentcontainer');
        $table->load($this->getAccessTargetIdentifier());

        // make sure no one is playing silly buggers... we cannot delete the root document container
        if ($table->id == 1) {
            WFactory::getOut()->log('Document container is root container, this cannot be deleted');
            throw new WException('WHD YOU CANNOT DELETE THE ROOT DOCUMENT CONTAINER');
        }

        // make sure the record isn't already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('Document container has been checked out by another user');
            throw new WException('WHD DOCUMENT CONTAINER IS ALREADY CHECKED OUT');
        }
    }

    public function canDelete() {
        ;
    }

    public function redirectOnSuccess($id) {
        ;
    }

    public function redirectOnFail($id) {
        JRequest::setVar('task', 'documentcontainer.display.start');
    }

}
