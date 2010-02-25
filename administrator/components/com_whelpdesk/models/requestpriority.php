<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('exceptions.composite');
jimport('joomla.utilities.date');

class ModelRequestPriority extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->setDefaultFilterOrder('ordering');
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getRequestPriority($id, $reload = false) {
        $table = WFactory::getTable('requestpriority');
        if ($id) {
            if ($reload || $table->id != $id) {
                if (!$table->load($id)) {
                    return false;
                }
            }
        } else {
            $table->reset();
            $table->id = 0;
        }

        return $table;
    }

    public function checkIn($id) {
        $this->getRequestPriority($id)->checkIn();
    }

    public function checkOut($id, $uid=0) {
        if (!$uid) {
            $uid = JFactory::getUser()->id;
        }
        $this->getRequestPriority($id)->checkOut($uid);
    }

    public function getTotal() {
        // get the total number of categories in the glossary
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('request_priority')
             . $this->buildQueryWhere();
        $this->_db->setQuery($sql);
        
        return (int)($this->_db->loadResult());
    }

    /**
     * Builds the WHERE clause
     *
     * @return string
     */
    private function buildQueryWhere() {
        // get the application
        $application =& JFactory::getApplication();

        // get the state filter (publishing)
        $state = $this->getFilterState();

        // get the free text search filter
        $search = $this->getFilterSearch();
        $search = JString::strtolower($search);

        // prepare to build WHERE clause as an array
        $where = array();
        $db    =& JFactory::getDBO();

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = 'LOWER(name) LIKE ' . $search;
        }

        // build the WHERE clause
        if (count($where)) {
            // building from array
            $where = ' WHERE ' . implode(' AND ', $where);
        } else {
            // array is empty... nothing to do!
            $where = "";
        }

        // all done, send the result back
        return $where;
    }

}
