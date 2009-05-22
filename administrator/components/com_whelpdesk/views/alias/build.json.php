<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class AliasJSONWView extends WView {

    public function render() {
        $json        = new stdClass();
        $json->name  = $this->getModel('name');
        $json->alias = $this->getModel('alias');
        
        echo json_encode($json);
    }

}

?>
