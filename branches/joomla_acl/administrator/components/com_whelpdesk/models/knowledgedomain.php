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

class KnowledgedomainWModel extends WModel {

    public function  __construct() {
        $this->setName('knowledgedomain');
        $this->setDefaultFilterOrder('k.name');
    }

    public function getKnowledgeDomain($id, $reload = false) {
        $table = WFactory::getTable('knowledgedomain');
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

    public function getKnowledgeDomainFromAlias($alias, $reload = false) {
        $table = WFactory::getTable('knowledgedomain');
        if ($alias) {
            if ($reload || $table->alias != $alias) {
                $db = JFactory::getDBO();
                if (!$table->loadFromAlias($alias)) {
                    return false;
                }
            }
        } else {
            $table->reset();
            $table->alias = 0;
        }

        return $table;
    }

    public function checkIn($id) {
        $this->getKnowledgeDomain($id)->checkIn();
    }

    public function checkOut($id, $uid=0) {
        if (!$uid) {
            $uid = JFactory::getUser()->id;
        }
        $this->getKnowledgeDomain($id)->checkOut($uid);
    }

    /**
     * Gets an array of the glossary terms
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
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('knowledge_domain') . ' AS ' . dbName('k')
             . ' ' . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }

    private function buildQuery() {
        return 'SELECT ' . dbName('k.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ', ' .
                           'COUNT(' . dbName('i.id') . ') AS ' . dbName('pages') . ', ' .
                           '0 AS ' . dbName('pagesMissing') . ' ' .
               'FROM ' . dbTable('knowledge_domain') . ' AS ' . dbName('k') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('k.created_by') . ' ' .
               'LEFT JOIN ' . dbTable('knowledge') . ' AS ' . dbName('i') .
               ' ON ' . dbName('k.id') . ' = ' . dbName('i.domain') . ' ' .
               $this->buildQueryWhere() . ' ' .
               'GROUP BY ' . dbName('k.id') .
               $this->buildQueryOrderBy();
    }

    /**
     * Builds the WHERE clause
     *
     * @return string
     */
    private function buildQueryWhere() {
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
            $where[] = dbName('k.published') . ' = 1';
        } elseif ($state == 'U') {
            // items must not be published
            $where[] = dbName('k.published') . ' = 0';
        }

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = 'LOWER(k.name) LIKE ' . $search;
        }

        // build the WHERE clause
        if (count($where)) {
            // building from array
            $where = " WHERE ". implode(" AND ", $where);
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

        return ' ORDER BY ' . dbName($order) . ' ' . $orderDirection;
    }

    /**
     *
     * @param int $id
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($id, $data) {
        // get the table
        $table = WFactory::getTable('knowledgedomain');
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
            $table->created    = $date->toMySQL();
            $table->created_by = JFactory::getUser()->get('id');
        }

        // check the data is valid
        $check = $table->check();
        if (is_array($check)) {
            // failed
            WFactory::getOut()->log('Table data failed to check', true);
            throw new WCompositeException($check);
        }

        // store the data in the database table and update nulls
        if (!$table->store()) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            return false;
        }

        // increment the revision counter
        if ($id) {
            if (!$table->revise()) {
                // failed
                WFactory::getOut()->log('Failed to increment revision counter', true);
                return false;
            }
        }

        WFactory::getOut()->log('Commited knowledge domain to the database');
        return $table->id;
    }

}

?>