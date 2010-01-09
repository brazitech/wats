<?php
/**
 * @version		$Id$
 * @package		Helpdesk
 * @subpackage	Form
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.form');

class WForm extends JForm
{

    /**
	 * Method to get an instance of a form.
	 *
	 * @param	string		$name		The name of the form.
	 * @param	string		$data		The name of an XML file or an XML string.
	 * @param	string		$file		Flag to toggle whether the $data is a file path or a string.
	 * @param	array		$options	An array of options to pass to the form.
	 * @return	WForm		A WForm instance.
	 */
	public static function getInstance($data, $name = 'form', $file = true, $options = array())
	{
		static $instances;

		if ($instances == null) {
			$instances = array();
		}

		// Only load the form once.
		if (!isset($instances[$name])) {
			// Instantiate the form.
			$instances[$name] = new WForm($options);

			// Set the form name.
			$instances[$name]->setName($name);

			// Load the data.
			if ($file) {
				$instances[$name]->load($data, true, true);
			} else {
				$instances[$name]->load($data, false, true);
			}

		}

		return $instances[$name];
	}

	/**
	 * Loads form fields from an XML fields element optionally reseting fields before loading new ones.
	 *
	 * @param	object		$xml		The XML fields object.
	 * @param	boolean		$reset		Flag to toggle whether the form groups should be reset.
	 * @return	boolean		True on success, false otherwise.
	 */
	public function loadFieldsXML(&$xml, $reset = true)
	{
        $success = parent::loadFieldsXML($xml, $reset);

        // check for success
        if ($success) {

            // Get the group name.
            $group = ($xml->attributes('group')) ? $xml->attributes('group') : '_default';

            // Get the fieldset position
            if ($value = $xml->attributes('position')) {
                $this->_fieldsets[$group]['position'] = $value;
            } else {
                $this->_fieldsets[$group]['position'] = 'normal';
            }

            // Get the name of the field from which the label can be sought
            if ($value = $xml->attributes('labelfield')) {
                $this->_fieldsets[$group]['labelfield'] = $value;
            }
            
        }

        return $success;
	}

    /**
     *
     * @param string $position
     * @return <type> 
     */
    public function getFieldsets($position=null)
	{
        if ($position == null)
        {
            return $this->_fieldsets;
        }

        // iterate over fieldsets and retrieve fieldsets in selected position.
        $fieldsets = array();
        foreach ($this->_fieldsets as $group => $fieldset)
        {
            if ($fieldset['position'] == $position)
            {
                $fieldsets[$group] = $fieldset;
            }
        }

        return $fieldsets;
	}

}
