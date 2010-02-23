<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WListColumnText extends WListColumn
{

    private $_text;
    private $_translateText;

    public function  __construct($node, WList $list)
    {
        parent::__construct($node, $list);

        if (isset($node->text))
        {
            $this->_text          = $node->text[0]->data();
            $this->_translateText = $node->text[0]->attributes('translate');
        }
    }

    public function getText($row)
    {
        if ($this->_text != null)
        {
            if ($this->_translateText == "true")
            {        
                return JText::_($this->_text);
            }
            else
            {
                return $this->_text;
            }
        }

        return parent::getText($row);
    }
}
