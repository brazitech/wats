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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomains.php');

/**
 * 
 */
class KnowledgedomainsListWController extends KnowledgedomainsWController {

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
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAINS LIST ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('knowledgedomain');

        // get the list data
        $knowledgeDomains = $model->getList();

        // get the filters
        $filters = $model->getFilters();
        
        // check if we should show the create button
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canCreate     = false;
        try {
            $canCreate = $accessSession->hasAccess('user', $user->get('id'),
                                                   'knowledgedomains', 'knowledgedomains',
                                                   'knowledgedomains', 'create');
        } catch (Exception $e) {
            $canCreate = false;
        }

        // get the custom fields that can be displayed
        wimport('database.fieldset');
        $customFields = WFieldset::getInstance('knowledge_domain')->getListFields();

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('knowledgedomains', 'list', $format);

        // add the default model to the view
        $view->addModel('knowledgedomains', $knowledgeDomains, true);

        // add the filters to the view
        $view->addModel('filters', $filters);

        // add the custom fields to the view
        $view->addModel('customFields', $customFields);
        
        // add the boolean value describing access to create knowledgedomain
        $view->addModel('canCreate', $canCreate);

        // add the pagination data to the view
        $view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());

        // display the view!
        $this->display();
    }
}

?>