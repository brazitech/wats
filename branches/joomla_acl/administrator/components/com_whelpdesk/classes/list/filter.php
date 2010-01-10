<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

abstract class WListFilter
{
    protected $_label;
    protected $_id;
    protected $_name;
    protected $_columns = array();
    protected $_attributes;

    protected $_list;

    public function  __construct($node, WList $list)
    {
        $this->_list = $list;

        $this->_label  = JText::_($node->label[0]->data());
        $this->_id     = $node->attributes('id');
        $this->_name   = $node->attributes('name');

        foreach ($node->column as $column)
        {
            $this->_columns[] = $column->data();
        }

        if (isset($node->attribute))
        {
            foreach ($node->attribute as $attribute)
            {
                $this->_attributes .= ' '.$attribute->attributes('name').'='.$attribute->data().' ';
            }
        }
    }

    public static function getInstance($node, WList $list)
    {
        // determine column type
        $type = $node->attributes('type');
        if ($type == null || $type == '')
        {
            throw new WException('WHD_LIST:FILTER MUST SPECIFY A TYPE');
        }

        $class = 'WListFilter'.ucfirst($type);
        if (!class_exists($class))
        {
            // load class file
            $filename = strtolower($type).'.php';
            $path     = JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'list'.DS.'filter'.DS.$filename;
            if (!JFile::exists($path))
            {
                // file not found
                throw new WException('WHD_LIST:UNKNOWN FILTER TYPE %s', $type);
            }
            else
            {
                require_once($path);
            }
        }

        // make sure the class exists now we have loaded the file.
        if (!class_exists($class))
        {
            throw new WException('WHD_LIST:FILTER CLASS IS MISSING FOR TYPE %s', $type);
        }

        // build new object and return
        return new $class($node, $list);
    }

    /**
     * @return string HTML filter field
     */
    public function render()
    {
        $value = JRequest::getVar($this->_id);
        return '<label for="'.$this->_id.'">'.$this->_label.'</label>'.
               '<input id="'.$this->_id.'" type="text" value="'.htmlentities($value, ENT_QUOTES, "UTF-8").'" name="'.$this->_name.'" '.$this->_attributes.'/>';
    }

    /**
     * Get the condition to add to the query
     */
    public function getCondition()
    {
        $value = JRequest::getVar($this->_id);
        if ($value == null || $value == '')
        {
            return false;
        }

        return ' ' . dbName($this->_column) . '=' .
               JFactory::getDBO()->Quote($value) . ' ';
    }
}
