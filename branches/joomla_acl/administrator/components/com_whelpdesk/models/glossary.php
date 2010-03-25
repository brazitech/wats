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

class ModelGlossary extends WModel {

    public function  __construct() {
        parent::__construct();
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getTerm($id, $reload = false) {
        $table = WFactory::getTable('glossary');
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

    public function delete($id) {
        if (is_array($id)) {
            for ($i = 0, $c = count($id); $i < $c; $i++) {
                $id[$i] = $this->delete($id[$i]);
            }
            return $id;
        } else {
            return $table = WFactory::getTable('glossary')->delete($id);
        }
    }

    /**
     * Gets an array of the glossary terms
     *
     * @param int $limit
     * @param int $limitstart
     * @return stdClass[]
     */
    public function getDisplayList() {
        // get the terms in the glossary
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM ' . dbTable('glossary') . ' '
             . 'WHERE ' . dbName('published') . ' = 1 '
             . 'ORDER BY ' . dbName('term');
        $db->setQuery($sql);
        return $db->loadObjectList();
    }

    private function buildQuery() {
        return 'SELECT ' . dbName('g.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ' ' .
               'FROM ' . dbTable('glossary') . ' AS ' . dbName('g') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('g.created_by') . ' ' .
               $this->buildQueryWhere() . 
               $this->buildQueryOrderBy();
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

        // check if we are filtering based on published state
        if ($state == 'P') {
            // items must be published
            $where[] = $db->nameQuote('published') . ' = 1';
        } elseif ($state == 'U') {
            // items must not be published
            $where[] = $db->nameQuote('published') . ' = 0';
        }

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = 'LOWER(term) LIKE ' . $search;
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

    private function buildQueryOrderBy() {
        // ordering
        $order = $this->getFilterOrder();

        // ordering direction
        $orderDirection = $this->getFilterOrderDirection();

        return ' ORDER BY ' . JFactory::getDBO()->nameQuote($order) . ' ' . $orderDirection;
    }

    /**
     *
     * @param int $id
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($id, $data) {
        // get the table and reset the data
        $table = WFactory::getTable('glossary');
        $table->reset();
        $table->id = $id;

        // make sure we do not override the supplied ID
        unset($data['id']);

        // load the base values
        if ($id) {
            if (!$table->load($id)) {
                WFactory::getOut()->log('Failed to load base data from table', true);
                return false;
            }
        }

        // bind data with the table
        if (!$table->bind($data, array(), true)) {
            // failed
            WFactory::getOut()->log('Failed to bind with table', true);
            return false;
        }

        // deal with created and modified dates
        $date  = new JDate();
        $table->modified = $date->toMySQL();
        if (!$id) {
            $table->created = $date->toMySQL();
        }

        // check the data is valid
        $check = $table->check();
        if (is_array($check)) {
            // failed
            WFactory::getOut()->log('Table data failed to check', true);
            throw new WCompositeException($check);
        }

        // store the data in the database table and update nulls
        if (!$table->store(true)) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            return false;
        }

        // store the data in the database table and update nulls
        if (!$table->revise()) {
            // failed
            WFactory::getOut()->log('Failed to increment revision counter', true);
            return false;
        }

        WFactory::getOut()->log('Commited glossary term to the database');
        return $table->id;
    }

}

?>