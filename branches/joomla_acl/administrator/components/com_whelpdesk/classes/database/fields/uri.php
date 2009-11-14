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
     * Checks if the parameters are valid for this field type (uri)
     *
     * @param JParameter params
     */
    public static function check($params) {
        $messages = array();

        // check required field
        $params->set(
            'required',
            intval($params->get('required'))
        );
        if ($params->get('required') < 0 || $params->get('required') > 1) {
            $messages[] = JText::_('WHD_CD:URI:INVALID REQUIRED VALUE');
        }

        // check invalidMessage
        if (strlen($params->get('invalidMessage') < 0)) {
            $messages[] = JText::_('WHD_CD:URI:MISSING INVALID MESSAGE');
        }

        return count($messages) ? $messages : true;
    }

    /**
     * Assumes maximum length of URLs to be 2,083 characters. This is the IE URL
     * charachter limiit. Technically there is no defined limit however for 
     * practical reasons we must assume a limit of some description.
     * 
     * @param <type> $tableName
     * @param <type> $groupName
     * @param <type> $field
     * @return <type> 
     */
    public static function addToTable($tableName, $groupName, $field) {

        $db = JFactory::getDBO();
        $db->setQuery(
            'ALTER TABLE ' . dbTable($tableName) .
            ' ADD COLUMN ' . dbName('field_'.$groupName.'_'.$field->name) . ' VARCHAR(2083)'
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
