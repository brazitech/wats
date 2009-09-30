<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

jimport('joomla.utilities.date');
wimport('exceptions.composite');

class DatagroupWModel extends WModel {

    public function  __construct() {
        $this->setName('datagroup');
        $this->setDefaultFilterOrder('g.ordering');
    }

    public function getGroup($id, $reload = false) {
        $table = WFactory::getTable('datagroup');
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

    public function getAllGroups() {
        $db = JFactory::getDBO();
        $query = new JQuery;

        $query->select('g.*');
		$query->from('#__whelpdesk_data_groups AS g');

        $query->select('t.name AS tableName');
        $query->join('LEFT', '#__whelpdesk_data_tables AS t ON t.id = g.table');

        $query->order('t.name, g.ordering');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
    
    /**
     * Gets an array of data groups
     *
     * @param int $limit
     * @param int $limitstart
     * @return stdClass[]
     */
    public function getList($limitstart = null, $limit = null) {
        // get the limitstart value
        if ($limitstart === null) {
            $limitstart = $this->getLimitstart();
        }

        // get the limit value
        if ($limit === null) {
            $limit = $this->getLimit();
        }

        // get the terms in the glossary
        $sql = $this->buildQuery();
        $database = JFactory::getDBO();
        $database->setQuery($sql, $limitstart, $limit);
        
        return $database->loadObjectList();
    }
    
    public function getTotal() {
        // get the total number of terms in the glossary
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('data_groups') . ' AS ' . dbName('g')
             . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }
    
    public function getFilters() {
        $filters = parent::getFilters();
        $filters['tables'] = $this->getFilterTables();

        return $filters;
    }
    
    private $filterTables = null;

    public function getFilterTables() {
        if ($this->filterTables != null) {
            return $this->filterTables;
        }

        // get all tables
        $sql = 'SELECT ' . dbName('t.*')
             . ' FROM ' . dbTable('data_tables') . ' AS ' . dbName('t')
             . ' ORDER BY ' . dbName('t.name');
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $this->filterTables =  $db->loadObjectList('id');

        // set the selected table
        $filter = $this->getFilterTable();
        if ($filter && array_key_exists($filter, $this->filterTables)) {
            $this->filterTables[$filter]->filtering = true;
        }

        return $this->filterTables;
    }

    private $filterTable = null;

    public function getFilterTable() {
        if ($this->filterTable != null) {
            return $this->filterTable;
        }

        // determine the table we are currently filtering on (if any)
        $this->filterTable = JFactory::getApplication()->getUserStateFromRequest(
            'com_whelpdesk.model.datagroup.filter.table',
            'filterTable',
            0,
            'INTEGER'
        );

        return $this->filterTable;
    }
    
    private function buildQuery() {
        return 'SELECT ' . dbName('g.*') . ', ' .
               dbName('g.*') . ', ' .
               dbName('t.name') . ' AS ' . dbName('tableName') . ' ' .
               'FROM ' . dbTable('data_groups') . ' AS ' . dbName('g') . ' ' .
               'JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t') .
               ' ON ' . dbName('t.id') . ' = ' . dbName('g.table') . ' ' .
               $this->buildQueryWhere() . ' ' .
               $this->buildQueryOrderBy();
    }
    
    /**
     * Builds the WHERE clause
     *
     * @return string
     */
    private function buildQueryWhere() {
        // prepare to build WHERE clause as an array
        $where = array();
        $db    =& JFactory::getDBO();
        
        // deal with table filter
        $filterTable = intval($this->getFilterTable());
        if ($filterTable) {
            $where[] = dbName('g.table') . ' = ' . $filterTable;
        }

        // build the WHERE clause
        if (count($where)) {
            // building from array
            $where = ' WHERE ' . implode(' AND ', $where);
        } else {
            // array is empty... nothing to do!
            $where = '';
        }

        // all done, send the result back
        return $where;
    }
    
    private function buildQueryOrderBy() {
        // ordering and ordering direction
        $order = $this->getFilterOrder();
        $orderDirection = $this->getFilterOrderDirection();

        //return ' ORDER BY ' . dbName($order) . ' ' . $orderDirection;
    }

}
