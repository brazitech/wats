<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */
class WListColumnSelect extends WListColumn {

    public function renderHeader($direction, $order)
    {
        $html = '<th  class="title" width="'.$this->_width.'" nowrap="nowrap">';
        $html .= '<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />';
        $html .= '</th>';

        return $html;
    }

    /**
     * @param WList $list
     */
    public function render(WList $list)
    {
        $row = $list->getCurrentRow();

        $cellData = JHtml::_('grid.id', 0, $row->{$this->_name});
        return '<td align="center" '.$this->_attributes.'>'.$cellData.'</td>';
    }
}
