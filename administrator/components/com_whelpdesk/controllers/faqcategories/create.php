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

        // get the model and a new category
        $model = WModel::getInstanceByName('faqcategory');
        $category = $model->getCategory(0);

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // before saving or applying the new category, make sure the token is valid
            shouldHaveToken();

            // attempt to save
            $id = $this->commit();
            if ($id !== false) {
               WMessageHelper::message(JText::sprintf('WHD_FAQCATEGORY:SAVED CATEGORY %s', JRequest::getString('name')));
               if ($stage == 'save') {
                   // return to the list
                   JRequest::setVar('task', 'faqcategories.list.start');
               } else {
                   // goto the edit page
                   JRequest::setVar('task', 'faqcategory.edit.start');
                   JRequest::setVar('id',   $id);
               }
               // no need to continue we will now be going to the list or edit page
               return;
            }
        }

        // get the view
        $view = WView::getInstance(
            'faqcategories',
            'form',
            strtolower(
                JFactory::getDocument()->getType()
            )
        );

        // add the default model to the view
        $view->addModel('faqcategory', $category, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $category->getFieldset());
        $view->addModel('fieldset-data', $category);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    /**
     * Commits a new FAQ category to the database. This method extracts data
     * from the POST request. Note that it also removes ID from the data so as
     * to ensure we don't update an existing FAQ category instead of creating a
     * new one.
     *
     * @return boolean
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        return parent::commit(0, $post);
    }
}

?>