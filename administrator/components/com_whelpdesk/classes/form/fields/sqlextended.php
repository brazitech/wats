<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

jimport('joomla.form.formfield');
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'fields'.DS.'sql.php';

class JFormFieldSqlExtended extends JFormFieldSQL
{
    /**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'SqlExtended';

	public function _getInput()
    {
        $defaultQuery = (string)$this->_element->attributes()->default_query;
        if ($this->value == null && $defaultQuery)
        {
            $db = JFactory::getDbo();
            $db->setQuery($defaultQuery);
            $key = ((string)$this->_element->attributes()->key_field) ? (string)$this->_element->attributes()->key_field : 'value';

            $this->value = $db->loadObject()->$key;
        }

        $input =  parent::_getInput();

        if ($this->value == null && $defaultQuery)
        {
            $this->value = null;
        }

        return $input;
    }
}

