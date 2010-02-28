<?php
/**
 * @version		$Id$
 * @package     helpdesk
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
wimport('database.query');
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'fields'.DS.'list.php';

/**
 *
 *
 */
class JFormFieldRequestCategory extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'RequestCategory';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
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

		$options	= array_merge(
						parent::_getOptions(),
						$options
					);

		return $options;
	}
}