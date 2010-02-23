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

class WListFilterSqlselect extends WListFilter
{
    private $_query;
    private $_options;
    private $_staticOptions;
    private $_optionKey;
    private $_optionText;

    public function  __construct($node, WList $list)
    {
        parent::__construct($node, $list);

        $this->_optionKey  = $node->attributes('key');
        $this->_optionText = $node->attributes('text');

        $this->_staticOptions = array();
         if (isset($node->option))
        {
            foreach ($node->option as $option)
            {
                $staticOption = new stdClass();
                $staticOption->{$this->_optionKey} = $option->attributes('key');
                if ($option->attributes('translate') == 'true')
                {
                    $staticOption->{$this->_optionText} = JText::_($option->data());
                }
                else
                {
                    $staticOption->{$this->_optionText} = $option->data();
                }
                $this->_staticOptions[] = $staticOption;
            }
        }


        // load the query
        if (isset($node->query))
        {
            $this->_query = new WDatabaseQuery();
            if (!$this->_query->fromXML($node->query[0]))
            {
                throw new WException('Error parsing SQL Select XML query.');
            }
        }
    }

    protected function getOptions()
    {
        if (!isset($this->_options))
        {
            // get options from the database
            if ($this->_query)
            {
                $db = &JFactory::getDbo();
                $db->setQuery((string)$this->_query);
                $this->_options = $db->loadObjectList();
            }
            else
            {
                $this->_options = array();
            }

            // add static options to the start of the options
            for ($i = count($this->_staticOptions) - 1; $i >= 0; $i--)
            {
                array_unshift($this->_options, $this->_staticOptions[$i]);
            }
        }

        return $this->_options;
    }

    public function render()
    {
        return JHtml::_(
			'select.genericlist',
			$this->getOptions(),
			$this->_id,
			array(
				'list.attr'   => 'class="inputbox" size="1" onchange="submitform();"',
				'list.select' => $this->getConditionValue(),
				'option.key'  => $this->_optionKey,
                'option.text' => $this->_optionText
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
