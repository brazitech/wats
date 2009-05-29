<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

class TextWField extends WField {

    public function __construct($definition) {
        parent::__construct($definition);
    }

    public function addToTable($table) {
        $db = JFactory::getDBO();
    }

    public function isValid($value) {
        if (JString::strlen($value) > $this->params->maximumCharacterLength) {
            $this->setError(JText::sprintf('%s VALUE %s IS TOO LONG', $this->getLabel(), $value));
            return false;
        }

        if (JString::strlen($value) < $this->params->minimumCharacterLength) {
            $this->setError(JText::sprintf('%s VALUE %s IS TOO SHORT', $this->getLabel(), $value));
            return false;
        }
        
        return true;
    }

    public function getHTML_FormElement($value=null) {
        return '<input type="text" name="' . $this->getName() . '" '
                    . 'value="' . $value . '" '
                    . 'maxlength="' . intval($this->params->maximumCharacterLength) . '"'
                    . 'size="' . (($this->params->fieldSize) ? intval($this->params->fieldSize) : '35') . '"/>';
    }

}
