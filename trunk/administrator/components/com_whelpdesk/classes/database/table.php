<?php
/* 
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// import JTable
jimport('joomla.database.table');
wimport('data.dataset');

/**
 *
 */
abstract class WTable extends JTable {

    /**
     *
     * @var <type>
     */
    private $dataset;

    private $init = false;

    public function __construct($table, $key, &$db) {
        // we need to deal with the custom fields for whelpdesk tables
        // each custom field required an instance variable
        if (preg_match('~^\#\_\_whelpdesk\_(.+)~', $table, $matches)) {
            // get the names of the groups associated with this table
            $this->dataset = WDataset::getInstance($matches[1]);
            $groupNames = $this->dataset->getGroupNames();

            // itterate over the fields
            $fields = $this->dataset->getFields();
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
        return parent::__set($var, $val);
	}

    public function check() {
        if (!$this->dataset) {
            // there is no dataset so we can continue
            return true;
        }

        // do some prep work
        $isValid = true;
        $fields = $this->dataset->getFields();

        // itterate over fields if there are any
        if (count($fields)) {
            foreach ($fields as $field) {
                // deal with field
                $value = $this->{$field->getName()};
                if ($field->isValid($value) != true) {
                    // field is not valid
                    // get the error message and set the return value
                    $isValid = false;
                    $this->setError($field->getError());
                }
            }
        }

        return $isValid;
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
		if ($oid !== null) {
			$this->$k = intval($oid);
		}

		$query = 'UPDATE '. $this->_tbl
		       . ' SET hits = 0'
               . ' WHERE '. $this->_tbl_key .'='. $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
        $this->_db->query();
        $this->hits = 0;

        // check this table suports hits reset date and time before continuing
        if (!in_array('reset_hits', array_keys($this->getProperties()))) {
			return;
		}

        jimport('joomla.utilities.date');

        $date  = new JDate();
        $query = 'UPDATE '. dbTable($this->_tbl)
               . ' SET '.   dbName('reset_hits')    . ' = ' . $this->_db->Quote($date->toMySQL())
               . ' WHERE '. dbName($this->_tbl_key) . ' = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
        $this->_db->query();
        $this->reset_hits = $date->toMySQL();
    }

    /**
     * Increments the version counter
     *
     * @param  int $oid ID of the record to revise
     * @return boolean
     */
    function revise($oid=null) {
        // check this table suports versions before continuing
        if (!in_array('version', array_keys($this->getProperties()))) {
			return true;
		}

        // get the table PK and value of the PK we are working with
        $k = $this->_tbl_key;
		if ($oid !== null) {
			$this->$k = intval($oid);
		}

        $query = 'UPDATE '. dbTable($this->_tbl)
               . ' SET '.   dbName('version')       . ' = ('.dbName('version').' + 1)'
               . ' WHERE '. dbName($this->_tbl_key) . ' = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
        return($this->_db->query());
    }


}

?>
