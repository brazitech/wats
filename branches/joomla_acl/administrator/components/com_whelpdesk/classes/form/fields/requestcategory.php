<?php
/**
 * @version		$Id$
 * @package     helpdesk
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
wimport('database.query');

/**
 *
 *
 */
class JFormFieldRequestCategory extends JFormField
{

    /**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	public function getInput()
	{
        if (!$this->value)
        {
            return JText::_('WHD_RC:UNKNOWN');
        }

        $db		= &JFactory::getDbo();
		$query	= new WDatabaseQuery();

		$query->select('c.name');
		$query->from(dbTable('request_categories').' AS c');
        $query->where('c.id = '.(int)$this->value);

		// Get the options.
		$db->setQuery($query);
		$requestCategory = $db->loadObject();

		$size =((string)$this->_element->attributes()->size) ? ' size="'.$this->_element->attributes()->size.'"' : '';
		$class =((string)$this->_element->attributes()->class) ? ' class="'.$this->_element->attributes()->class.'"' : ' class="text_area"';

		return '<input type="text" '.$class.$size.' value="'.htmlspecialchars($requestCategory->name, ENT_COMPAT, 'UTF-8').'" readonly="readonly" disabled="disabled"/><input type="hidden" name="'.$this->inputName.'" id="'.$this->inputId.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" /></span>';
	}
}