<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faq.php');

class FaqEditWController extends FaqWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('edit');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD FAQ EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('faq');
        $table->load($this->getAccessTargetIdentifier());

        // make sure the record isn;t already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('FAQ container has been checked out by another user');
            JError::raiseWarning('500', 'WHD FAQ IS ALREADY CHECKED OUT');
            return;
        }

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $table->checkIn();
                JRequest::setVar('task', 'faq.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // attempt to save
                if ($this->commit()) {
                   JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY SAVED'));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'faq.list.start');
                       $table->checkIn();
                   } else {
                       JRequest::setVar('task', 'faq.edit.start');
                   }

                   $table->revise();
                   return;
                } else {
                    JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                    foreach($table->getErrors() AS $error) {
                        JError::raiseNotice('INPUT', $error);
                    }
                }
        }

        // check if we can allow state changes
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'faq', $table->id,
                                                        'faq', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('faq', 'form', $format);

        // add the default model to the view
        $view->addModel('faq', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the boolean value describing access to change state
        $view->addModel('canChangeState', $canChangeState);

        // check out the table record
        $table->checkOut(JFactory::getUser()->id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // commit the changes
        return parent::commit($post);
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'faqcategory.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD FAQ UNKNOWN'));
        }
        return $id;
    }
}

?>