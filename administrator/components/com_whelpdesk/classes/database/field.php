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
     * Gets a cached instance of a WField object
     *
     * @param string $type
     * @return WField
     */
    public static function getInstance($type, $definition) {
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
        return new $className($definition);
    }

    public function  __construct($definition) {
        $this->name        = $definition['name'];
        $this->label       = $definition['label'];
        $this->description = $definition['description'];
        $this->default     = $definition['default'];
        $this->list        = (bool)$definition['list'];
        $this->params      = json_decode($definition['params']);
        if (!is_object($this->params)) {
            $this->params = new stdClass();
        }
    }

    //public abstract function addToTable($table);

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

    public function getName() {
        return $this->name;
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
