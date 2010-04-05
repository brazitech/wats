<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');
wimport('application.controller');

class RequestCategoryListWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('list');
        $this->setType('requestcategory');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('requestcategory');

        // get the view
        $view = WView::getInstance(
            'requestcategory',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // get the list data add to the view
        $list = $model->getList();
        $view->addModel('requestcategories', $list, true);

        // display the view!
        $this->display();
    }
}
