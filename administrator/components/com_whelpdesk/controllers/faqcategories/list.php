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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategories.php');

/**
 * 
 */
class FaqcategoriesListWController extends FaqcategoriesWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('list');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD LIST FAQ CATEGORIES ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('faqcategory');

        // get the list data
        $categories = $model->getList();

        // get the filters
        $filters = $model->getFilters();
        
        // check if we should show the edit button
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canEdit = false;
        try {
            $canEdit = $accessSession->hasAccess('user', $user->get('id'),
                                                 'faqcategories', 'faqcategories',
                                                 'faqcategory', 'edit');
        } catch (Exception $e) {
            $canEdit = false;
        }
        
        // check if we should show the create button
        $canCreate = false;
        try {
            $canCreate = $accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategories', 'faqcategories',
                                                   'faqcategories', 'create');
        } catch (Exception $e) {
            $canCreate = false;
        }
        
        // check if we should show the delete button
        $canDelete = false;
        try {
            $canDelete = $accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategories', 'faqcategories',
                                                   'faqcategory', 'delete');
        } catch (Exception $e) {
            $canDelete = false;
        }

        // get the custom fields that can be displayed
        wimport('database.fieldset');
        $customFields = WFieldset::getInstance('faq_categories')->getListFields();

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('faqcategories', 'list', $format);

        // add the default model to the view
        $view->addModel('categories', $categories, true);

        // add the filters to the view
        $view->addModel('filters', $filters);

        // add the custom fields to the view
        $view->addModel('customFields', $customFields);
        
        // add the boolean value describing access to change state
        $view->addModel('canCreate', $canCreate);
        $view->addModel('canEdit', $canEdit);
        $view->addModel('canDelete', $canDelete);

        // add the pagination data to the view
        $view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());

        // display the view!
        $this->display();
    }
}

?>