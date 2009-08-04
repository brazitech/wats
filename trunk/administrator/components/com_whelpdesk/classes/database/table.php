<?php
/* 
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// import library classes
jimport('joomla.database.table');
wimport('database.fieldset');

/**
 *
 */
abstract class WTable extends JTable {

    /**
     *
     * @var <type>
     */
    private $fieldset;

    /**
     *
     * @var boolean
     */
    private $init = false;

    public function __construct($table, $key, &$db) {
        // we need to deal with the custom fields for whelpdesk tables
        // each custom field required an instance variable
        if (preg_match('~^\#\_\_whelpdesk\_(.+)~', $table, $matches)) {
            // get the names of the groups associated with this table
            $this->fieldset = WFieldset::getInstance($matches[1]);
            $groupNames = $this->fieldset->getGroupNames();

            // itterate over the fields
            $fields = $this->fieldset->getFields();
            $this->init = true;
            for ($z = 0, $t = count($fields); $z < $t; $z++) {
                $field = $fields[$z];
                $this->set($field->getName(), $field->getDefault());
            }
            $this->init = false;
        }
        
        // let JTable take over
        parent::__construct($table, $key, $db);
    }

    /**
     * Sets the value of a property.
     *
     * @param string $var
     * @param mixed $val
     * @return mixed
     */
    public function __set($var, $val) {
        if ($this->init && !array_key_exists($var, get_object_vars($this))) {
            // we are in initialisation mode
            // this is a new property - lets create it
            return $this->{$var} = $val;
        }
        
        // carry on as normal way
        //debug_print_backtrace();
        //return parent::__set($var, $val);
	}

    /**
	 * Method to load a row from the database based on the alias and bind the
     * fields to the WTable instance properties. This only works on tables that
     * have an alias field.
	 *
	 * @param	mixed	Optional alias value to load the row by.  If not set the instance property value is used.
     * @param   array   additional keys and values that need to match - note that the values are not escaped, we must do this ourselves
	 * @param	boolean	True to reset the default values before loading the new row.
	 * @return	boolean	True if successful. False if row not found or on error (internal error state set in that case).
	 */
	public function loadFromAlias($alias = null, $grouping = array(), $reset = true) {
        if (!$this->hasField('alias')) {
            throw new WNotImplementedException();
        }

		$alias = (is_null($alias)) ? $this->alias : $alias;

		// Check we have an alias
		if ($alias === null) {
			return false;
		}

		// Reset the object values
		if ($reset) {
			$this->reset();
		}

		// Load the row by alias.
        $sql = 'SELECT *' .
			' FROM ' . dbTable($this->_tbl) .
			' WHERE ' . dbName('alias') . ' = ' . $this->_db->quote($alias);
		if (count($grouping)) {
            foreach ($grouping as $field => $value) {
                $sql .= ' AND ' . dbName($field) . ' = ' . $value;
            }
        }
        $this->_db->setQuery($sql);
		$row = $this->_db->loadAssoc();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Check that we have a result.
		if (empty($row)) {
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}


    /**
	 * @todo add security to ignore fields we cannot edit
	 */
	public function bind($src, $ignore = array()) {
        return parent::bind($src, $ignore);
    }

    public function hasField($fieldName) {
        return in_array($fieldName, array_keys($this->getProperties()));
    }

    /**
     * Checks the table data for validity - if the table is valid boolean true
     * is returned, otherwise 
     *
     * @return int|array
     */
    public function check() {
        if (!$this->fieldset) {
            // there is no dataset so we can continue
            return true;
        }

        // do some prep work
        $messages = array();
        $fields = $this->fieldset->getFields();

        // itterate over fields if there are any
        if (count($fields)) {
            foreach ($fields as $field) {
                // deal with field
                $value = $this->{$field->getName()};
                if ($field->isValid($value) != true) {
                    // field is not valid
                    // get the error message and set the return value
                    $isValid = false;
                    $messages[] = $field->getError();
                }
            }
        }

        // if there are
        if (count($messages)) {
            return $messages;
        }

        return true;
    }


    /**
     * Resets the hit counter for the specified record or for the current 
     * record. If the table has a reset_hits field this will be updated to 
     * reflect the current date and time.
     *
     * @param int|string $oid
     */
    function resetHits($oid=null) {
        // check this table suports hits before continuing
        if (!in_array('hits', array_keys($this->getProperties()))) {
			return;
		}

		// get the table PK and value of the PK we are working with
        $k = $this->_tbl_key;
		if ($oid == null) {
			$oid = $this->$k;
		}

        // get the names of the fields in this table
        $fieldNames = array_keys($this->getProperties());

        // make sure this table has a hits field
        if (!in_array('hits', $fieldNames)) {
			throw new WNotImplementedException();
		}

        // get ready to deal with the date
        jimport('joomla.utilities.date');
        $date  = new JDate();

        // update the actual table
		$query = 'UPDATE '. $this->_tbl
		       . ' SET ' . dbName('hits') . ' = 0 '
               . (in_array('hits_reset', $fieldNames)    ? ', ' . dbName('hits_reset') . ' = ' . $this->_db->Quote($date->toMySQL()) : '')
               . (in_array('hits_reset_by', $fieldNames) ? ', ' . dbName('hits_reset_by') . ' = ' . $this->_db->Quote(JFactory::getUser()->get('id')) : '')
               . ' WHERE '. $this->_tbl_key .'='. $this->_db->Quote($oid);
		$this->_db->setQuery($query);
        $this->_db->query();

        // update the local table data values if this is the same record
        if ($oid == $this->$k) {
            $this->hits = 0;
            if (in_array('hits_reset', $fieldNames)) {
                $this->hits_reset = $date->toMySQL();
            }
            if (in_array('hits_reset_by', $fieldNames)) {
                $this->hits_reset_by = JFactory::getUser()->get('id');
            }
        }
    }

    /**
     * Increments the version counter
     *
     * @param  int $oid ID of the record to revise
     * @return boolean
     */
    function revise($oid=null) {
        // check this table suports versions before continuing
        if (!in_array('revised', array_keys($this->getProperties()))) {
			return true;
		}

        // get the table PK and value of the PK we are working with
        $k = $this->_tbl_key;
		if ($oid == null) {
			$oid = $this->$k;
		}

        $query = 'UPDATE '. dbTable($this->_tbl)
               . ' SET '.   dbName('revised')       . ' = ('.dbName('revised').' + 1)'
               . ' WHERE '. dbName($this->_tbl_key) . ' = ' . $this->_db->Quote($oid);
		$this->_db->setQuery($query);
        $result = $this->_db->query();

        if ($result && $oid == $this->$k) {
            $this->revision++;
        }

        return $result;
    }

    public function getFieldset() {
        return $this->fieldset;
    }

    /**
     * Not supported by WTable objects
     */
    public function save() {
        throw new WNotImplementedException();
    }

}

?>
