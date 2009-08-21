<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

// No direct access
defined('JPATH_BASE') or die();

class WField extends JObject {

    /**
     * name of the field
     *
     * @var string
     */
    private $name;

    /**
     * Label as seen by the end user, this is the untranslated string
     *
     * @var string
     */
    private $label;

    /**
     * A description of the field for the end user
     *
     * @var string
     */
    private $description;

    /**
     * Default value of the field used to populate the form field
     *
     * @var string
     */
    private $default;

    /**
     * Show in list views?
     *
     * @var boolean
     */
    private $list;

    protected $params;

    /**
     * Group to which the field belongs
     * 
     * @var object
     */
    private $group;

    /**
     * Gets an instance of a WField object
     *
     * @param string $type
     * @return WField
     */
    public static function getInstance($type, $group, $definition) {
        // sanitize type
        $type = strtolower($type);

        // build class name
        $className = ucfirst($type) . 'WField';

        // check class does not already exist
        if (!class_exists($className)) {
            // load class
            $path = JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS
                  . 'database' . DS . 'fields' . DS . JFile::makeSafe($type) . '.php';

            if (!JFile::exists($path)) {
                JError::raiseError(500, JText::sprintf('WHD UNKNOWN FIELD TYPE %s', $type));
                jexit();
            }

            // get the class file!
            require_once($path);
        }

        // create an instance of the class and return it
        return new $className($group, $definition);
    }

    public function  __construct($group, $definition) {
        $this->name        = $definition['name'];
        $this->label       = $definition['label'];
        $this->description = $definition['description'];
        $this->default     = $definition['default'];
        $this->list        = (bool)$definition['list'];
        $this->params      = json_decode($definition['params']);
        $this->group       = $group;
        if (!is_object($this->params)) {
            $this->params = new stdClass();
        }
    }

    private static $fieldTypes;

    public static function getFieldTypes() {
        if (!self::$fieldTypes) {
            self::$fieldTypes = JFolder::files(
                JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'database' . DS . 'fields',
                '\.xml$'
            );

            for ($i = count(self::$fieldTypes) - 1; $i >= 0; $i--) {
                self::$fieldTypes[$i] = JFile::stripExt(self::$fieldTypes[$i]);
            }
        }

        return self::$fieldTypes;
    }

    /**
     *
     * @param string $fieldType
     * @param string $tableName
     * @param string $groupName
     * @param object $field
     * @return boolean
     */
    public static function addToTable($fieldType, $tableName, $groupName, $field) {
        $fieldTypeClassName = ucfirst(strtolower($fieldType)) . 'WField';
        wimport('database.fields.'.strtolower($fieldType));
        return call_user_func(array($fieldTypeClassName, 'addToTable'), $tableName, $groupName, $field);
    }

    public static function updateTable($fieldType, $tableName, $groupName, $field) {
        $fieldTypeClassName = ucfirst(strtolower($fieldType)) . 'WField';
        wimport('database.fields.'.strtolower($fieldType));
        return call_user_func(array($fieldTypeClassName, 'updateTable'), $tableName, $groupName, $field);
    }

    public static function check($fieldType, $fieldParameters) {
        $fieldTypeClassName = ucfirst(strtolower($fieldType)) . 'WField';
        wimport('database.fields.'.strtolower($fieldType));
        return call_user_func(array($fieldTypeClassName, 'check'), $fieldParameters);
    }

    public function isValid($value) {
        return true;
    }

    public function getFormattedValue($value) {
        return $value;
    }

    public function getStoredValue($value) {
        return $value;
    }

    //public abstract function getHTML_FormElement($value=null);

    public function getGroup() {
        return $this->group;
    }

    public function getName() {
        return $this->name;
    }

    public function getFullName() {
        return 'field_' . $this->group->name . '_' . $this->name;
    }

    public function getLabel($translate=true) {
        return ($translate) ? JText::_($this->label) : $this->label;
    }

    public function getDescription($translate=true) {
        return ($translate) ? JText::_($this->description) : $this->description;
    }

    public function getDefault() {
        return $this->default;
    }

    public function getHTML_FormElement($value=null) {
        throw new WException();
    }

    public function getHTML($value=null) {
        return $value;
    }

    public function isListField() {
        return $this->list;
    }

}
