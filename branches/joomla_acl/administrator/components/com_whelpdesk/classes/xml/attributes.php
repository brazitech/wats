<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WXML_Attributes
{
    /**
     * Indexed array of attribtes, each attribute is represented as a
     * 
     * @var array
     */
    private $_attributes = array();

    public function  __construct($attributes)
    {
        // Check if there is anything to do
        if (!isset($attributes) || !count($attributes))
        {
            return;
        }

        // Get all attributes.
        foreach ($attributes->attribute AS $attribute)
        {
            // Basic data.
            $name = $attribute->attributes('name');
            $this->_attributes[$name] = array(
                'value'  => $attribute->attributes('value'),
                'params' => array()
            );

            // Add params if there are any.
            if (isset($attribute->param))
            {
                foreach($attribute->param AS $param)
                {
                    $this->_attributes[$name]['params'][] = $param->data();
                }
            }
        }
    }

    /**
     *
     * @return string
     */
    public function  buildAttributes($data)
    {
        // If this is an array convert to an object.
        if (is_array($data))
        {
            jimport('utilities.arrayhelper');
            $data = JArrayHelper::toObject($data);
        }

        $attributes = '';
        foreach ($this->_attributes as $attributeName => $attribute)
        {
            $values = array($attribute['value']);
            foreach($attribute['params'] AS $paramName)
            {
                $values[]  = $data->$paramName;
            }

            $attributeValue = ' '.call_user_func_array('sprintf', $values);
            $attributes .= ' '.$attributeName.'="'.htmlentities($attributeValue, ENT_QUOTES, 'UTF-8').'" ';
        }

        return trim($attributes);
    }
    
}