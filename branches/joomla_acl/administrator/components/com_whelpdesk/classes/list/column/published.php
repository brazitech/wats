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
            'some.state.'
        );
        return '<td align="center" '.$this->_attributes.'>'.$cellData.'</td>';
    }
}
