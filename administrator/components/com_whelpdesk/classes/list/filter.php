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
    protected $_position = 'right';
    protected $_columns = array();
    protected $_attributes;

    /**
     *
     * @var WList
     */
    protected $_list;

    public function  __construct($node, WList $list)
    {
        $this->_list = $list;

        $this->_label    = JText::_($node->label[0]->data());
        $this->_id       = $node->attributes('id');
        $this->_name     = $node->attributes('name');
        $this->_position = $node->attributes('position');

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
        $value = $this->getConditionValue();

        if ($value === false)
        {
            return false;
        }

        $conditions = array();
        $value = JFactory::getDBO()->Quote($value);
        foreach ($this->_columns AS $column)
        {
            $conditions[] = dbName($column) . ' = ' . $value;
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }

    protected function getConditionValue()
    {
        // get the application object and define the state context
        $app =& JFactory::getApplication();
        $context = $this->_list->getNamespace().'.';

        // get the filter value.
        if ($app->isSite())
        {
            $value = JRequest::getVar($this->_id);
        }
        else
        {
            $value = $app->getUserStateFromRequest($context.$this->_id, $this->_id, '');
        }

        if ($value == '')
        {
            return false;
        }

        return $value;
    }

    /**
     * Get the position in which to display the filter.
     *
     * This should always be left or right
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * Render JavaScript to reset the filter
     * 
     * @return string
     */
    public function renderReset()
    {
        return "document.getElementById('$this->_id').value='';";
    }
}
