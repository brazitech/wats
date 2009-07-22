<?php
/**
 * @version $Id: edit.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategory.php');

class FaqcategoryEditWController extends FaqcategoryWController {

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
            JError::raiseWarning('401', 'WHD FAQ CATEGORY EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('faqcategory');

        // load the table data
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'faqcategories.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY UNKNOWN'));
            return;
        }
        $table->load($id);

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $table->checkIn();
                JRequest::setVar('task', 'faqcategories.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // attempt to save
                if ($this->commit()) {
                   JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY SAVED'));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'faqcategories.list.start');
                   } else {
                       JRequest::setVar('task', 'faqcategory.edit.start');
                   }

                   return;
                } else {
                    JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                    foreach($table->getErrors() AS $error) {
                        JError::raiseNotice('INPUT', $error);
                    }
                }
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('faqcategory', 'form', $format);

        // add the default model to the view
        $view->addModel('faqcategory', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

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
            JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY UNKNOWN'));
        }
        return $id;
    }
}

?>