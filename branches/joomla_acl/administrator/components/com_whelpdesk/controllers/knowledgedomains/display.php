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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomains.php');

/**
 * 
 */
class KnowledgedomainsDisplayWController extends KnowledgedomainsWController {

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
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAINS DISPLAY ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstance('knowledgedomain');

        // get the list data
        $knowledgeDomains = $model->getList();

        // get the filters
        $filters = $model->getFilters();
        
        // check if we should show the create button
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canList     = false;
        try {
            $canList = $accessSession->hasAccess('user', $user->get('id'),
                                                 'knowledgedomains', 'knowledgedomains',
                                                 'knowledgedomains', 'list');
        } catch (Exception $e) {
            $canList = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('knowledgedomains', 'display', $format);

        // add the default model to the view
        $view->addModel('knowledgedomains', $knowledgeDomains, true);

        // add the filters to the view
        $view->addModel('filters', $filters);
        
        // add the boolean value describing access to list knowledgedomains
        $view->addModel('canList', $canList);

        // add the total number of terms to the view
        $view->addModel('total', $model->getTotal());

        // display the view!
        $this->display();
    }
}

?>