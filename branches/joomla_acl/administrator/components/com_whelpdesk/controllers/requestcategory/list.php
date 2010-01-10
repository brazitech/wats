<?php
/**
 * @version $Id: edit.php 105 2009-05-04 12:46:26Z webamoeba $
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
        $this->setUsecase('list');
        $this->setType('requestcategory');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('requestcategory');

        // get the list data and current filters
        $list = $model->getList();
        //$filters = $model->getFilters();

        // get the view
        $view = WView::getInstance(
            'requestcategory',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model and the filters to the view
        $view->addModel('requestcategories', $list, true);

        // add the pagination data to the view
        /*$view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());*/

        // display the view!
        $this->display();
    }
}

?>