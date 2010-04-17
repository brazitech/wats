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
 * Lists all of the glossary items. Lists are essentially for management
 * purposes. Only users who are going to edit glossary items should be able to
 * access this controller.
 */
class GlossaryDisplayWController extends GlossaryWController {

    public function __construct() {
        parent::__construct();
        $this->setDefaultView('display');
        $this->setType('glossary');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('glossary');

        // get the view
        $view = WView::getInstance(
            'glossary',
            'display',
            strtolower(JFactory::getDocument()->getType())
        );

        // get the list data add to the view
        $list = $model->getList();
        $view->addModel('glossary', $list, true);

        // display the view!
        $this->display();
    }
}

?>