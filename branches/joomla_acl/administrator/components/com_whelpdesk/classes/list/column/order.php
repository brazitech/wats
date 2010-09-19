<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */
class WListColumnOrder extends WListColumn {
    
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
     * @param WList $list
     */
    public function renderPlain(WList  $list)
    {
        $row = $list->getCurrentRow();
        $previousRow = $list->getRow($list->getRowPointer() - 1);
        $nextRow = $list->getRow($list->getRowPointer() + 1);

        $html = '';

        if (!is_null($previousRow))
        {
            $html .= JHtml::_('jgrid.orderUp', $i, $task, '', $alt, $enabled, $checkbox);
        }

        if (!is_null($nextRow))
        {
            $html .= JHtml::_('jgrid.orderDown', $i, $task, '', $alt, $enabled, $checkbox);
        }
        
        return $html;
    }
}
