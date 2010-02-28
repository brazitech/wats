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
        $datetime = $row->{$this->_name};

        if (!$datetime || $datetime == '0000-00-00 00:00:00')
        {
            // no value
            return;
        }
        
        return JHTML::_('date', $datetime); ;
    }
}
