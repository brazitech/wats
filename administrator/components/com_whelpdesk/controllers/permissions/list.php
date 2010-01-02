<?php
/**
 * @version $Id: list.php 122 2009-05-29 14:49:37Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * 
 */
class PermissionsListWController extends WController {

    public function __construct() {
        $this->setType('permissions');
        $this->setUsecase('list');
    }

    /**
     * 
     */
    public function execute($stage) {
        // get the bells and whistles
        $targetIdentifierAlias = base64_decode(JRequest::getVar('targetIdentifierAlias', '', 'REQUEST', 'BASE64'));
        $returnURI = base64_decode(JRequest::getVar('returnURI', '', 'REQUEST', 'BASE64'));

        // get the target
        $targetType       = JRequest::getString('targetType');
        $targetIdentifier = JRequest::getString('targetIdentifier');

        // get the model
        $model = WModel::getInstanceByName('permissions');
        $model->setTargetIdentifier($targetIdentifier);
        $model->setTargetType($targetType);

        // get the list data
        $rules = $model->getList();

        // get the filters
        $filters = $model->getFilters();

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('permissions', 'list', $format);

        // add the default model to the view
        $view->addModel('rules', $rules, true);

        // add the filters to the view
        $view->addModel('filters', $filters);

        // add the pagination data to the view
        $view->addModel('paginationTotal',      $model->getTotal());
        $view->addModel('paginationLimit',      $model->getLimit());
        $view->addModel('paginationLimitStart', $model->getLimitStart());

        // add traget details
        $view->addModel('targetType',       $targetType);
        $view->addModel('targetIdentifier', $targetIdentifier);
        $view->addModel('targetIdentifierAlias', $targetIdentifierAlias);
        $view->addModel('returnURI',        $returnURI);

        // display the view!
        $this->display();
    }
}

?>