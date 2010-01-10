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
        $html .= JHtml::_('grid.order', $this->_list->getRows(), 'filesave.png', 'modules.saveorder');
        $html .= '</th>';

        return $html;
    }

    public function render($row)
    {
        /*$html = <<<HTML
<td align="center" $this->_attributes>
    <span>$this->pagination->orderUpIcon($i, true, 'modules.orderup', 'JGrid_Move_Up', $ordering)</span>
    <span>$this->pagination->orderDownIcon($i, $this->pagination->total, true, 'modules.orderdown', 'JGrid_Move_Down', $ordering);</span>
    <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
</td>
HTML;*/
        return parent::render($row);
    }
}
