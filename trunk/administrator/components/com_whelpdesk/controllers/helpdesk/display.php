<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'helpdesk.php');

class HelpdeskDisplayWController extends HelpdeskWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * Displays the control panel
     */
    public function execute() {
        parent::execute();
        $this->display();
    }
}

?>