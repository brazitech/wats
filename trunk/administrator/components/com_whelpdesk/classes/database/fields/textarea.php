<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

class TextareaWField extends WField {

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
        return '<textarea name="' . $this->getName() . '" '
             .           'class="text_area" '
             .           'rows="' . (($this->params->rows) ? $this->params->rows : '5') . '" '
             .           'cols="' . (($this->params->cols) ? $this->params->cols : '30') . '">'
             .     $value
             . '</textarea>';
    }

}
