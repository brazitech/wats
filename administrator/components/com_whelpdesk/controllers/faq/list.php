<?php
/**
 * @version $Id: list.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faq.php');

/**
 * 
 */
class FaqListWController extends FaqWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('list');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        if (!$this->hasAccess('faqcategories', 'faqcategories', 'list', 'faq')) {
            // try the next control
            $accessSession = WFactory::getAccessSession();
            $controlPath = $accessSession->getControlPath();
            if (count($controlPath) > 1) {
                $nextControl = $controlPath[1];
                JRequest::setVar('task', $nextControl['type'] . '.' . $nextControl['identifier']);
            }

            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD FAQ LIST ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstance('faq');

        // get the list data
        $list = $model->getList();
        
        // check if we should show the state buttons
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'faqcategories', 'faqcategories',
                                                        'faq', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        
        // check if we should show the edit button
        $canEdit = false;
        try {
            $canEdit = $accessSession->hasAccess('user', $user->get('id'),
                                                 'faqcategories', 'faqcategories',
                                                 'faq', 'edit');
        } catch (Exception $e) {
            $canEdit = false;
        }
        
        // check if we should show the create button
        $canCreate = false;
        try {
            $canCreate = $accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategories', 'faqcategories',
                                                   'faq', 'create');
        } catch (Exception $e) {
            $canCreate = false;
        }
        
        // check if we should show the delete button
        $canDelete = false;
        try {
            $canDelete = $accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategories', 'faqcategories',
                                                   'faq', 'delete');
        } catch (Exception $e) {
            $canDelete = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('faq', 'list', $format);

        // add the default model to the view
        $view->addModel('faqs', $list, true);
        
        // add the boolean value describing access to change state
        $view->addModel('canCreate', $canCreate);
        $view->addModel('canEdit', $canEdit);
        $view->addModel('canDelete', $canDelete);
        $view->addModel('canChangeState', $canChangeState);

        // add the total number of terms to the view
        $view->addModel('total', $model->getTotal());

        // get the filters
        $view->addModel('filters', $model->getFilters());

        // display the view!
        $this->display();
    }
}

?>