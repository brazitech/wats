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

class WListFilterState extends WListFilter
{
    public function render()
    {
        return JHTML::_('grid.state', $this->_id);
    }
}
