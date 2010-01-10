<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

abstract class WListColumn
{
    /**
     *
     * @var WList
     */
    protected $_list;

    protected $_label;
    protected $_name;
    protected $_width;
    protected $_attributes;

    /**
     *
     * @var boolean
     */
    protected $_canSort;

    public function  __construct($node, WList $list)
    {
        $this->_list    = $list;

        $this->_label   = JText::_($node->label[0]->data());
        $this->_name    = $node->name[0]->data();
        $this->_width   = $node->attributes('width');
        $this->_canSort = ($node->attributes('cansort') == 'true');

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
            $type = 'simple';
        }

        $class = 'WListColumn'.ucfirst($type);
        if (!class_exists($class))
        {
            // load class file
            $filename = strtolower($type).'.php';
            $path     = JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'list'.DS.'column'.DS.$filename;
            if (!JFile::exists($path))
            {
                // file not found, use simple type instead
                JError::raiseWarning(
                    '500', 
                    JText::sprintf('WHD_LIST:UNKNOWN COLUMN TYPE %s', $type)
                );
                $node->addAttribute('type', 'simple');
                return self::getInstance($node);
            }
            else
            {
                require_once($path);
            }
        }

        // make sure the class exists now we have loaded the file.
        if (!class_exists($class))
        {
            JError::raiseWarning(
                '500',
                JText::sprintf('WHD_LIST:UNKNOWN COLUMN TYPE %s CLASS', $type)
            );
            $node->addAttribute('type', 'simple');
            return self::getInstance($node);
        }

        // build new object and return
        return new $class($node, $list);
    }

    /**
     * @return string HTML Table Header cell
     */
    public function renderHeader($direction, $order)
    {
        $html = '<th  class="title" width="'.$this->_width.'" nowrap="nowrap">';
        if ($this->_canSort)
        {
            $html .= JHTML::_('grid.sort', $this->_label, $this->_name, $direction, $order);
        }
        else
        {
            $html .= htmlentities(JText::_($this->_label), ENT_QUOTES, 'UTF-8');
        }
        $html .= '</th>';

        return $html;
    }

    /**
     * @return string
     */
    public function render($row)
    {
        return '<td '.$this->_attributes.'>'.htmlentities($row->{$this->_name}, ENT_QUOTES, 'UTF-8').'</td>';
    }
}

/**
 * Default WListColumn class
 */
class WListColumnSimple extends WListColumn { }