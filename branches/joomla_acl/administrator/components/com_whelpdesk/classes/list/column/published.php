<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */
class WListColumnPublished extends WListColumn {
    
    public function render($row)
    {
        $cellData = JHTML::_(
            'grid.published',
            $row->published,
            0,
            'tick.png',
            'publish_x.png',
            WCommand::getInstance()->getType().'.state.'.($row->published == 1 ? 'unpublish' : 'publish')
        );
        return '<td align="center" '.$this->_attributes.'>'.$cellData.'</td>';
    }
}
