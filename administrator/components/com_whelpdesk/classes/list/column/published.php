<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */
class WListColumnPublished extends WListColumn
{

    /**
     * @param WList $list
     */
    public function render(WList $list)
    {
        $row = $list->getCurrentRow();

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
