<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * 
 */
class DatagroupsListWController extends WController {

    public function __construct() {
        $this->setType('datagroups');
        $this->setUsecase('list');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        /*if (!$this->hasAccess('faqcategories', 'faqcategories', 'list', 'faq')) {
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
        }*/

        // get the bells and whistles
        $targetIdentifierAlias = base64_decode(JRequest::getVar('targetIdentifierAlias', '', 'REQUEST', 'BASE64'));
        $returnURI = base64_decode(JRequest::getVar('returnURI', '', 'REQUEST', 'BASE64'));

        // get the model
        $fieldModel = WModel::getInstanceByName('datagroup');

        // get the list data
        $fields = $fieldModel->getList();
        print_r($fields);

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
        $view = WView::getInstance('datagroups', 'list', $format);

        // add the default model to the view
        $view->addModel('fields', $fields, true);

        // add the boolean value describing access to change state
        $view->addModel('canCreate', $canCreate);
        $view->addModel('canEdit', $canEdit);
        $view->addModel('canDelete', $canDelete);
        $view->addModel('canChangeState', $canChangeState);

        // add the total number of fields to the view
        $view->addModel('paginationTotal',      $fieldModel->getTotal());
        $view->addModel('paginationLimit',      $fieldModel->getLimit());
        $view->addModel('paginationLimitStart', $fieldModel->getLimitStart());

        // get the filters
        $view->addModel('filters', $fieldModel->getFilters());

        // display the view!
        $this->display();
    }

}

?>