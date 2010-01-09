<?php
/**
 * @version $Id: glossary.php 207 2010-01-02 14:23:37Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('exceptions.composite');
jimport('joomla.utilities.date');

class ModelRequestCategory extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->setDefaultFilterOrder('name');
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getRequestCategory($id, $reload = false) {
        $table = WFactory::getTable('requestcategory');
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
        $this->getRequestCategory($id)->checkIn();
    }

    public function checkOut($id, $uid=0) {
        if (!$uid) {
            $uid = JFactory::getUser()->id;
        }
        $this->getRequestCategory($id)->checkOut($uid);
    }

    public function changeState($cid, $published) {
        // get the table and publish the identified terms
        $table = WFactory::getTable('requestcategory');
        $table->publish($cid, ($published ? 1 : 0), JFactory::getUser()->id);
    }

    public function resetHits($id) {
        $table = WFactory::getTable('requestcategory');
        $table->resetHits($id);
    }

    public function delete($id) {
        // @todo
        
        /*if (is_array($id)) {
            for ($i = 0, $c = count($id); $i < $c; $i++) {
                $id[$i] = $this->delete($id[$i]);
            }
            return $id;
        } else {
            return $table = WFactory::getTable('requestcategory')->delete($id);
        }*/
    }

    /**
     * Gets an array of the glossary terms
     *
     * @param int $limit
     * @param int $limitstart
     * @return stdClass[]
     
    public function getDisplayList() {
        // get the terms in the glossary
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM ' . dbTable('glossary') . ' '
             . 'WHERE ' . dbName('published') . ' = 1 '
             . 'ORDER BY ' . dbName('term');
        $db->setQuery($sql);
        return $db->loadObjectList();
    }

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
    }*/

    public function getTotal() {
        // get the total number of categories in the glossary
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('request_categories')
             . $this->buildQueryWhere();
        $this->_db->setQuery($sql);
        
        return (int)($this->_db->loadResult());
    }

    /*private function buildQuery() {
        return 'SELECT ' . dbName('g.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ' ' .
               'FROM ' . dbTable('glossary') . ' AS ' . dbName('g') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('g.created_by') . ' ' .
               $this->buildQueryWhere() . 
               $this->buildQueryOrderBy();
    }*/

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
        $table = WFactory::getTable('requestcategory');
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

        // run advanced validation using JForm object
        $form = $this->_form;
        $form->bind($table);
        $check = $form->validate($table);
        if (!$check)
        {
            $check = array();
            $totalErrors = count($form->getErrors());
            for ($i = 0; $i < $totalErrors; $i++)
            {
                $check[] = $form->getError($i, true);
            }
            WFactory::getOut()->log('Form data failed to check', true);
            throw new WCompositeException($check);
        }

        // run simple validation (very loose rules)
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

        WFactory::getOut()->log('Commited request category to the database');
        return $table->id;
    }

}

?>