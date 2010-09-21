<?php

/**
 * Column that when rendered reflects the depth of the item in the nested set.  
 */
class WListColumnNested extends WListColumn
{

    /**
     * @param WList $list
     */
    public function render(WList $list)
    {
        $row = $list->getCurrentRow();

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
            $html .= '<br/>(' . JText::sprintf('WHD_LIST:ALIAS %s', $row->alias) . ')';
        }
        $html .= '</td>';

        return $html;
    }
}
