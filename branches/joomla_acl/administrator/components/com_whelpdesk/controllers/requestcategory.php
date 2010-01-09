<?php
/**
 * @version $Id: glossary.php 207 2010-01-02 14:23:37Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.controller');
wimport('application.model');

abstract class RequestcategoryWController extends WController {

    public function __construct() {
        $this->setType('requestcategory');
    }

}

?>