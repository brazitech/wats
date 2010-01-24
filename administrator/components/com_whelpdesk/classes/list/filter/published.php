<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('list.filter');

class WListFilterPublished extends WListFilter
{

    public function render()
    {
        return JHtml::_(
			'select.genericlist',
			array(
                '' => '- ' . JText::_('Select State') . ' -',
                '1' => JText::_('PUBLISHED'),
                '0' => JText::_('UNPUBLISHED')
            ),
			$this->_id,
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="submitform();"',
				'list.select' => $this->getConditionValue(),
				'option.key' => null
			)
		);
    }

    /*protected function getConditionValue()
    {
        // get the application object and define the state context
        $app =& JFactory::getApplication();
        $context = $this->_list->getNamespace().'.';

        // get the filter value.
        if ($app->isSite())
        {
            $value = JRequest::getVar($this->_id);
        }
        else
        {
            $value = $app->getUserStateFromRequest($context.$this->_id, $this->_id, '');
        }

        if ($value == '')
        {
            return false;
        }

        switch ($value)
        {
            case 'P':
                $value = 1;
                break;
            case 'U':
                $value = 0;
                break;
            default:
                $value = false;
        }
        return $value;
    }*/
}
