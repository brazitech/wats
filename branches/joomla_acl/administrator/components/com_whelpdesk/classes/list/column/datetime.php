<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */
class WListColumnDatetime extends WListColumn {
    
    public function getText($row)
    {
        return JHTML::_('date', $row->{$this->_name}); ;
    }
}
