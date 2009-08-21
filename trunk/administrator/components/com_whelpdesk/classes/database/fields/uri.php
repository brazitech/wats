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
            ' ADD COLUMN ' . dbName('field_'.$groupName.'_'.$field->name) . ' VARCHAR(255)'
        );
        return $db->query();
    }

    public static function updateTable($tableName, $groupName, $field) {
        return true;
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
                    . 'name="' .$this->getFullName() . '" '
                    . 'value="' . $value . '" '
                    . 'maxlength="400" '
                    . 'size="30"'
                    . 'title="' . $this->getLabel() . '" />';
    }

}
