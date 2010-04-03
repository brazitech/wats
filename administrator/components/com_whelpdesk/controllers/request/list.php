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

class RequestListWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setUsecase('list');
        $this->setType('request');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('request');

        // get the list data and current filters
        $list = $model->getList();
        //$filters = $model->getFilters();

        // get the view
        $view = WView::getInstance(
            'request',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model and the filters to the view
        $view->addModel('request', $list, true);

        // add the pagination data to the view
        /*$view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());*/

        // display the view!
        $this->display();
    }
}

?>