<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'document.php');

/**
 * 
 */
class DocumentDeleteWController extends DocumentWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('delete');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        // get the table
        $table = WFactory::getTable('document');
        $table->load($this->getAccessTargetIdentifier());

        // no need to check acess until now because we need to load the 
        // data before we can safely move on
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT DELETE ACCESS DENIED');
            JRequest::setVar('id', $table->parent);
            return;
        }

        // make sure the record isn't already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('Document has been checked out by another user');
            JError::raiseWarning('500', 'WHD DOCUMENT IS ALREADY CHECKED OUT');
            return;
        }

        // attempt to delete the node and document
        // note that the treeSession will deal with the document delete as well
        // as the node delete
        $treeSession = WTree::getInstance()->getSession('component');
        try {
            $treeSession->removeNode('document', $table->id, true);
            JRequest::setVar('id', $table->parent);
        } catch (Exception $e) {
            JError::raiseWarning('WHD DELETE DOCUMENT FAILED');
        }

        // move into the parent container
        JRequest::setVar('task', 'documentcontainer.display');
        JRequest::setVar('id',   $table->parent);
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['parent']);
        unset($post['payload']);
        unset($post['bytes']);
        unset($post['mime_type']);

        return parent::commit($post);
    }

    /**
     *
     * @return int
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return WModel::getId();
    }
}

?>