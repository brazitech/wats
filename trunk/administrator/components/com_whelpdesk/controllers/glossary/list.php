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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * 
 */
class GlossaryListWController extends GlossaryWController {

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
            JError::raiseWarning('401', 'WHD GLOSSARY LIST ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstance('glossary');

        // get the list data
        $terms = $model->getList();

        // get the filters
        $filters = $model->getFilters();
        
        // check if we should show the state buttons
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        
        // check if we should show the edit button
        $canEdit = false;
        try {
            $canEdit = $accessSession->hasAccess('user', $user->get('id'),
                                                 'glossary', 'glossary',
                                                 'glossary', 'edit');
        } catch (Exception $e) {
            $canEdit = false;
        }
        
        // check if we should show the create button
        $canCreate = false;
        try {
            $canCreate = $accessSession->hasAccess('user', $user->get('id'),
                                                   'glossary', 'glossary',
                                                   'glossary', 'create');
        } catch (Exception $e) {
            $canCreate = false;
        }
        
        // check if we should show the delete button
        $canDelete = false;
        try {
            $canDelete = $accessSession->hasAccess('user', $user->get('id'),
                                                   'glossary', 'glossary',
                                                   'glossary', 'delete');
        } catch (Exception $e) {
            $canDelete = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('glossary', 'list', $format);

        // add the default model to the view
        $view->addModel('terms', $terms, true);

        // add the filters to the view
        $view->addModel('filters', $filters);
        
        // add the boolean value describing access to change state
        $view->addModel('canCreate', $canCreate);
        $view->addModel('canEdit', $canEdit);
        $view->addModel('canDelete', $canDelete);
        $view->addModel('canChangeState', $canChangeState);

        // add the total number of terms to the view
        $view->addModel('total', $model->getTotal());

        // display the view!
        $this->display();
    }
}

?>