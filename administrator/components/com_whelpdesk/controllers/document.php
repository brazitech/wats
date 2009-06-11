<?php
/**
 * @version $Id: glossary.php 120 2009-05-22 14:05:02Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

abstract class DocumentWController extends WController {

    public function __construct() {
        $this->setType('document');
    }

}
