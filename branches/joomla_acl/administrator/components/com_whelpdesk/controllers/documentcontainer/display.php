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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'documentcontainer.php');

/**
 * 
 */
class DocumentcontainerDisplayWController extends DocumentcontainerWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT CONTAINER DISPLAY ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('documentcontainer');
        $table->load($this->getAccessTargetIdentifier());

        // check if we should show the create container button
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canCreate = false;
        try {
            $canCreate = $accessSession->hasAccess('user', $user->get('id'),
                                                   'documentcontainer', $table->id,
                                                   'documentcontainer', 'create');
        } catch (Exception $e) {
            $canCreate = false;
        }
        
        // check if we should show the create document button
        $canCreateDocument = false;
        try {
            $canCreateDocument = $accessSession->hasAccess('user', $user->get('id'),
                                                           'documentcontainer', $table->id,
                                                           'documentcontainer', 'upload');
        } catch (Exception $e) {
            $canCreateDocument = false;
        }

        // check if we should show the edit button
        $canEdit = false;
        try {
            $canEdit = $accessSession->hasAccess('user', $user->get('id'),
                                                 'documentcontainer', $table->id,
                                                 'documentcontainer', 'edit');
        } catch (Exception $e) {
            $canEdit = false;
        }

        // check if we should show the move button
        $canMove = false;
        try {
            $canMove = $accessSession->hasAccess('user', $user->get('id'),
                                                 'documentcontainer', $table->id,
                                                 'documentcontainer', 'move');
        } catch (Exception $e) {
            $canMove = false;
        }

        // check if we should show the delete button
        $canDelete = false;
        try {
            $canDelete = $accessSession->hasAccess('user', $user->get('id'),
                                                   'documentcontainer', $table->id,
                                                   'documentcontainer', 'delete');
        } catch (Exception $e) {
            $canDelete = false;
        }

        // get the model
        $model = WModel::getInstanceByName('documentcontainer');

        // get the sub documentcontainers and documents
        $documentcontainers = $model->getDocumentcontainers($table->id);
        $documents          = $model->getDocuments($table->id);

        // get the parents
        $parents = $model->getParents($table->id);

        // get the filters
        $filters = $model->getFilters();

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('documentcontainer', 'display', $format);

        // add the default model - the container itself
        $view->addModel('container', $table, true);

        // add the creator to the view
        $view->addModel('creator', JFactory::getUser($table->creator));

        // add children to the view
        $view->addModel('documentcontainers', $documentcontainers);
        $view->addModel('documents',          $documents);

        // add the parents to the view
        $view->addModel('parents', $parents);

        // add the filters to the view
        $view->addModel('filters', $filters);

        // add the boolean values describing access
        $view->addModel('canCreate', $canCreate);
        $view->addModel('canCreateDocument', $canCreateDocument);
        $view->addModel('canEdit', $canEdit);
        $view->addModel('canMove', $canMove);
        $view->addModel('canDelete', $canDelete);

        // display the view!
        $this->display();
    }
}

?>