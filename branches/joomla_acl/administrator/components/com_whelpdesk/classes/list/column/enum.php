<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WListColumnEnum extends WListColumn
{

    private $_toTextClass;
    private $_toTextClassPath;

    public function  __construct($node, WList $list)
    {
        parent::__construct($node, $list);

        $fullPathToClass = (string)$node->attributes('class');
        $this->_toTextClass = substr($fullPathToClass, strrpos($fullPathToClass, '.') + 1);
        $this->_toTextClassPath = strtolower($fullPathToClass);
    }

    public function getText($row)
    {
        $toTextClass = $this->_toTextClass;

        // Check if we need to import this class
        if (!class_exists($toTextClass))
        {
            wimport($this->_toTextClassPath);

            // Check class loaded.
            if (!class_exists($toTextClass))
            {
                if(class_exists("W$toTextClass"))
                {
                    // Last chance (phew!)
                    $this->_toTextClass = "W$toTextClass";
                }
                else
                {
                    // all gone wrong...
                    throw new WException("COULD NOT LOAD CLASS $this->_toTextClassPath");
                }
            }
        }

        // get the value
        return eval("return $toTextClass::toText(\$row->{\$this->_name});");
    }
}
