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

class WListFilterRequestCategory extends WListFilter
{

    public function  __construct($node, WList $list)
    {
        parent::__construct($node, $list);
    }

    protected function getOptions()
    {
        $db		= &JFactory::getDbo();
		$query	= new WDatabaseQuery();

		$query->select('c.id AS value, c.name AS text, c.level');
		$query->from(dbTable('request_categories').' AS c');
        $query->where('c.parent_id > 0');
		$query->order('c.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 1, $n = count($options); $i < $n; $i++) {
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		$options = array_merge(parent::_getOptions(), $options);

		return $options;
    }

    public function render()
    {
        return JHtml::_(
			'select.genericlist',
			$this->getOptions(),
			$this->_id,
			array(
				'list.attr'   => 'class="inputbox" size="1" onchange="submitform();"',
				'list.select' => $this->getConditionValue(),
				'option.key'  => $this->_optionKey,
                'option.text' => $this->_optionText
			)
		);
    }
}
