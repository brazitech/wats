<?php
/**
 * @version $Id: create.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

/**
 * Get parent class
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategories.php');

class FaqcategoriesCreateWController extends FaqcategoriesWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('create');
    }

    /**
     * Create a new knowledge domain. Valid stages are "start", "save" and 
     * "apply". Note that "save" and "apply" are essentially the same except 
     * that "apply" redirects to the edit page, whereas "save" redirects to the 
     * list page.
     *
     * @param string @stage Stage at which the usecase is to be executed
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD FAQ CATEGORIES CREATE ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('faqcategory');

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // attempt to save
            if ($this->commit()) {
               JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY SAVED'));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'faqcategories.list.start');
               } else {
                   JRequest::setVar('task', 'faqcategories.edit.start');
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
        $view     = WView::getInstance('faqcategories', 'form', $format);

        // add the default model to the view
        $view->addModel('faqcategory', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    /**
     * Commits a new knowledge domain to the database. This method extracts data
     * from the POST request. Note that it also removes ID from the data so as
     * to ensure we don't update an existing KD instead of creating a new one.
     *
     * @return boolean
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        return parent::commit($post);
    }
}

?>