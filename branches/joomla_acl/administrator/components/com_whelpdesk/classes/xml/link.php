<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WXML_Link
{
    private $_link;

    private $_params = array();

    public function  __construct($node)
    {
        // Make sure we are dealing with the correct node.
        if ($node->name() != 'link')
        {
            if (isset($node->link))
            {
                $link = $node->link;
            }
            else
            {
                throw new WException('Error parsing XML Link');
            }
        }
        else
        {
            $link = $node;
        }

        // get the link
        $this->_link = $link->attributes('link');

        // itterate over the parameters.
        foreach($link->param AS $param)
        {
            $this->_params[] = $param->data();
        }
    }

    /**
     *
     * @param array|object $data
     * @return string
     */
    public function buildLink($data)
    {
        // If this is an array convert to an object.
        if (is_array($data))
        {
            jimport('utilities.arrayhelper');
            $data = JArrayHelper::toObject($data);
        }

        // build values array.
        $values = array();
        $values[] = $this->_link;
        foreach ($this->_params AS $param)
        {
            $values[] = $data->$param;
        }

        // substitue bits and pieces and we're all done!
        return call_user_func_array('sprintf', $values);
    }
    
}