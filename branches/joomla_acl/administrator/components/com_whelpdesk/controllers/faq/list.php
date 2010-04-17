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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faq.php');

/**
 * 
 */
class FaqListWController extends FaqWController {

    public function __construct() {
        parent::__construct();
        $this->setDefaultView('list');
        $this->setType('faq');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('faq');

        // get the view
        $view = WView::getInstance(
            'faq',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // get the list data add to the view
        $list = $model->getList();
        $view->addModel('faq', $list, true);

        // display the view!
        $this->display();
    }
}
