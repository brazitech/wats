<?php

/**
 * Column that when rendered reflects the depth of the item in the nested set.  
 */
class WListColumnNested extends WListColumn {
    
    public function render($row)
    {
        $class = '';
        if ($row->level)
        {
            $class = ' class="indent-'.(4 + (($row->level - 1) * 15)).'"';
        }

        $text = '';
        if ($this->_link)
        {
            $text .= '<a href="'.$this->_link->buildLink($row).'">';
        }
        $text .= htmlentities($row->{$this->_name}, ENT_QUOTES, 'UTF-8');
        if ($this->_link)
        {
            $text .= '</a>';
        }
        
        $html = '<td '.$this->_attributes.$class.'>'.$text;
        if (array_key_exists('alias', get_object_vars($row)))
        {
            $html .= '<br/>('.JText::_('WHD_LIST:ALIAS').': '.$row->alias.')';
        }
        $html .= '</td>';

        return $html;
    }
}
