<?php
/**
 * @version $Id: list.php 122 2009-05-29 14:49:37Z webamoeba $
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
class DocumentcontainerEditWController extends DocumentcontainerWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('edit');
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

        // make sure the record isn;t already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('Document container has been checked out by another user');
            JError::raiseWarning('500', 'WHD DOCUMENT CONTAINER IS ALREADY CHECKED OUT');
            return;
        }

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $table->checkIn();
                JRequest::setVar('task', 'documentcontainer.display.start');
                return;
                break;
            case 'save':
            case 'apply':
                // attempt to save
                if ($this->commit()) {
                   JError::raiseNotice('INPUT', JText::_('WHD DOCUMENT CONTAINER SAVED'));
                   if ($stage == 'save') {
                       JRequest::setVar('id',   $table->id);
                       JRequest::setVar('task', 'documentcontainer.display.start');
                       // check in the table record
                       $table->checkin();
                       return;
                   }/* else {
                       JRequest::setVar('task', 'documentcontainer.edit.start');
                       // @todo set request id
                   }

                   return;*/
                } else {
                    JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                    foreach($table->getErrors() AS $error) {
                        JError::raiseNotice('INPUT', $error);
                    }
                }
        }

        // get the model
        $model = WModel::getInstanceByName('documentcontainer');

        // get the parents
        $parents = $model->getParents($table->id);

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('documentcontainer', 'form', $format);

        // add the deafult model, the container
        $view->addModel('container', $table, true);

        // add the creator to the view
        $view->addModel('creator', JFactory::getUser($table->creator));

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the parents to the view
        $view->addModel('parents', $parents);

        // check out the table record
        $table->checkOut(JFactory::getUser()->id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
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