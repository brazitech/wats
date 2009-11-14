<?php
/**
 * @version $Id: list.php 132 2009-06-17 16:42:38Z webamoeba $
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
 * Lists all of the glossary items. Lists are essentially for management
 * purposes. Only users who are going to edit glossary items should be able to
 * access this controller.
 */
class GlossaryDisplayWController extends GlossaryWController {

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
            JError::raiseWarning('401', JText::_('WHD_GLOSSARY:DISPLAY ACCESS DENIED'));
            return;
        }

        // get the model
        $model = WModel::getInstance('glossary');

        // get the list data and current filters
        $terms = $model->getDisplayList();
        
        // check if we should show the list button
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canList = false;
        try {
            $canList = $accessSession->hasAccess('user', $user->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'list');
        } catch (Exception $e) {
            $canList = false;
        }

        // get the view
        $view = WView::getInstance(
            'glossary',
            'display',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model to the view
        $view->addModel('terms', $terms, true);
        
        // add the boolean values describing permissions
        $view->addModel('canList', $canList);

        // add the pagination data to the view
        $view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());

        // display the view!
        $this->display();
    }
}

?>