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
class JFormFieldTextOnly extends JFormField
{

    /**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'TextOnly';

    /**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function _getInput()
	{
		$class     =((string)$this->_element->attributes()->class) ? ' class="'.$this->_element->attributes()->class.'"' : '';
        $allowHTML =((string)$this->_element->attributes()->html == 'true');

        if ($allowHTML)
        {
            $value = $this->value;
        }
        else
        {
            $value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
        }

		return "<div class=\"whdJFormFieldTextOnlyWrapper\"><div$class>$value</div></div>";
	}
}
