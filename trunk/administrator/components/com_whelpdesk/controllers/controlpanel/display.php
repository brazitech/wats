<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

class ControlpanelDisplayWController extends WController {

    public function __construct() {
        $this->setEntity('controlpanel');
    }

    /**
     * Displays the control panel
     */
    public function execute() {
        $this->display();
    }
}

?>