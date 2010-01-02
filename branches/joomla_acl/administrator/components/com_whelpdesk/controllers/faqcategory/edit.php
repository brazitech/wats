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

        // get the ID
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'faqcategories.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY UNKNOWN'));
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('faqcategory');
        $category = $model->getCategory($id);

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'faqcategories.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the term, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $id = $this->commit($id);
                if ($id !== false) {
                   // successfully saved changes
                   WMessageHelper::message(JText::sprintf('WHD_FAQCATEGORY:UPDATED CATEGORY %s', JRequest::getString('name')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'faqcategories.list.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'faqcategory.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                }
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('faqcategory', 'form', $format);

        // add the default model to the view
        $view->addModel('faqcategory', $category, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $category->getFieldset());
        $view->addModel('fieldset-data', $category);

        // check out the table record
        $category->checkOut(JFactory::getUser()->id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($id) {
        // values to use to edit record
        $post = JRequest::get('POST');

        // commit the changes
        return parent::commit($id, $post);
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