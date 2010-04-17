<?php
/**
 * @version $Id: published.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('list.filter');

class WListFilterHidden extends WListFilter
{
    public function render()
    {
        return '<input id="'.$this->_id.'" type="hidden" value="'.htmlentities($value, ENT_QUOTES, "UTF-8").'" name="'.$this->_name.'" '.$this->_attributes.'/>';
    }
}
