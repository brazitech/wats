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
     * @param   string      $type       The type of form, EDIT or NEW
	 * @return	WForm		A WForm instance.
	 */
	public static function getInstance($data, $name = 'form', $file = true, $options = array(), $type = null)
	{
		static $instances;

		if ($instances == null) {
			$instances = array();
		}

        // Deal with type if specified.
        if ($file && ($type != null))
        {
            $specificData = $data . '_' . strtolower($type);

            if (JPath::find(JForm::addFormPath(), $specificData.'.xml') !== false)
            {
                // There is a specialised XML file for this type
                $data = $specificData;
            }
        }

        // Default type.
        if ($type == null)
        {
            $type = '_default';
        }
        
		// Only load the form once.
		if (!isset($instances[$name][$type])) {
			// Instantiate the form.
			$instances[$name][$type] = new WForm($name, $options);

			// Set the form name.
			// $instances[$name][$type]->setName($name);

			// Load the data.
			if ($file)
            {
				$instances[$name][$type]->loadFile($data, true);
			}
            else
            {
				$instances[$name][$type]->load($data, true);
			}

		}

		return $instances[$name][$type];
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
            $group = ($xml->attributes()->group) ? (string)$xml->attributes()->group : '_default';

            // Get the fieldset position
            if ($value = $xml->attributes()->position) {
                $this->_fieldsets[$group]['position'] = (string)$value;
                echo $value;
            } else {
                $this->_fieldsets[$group]['position'] = 'normal';
            }

            // Get the name of the field from which the label can be sought
            if ($value = $xml->attributes()->labelfield) {
                $this->_fieldsets[$group]['labelfield'] = (string)$value;
            }
            
        }

        return $success;
	}

    /**
     *
     * @param string $position
     * @return <type> 
     
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
	}*/

}
