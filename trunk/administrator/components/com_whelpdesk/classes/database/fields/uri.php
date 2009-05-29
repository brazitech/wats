<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

class UriWField extends WField {

    static $uriPattern = "#(?:(?:http)://(?:(?:(?:(?:(?:(?:[a-zA-Z0-9][-a-zA-Z0-9]*)?[a-zA-Z0-9])[.])*(?:[a-zA-Z][-a-zA-Z0-9]*[a-zA-Z0-9]|[a-zA-Z])[.]?)|(?:[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+)))(?::(?:(?:[0-9]*)))?(?:/(?:(?:(?:(?:(?:(?:[a-zA-Z0-9\-_.!~*'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\-_.!~*'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*)(?:/(?:(?:(?:[a-zA-Z0-9\-_.!~*'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\-_.!~*'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*))*))(?:[?](?:(?:(?:[;/?:@&=+$,a-zA-Z0-9\-_.!~*'()]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)))?))?)#";

    public function __construct($definition) {
        parent::__construct($definition);
    }

    public function addToTable($table) {
        $db = JFactory::getDBO();
    }

    public function isValid($value) {
        // check if a value has been provided if it is required
        if ($this->params->required && strlen($value) == 0) {
            $this->setError(JText::sprintf('%s IS REQUIRED', $this->getLabel()));
            return false;
        }

        if (strlen($value) && !preg_match(self::$uriPattern, $value)) {
            $this->setError(JText::sprintf('%s VALUE %s IS NOT A VALID URI', $this->getLabel(), $value));
            return false;
        }
        
        return true;
    }

    public function getHTML_FormElement($value=null) {
        return '<input type="text" '
                    . 'name="' . $this->getName() . '" '
                    . 'value="' . $value . '" '
                    . 'maxlength="400" '
                    . 'size="30"'
                    . 'title="' . $this->getLabel() . '" />';
    }

}
