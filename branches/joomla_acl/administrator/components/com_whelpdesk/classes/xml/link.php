<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('xml.attributes');

class WXML_Link
{
    private $_link;

    private $_params = array();

    /**
     * @var XML_Attributes
     */
    private $_attributes;

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

        // Add attributes is there are any
        if (isset($node->attribute))
        {
            $this->_attributes = new WXML_Attributes($node);
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

    /**
     *
     *
     * @param array|object $data
     * @param string
     * @return string
     */
    public function buildHTML_Link($data, $linkName)
    {
        $link = $this->buildLink($data);
        $attributes = $this->buildLinkAttributes($data);

        return '<a href="'.$link.'" '.$attributes.'>'.$linkName.'</a>';
    }

    /**
     *
     *
     * @param array|object $data
     * @param string
     * @return string
     */
    public function buildLinkAttributes($data)
    {
        // Only build if there are attributes to build
        if ($this->_attributes)
        {
            return $this->_attributes->buildAttributes($data);
        }

        return '';
    }

    
}