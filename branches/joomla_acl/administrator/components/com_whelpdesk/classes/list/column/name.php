<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WListColumnName extends WListColumn
{
    private $_showDefault;

    /**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Name';

    public function  __construct($node, WList $list)
    {
        parent::__construct($node, $list);
        $this->_showDefault = ((string)$node->attributes('showdefault') == 'true');
    }

    /**
     * @param WList $list
     */
    public function renderPlain(WList $list)
    {
        $row = $list->getCurrentRow();

        $name = parent::renderPlain($row);

        if ($this->_showDefault && $row->default)
        {
            $name .= '&nbsp;';
            $name .= '<span class="defaultIcon">';
            $name .= JHTML::_('image', 'menu/icon-16-default.png', JText::_('WHD_DATA:DEFAULT'), array('title' => JText::_('WHD_DATA:DEFAULT')), true);
            $name .= '</span>';
        }

        return $name;
    }
}
