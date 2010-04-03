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

class RequestCategoriesListWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setUsecase('list');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        
    }
}

?>