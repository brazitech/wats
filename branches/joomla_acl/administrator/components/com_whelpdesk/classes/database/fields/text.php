<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

class TextWField extends WField {

    public function __construct($group, $definition) {
        parent::__construct($group, $definition);
    }

    /**
     * Checks if the parameters are valid for this field type (text)
     * 
     * @param JParameter params
     */
    public static function check($params) {
        $messages = array();
        
        // check maximumCharacterLength
        $params->set(
            'maximumCharacterLength',
            intval($params->get('maximumCharacterLength'))
        );
        if ($params->get('maximumCharacterLength') < 1) {
            $messages[] = JText::_('WHD_CD:TEXT:MAXIMUM CHARACTER LENGTH MUST BE GREATER THAN 0');
        } else if ($params->get('maximumCharacterLength') > 255) {
            $messages[] = JText::_('WHD_CD:TEXT:MAXIMUM CHARACTER LENGTH MUST BE LESS THAN 256');
        }

        // check minimumCharacterLength
        $params->set(
            'minimumCharacterLength',
            intval($params->get('minimumCharacterLength'))
        );
        if ($params->get('minimumCharacterLength') < 0) {
            $messages[] = JText::_('WHD_CD:TEXT:MINIMUM CHARACTER LENGTH MUST BE GREATER THAN -1');
        } elseif ($params->get('minimumCharacterLength') > $params->get('maximumCharacterLength')) {
            $messages[] = JText::_('WHD_CD:TEXT:MINIMUM CHARACTER LENGTH MUST BE LESS THAN MAXIMUM CHRACHTER LENGTH');
        }

        // check validity of minimum gaianst maximum
        if ($params->get('minimumCharacterLength') > $params->get('maximumCharacterLength')) {
            $messages[] = JText::_('WHD_CD:TEXT:MINIMUM CHARACTER LENGTH MUST BE LESS THAN MAXIMUM CHARACTER LENGTH');
        }

        // check fieldSize
        $params->set(
            'fieldSize',
            intval($params->get('fieldSize'))
        );
        if ($params->get('fieldSize') < 1) {
            $messages[] = JText::_('WHD_CD:TEXT:FIELD SIZE MUST BE GREATER THAN 0');
        }

        if (strlen($params->get('regularExpressionPattern'))) {
            if (!strlen($params->get('regularExpressionMatchFailedMessage'))) {
                $messages[] = JText::_('WHD_CD:TEXT:REGULAR EXPRESSION FAILED MESSAGE MISSING');
            }
        }
        
        return count($messages) ? $messages : true;
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
        if (JString::strlen($value) > $this->params->maximumCharacterLength) {
            $this->setError(JText::sprintf('%s VALUE %s IS TOO LONG', $this->getLabel(), $value));
            return false;
        }

        if (JString::strlen($value) < $this->params->minimumCharacterLength) {
            $this->setError(JText::sprintf('%s VALUE %s IS TOO SHORT', $this->getLabel(), $value));
            return false;
        }

        if (strlen($this->params->regularExpressionPattern)) {
            if (!preg_match($this->params->regularExpressionPattern, $value)) {
                $this->setError($this->params->regularExpressionMatchFailedMessage);
            }
        }
        
        return true;
    }

    public function getHTML_FormElement($value=null) {
        return '<input type="text" name="' . $this->getFullName() . '" '
                    . 'value="' . $value . '" '
                    . 'maxlength="' . intval($this->params->maximumCharacterLength) . '"'
                    . 'size="' . (($this->params->fieldSize) ? intval($this->params->fieldSize) : '35') . '"/>';
    }

}
