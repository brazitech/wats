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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledge.php');

/**
 * 
 */
class KnowledgeListWController extends KnowledgeWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('list');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        /*try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD KNOWLEDGE LIST ACCESS DENIED');
            return;
        }*/

        // get the model
        $model = WModel::getInstanceByName('knowledge');

        // get the list data
        $list = $model->getList();

        // get the view
        $view = WView::getInstance(
            'knowledge',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model to the view
        $view->addModel('knowledge', $list, true);
        $view->addModel('knowledgeDomain', WModel::getInstanceByName('knowledgedomain')->getKnowledgeDomain(JRequest::getInt('filterDomain')));

        // add the total number of terms to the view
        $view->addModel('total', $model->getTotal());

        // get the filters
        $view->addModel('filters', $model->getFilters());

        // display the view!
        $this->display();
    }
}

?>