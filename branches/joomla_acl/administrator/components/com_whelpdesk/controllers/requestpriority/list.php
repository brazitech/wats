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

class RequestPriorityListWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('list');
        $this->setType('requestpriority');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('requestpriority');

        // get the list data and current filters
        $list = $model->getList();
        //$filters = $model->getFilters();

        // get the view
        $view = WView::getInstance(
            'requestpriority',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model and the filters to the view
        $view->addModel('requestpriority', $list, true);

        // add the pagination data to the view
        /*$view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());*/

        // display the view!
        $this->display();
    }
}
