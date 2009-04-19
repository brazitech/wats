<?php
/* 
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// import JTable
jimport('joomla.database.table');

/**
 *
 */
abstract class WTable extends JTable {

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
        $query = 'UPDATE '. $this->_tbl
               . ' SET reset_hits = ' . $this->_db->Quote($date->toMySQL())
               . ' WHERE '. $this->_tbl_key . ' = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
        $this->_db->query();
        $this->reset_hits = $date->toMySQL();
    }


}

?>
