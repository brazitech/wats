<?php
/**
 * @version $Id: list.php 237 2010-04-05 10:18:11Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');
wimport('application.controller');

class AnnouncementListWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('list');
        $this->setType('announcement');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model and the list
        $model = WModel::getInstanceByName('announcement');
        $list = $model->getList();

        // get the view
        $view = WView::getInstance(
            'announcement',
            'list',
            strtolower(JFactory::getDocument()->getType())
        );

        // display the view!
        $view->addModel('announcement', $list, true);
        $this->display();
    }
}
