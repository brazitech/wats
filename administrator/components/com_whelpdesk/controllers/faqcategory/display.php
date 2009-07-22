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

class FaqcategoryDisplayWController extends FaqcategoryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD FAQ CATEGORY DISPLAY ACCESS DENIED');
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

        // get the model
        $model = WModel::getInstance('faqcategory');

        // get the view
        $document = JFactory::getDocument();
		$format   = strtolower($document->getType());
        $view     = WView::getInstance('faqcategory', 'display', $format);

        // add the default model to the view
        $view->addModel('faqcategory', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the faqs
        $view->addModel('faqs', $model->getFaqs($table->id));

        // display the view!
        JRequest::setVar('view', 'display');
        $this->display();
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