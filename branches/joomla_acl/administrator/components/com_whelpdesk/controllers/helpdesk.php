<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

abstract class HelpdeskWController extends WController {

    public function __construct() {
        $this->setType('helpdesk');
    }

    /**
     * Helpdesk controller, execute usecase
     */
    public function execute() {
        if (!$this->hasAccess()) {
            JError::raiseError('401', 'WHD HELPDESK DISPLAY ACCESS DENIED');
            jexit();
        }
    }
}

?>