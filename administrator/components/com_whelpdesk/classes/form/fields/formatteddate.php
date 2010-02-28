<?php
/**
 * @version		$Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldFormattedDate extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'FormattedDate';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function _getInput()
	{
		$size =((string)$this->_element->attributes()->size) ? ' size="'.$this->_element->attributes()->size.'"' : '';
		$class =((string)$this->_element->attributes()->class) ? ' class="'.$this->_element->attributes()->class.'"' : ' class="text_area"';

        $value = $this->value;
        $value = JHtml::_('date', $value);

		return '<input type="text" name="'.$this->inputName.'" id="'.$this->inputId.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'"'.$class.$size.'readonly="readonly" />';
	}
}
