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
    /**
     * Get the condition to add to the query
     */
    public function getCondition()
    {
        $value = $this->getConditionValue();

        if ($value === false)
        {
            return false;
        }

        // prepapre the value.
        $db = JFactory::getDbo();
        $value = $db->getEscaped($value, true);
        $value = $db->Quote('%'.$value.'%', false);

        $conditions = array();
        foreach ($this->_columns AS $column)
        {
            $conditions[] = dbName($column) . ' LIKE ' . $value;
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }

    public function render()
    {
        // Get the reset JavaScript for the other fields.
        $reset = '';
        foreach ($this->_list->getFilters() AS $filter)
        {
            $reset .= $filter->renderReset();
        }

        $html = parent::render();
        $html .= '&nbsp;<button onclick="this.form.submit();">'.JText::_("GO").'</button>';
        $html .= '&nbsp;<button onclick="'.$reset.'this.form.submit();">';
        $html .= JText::_("RESET");
        $html .= '</button>';

        return $html;
    }
}
