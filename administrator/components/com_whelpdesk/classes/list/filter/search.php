<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('list.filter');

class WListFilterSearch extends WListFilter
{
    public function render()
    {
        $html = parent::render();
        $html .= '&nbsp;<button onclick="this.form.submit();">'.JText::_("GO").'</button>';
        $html .= '&nbsp;<button onclick="document.getElementById(\''.$this->_id.'\').value=\'\'; this.form.submit();">';
        $html .= JText::_("RESET");
        $html .= '</button>';

        return $html;
    }
}
