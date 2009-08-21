<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

class TextareaWField extends WField {

    public function __construct($group, $definition) {
        parent::__construct($group, $definition);
    }

    /**
     * Checks if the parameters are valid for this field type (text)
     *
     * @param JParameter params
     */
    public static function check($params) {
        return true;
    }

    public static function addToTable($tableName, $groupName, $field) {
        $db = JFactory::getDBO();
        $db->setQuery(
            'ALTER TABLE ' . dbTable($tableName) .
            ' ADD COLUMN ' . dbName('field_'.$groupName.'_'.$field->name) . ' VARCHAR(1024)'
        );
        return $db->query();
    }

    public static function updateTable($tableName, $groupName, $field) {
        return true;
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
        return '<textarea name="' . $this->getFullName() . '" '
             .           'class="text_area" '
             .           'rows="' . (($this->params->rows) ? $this->params->rows : '5') . '" '
             .           'cols="' . (($this->params->cols) ? $this->params->cols : '30') . '">'
             .     $value
             . '</textarea>';
    }

}
