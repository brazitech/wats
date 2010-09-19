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

class ModelFaqcategory extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->_tableName = 'faqcategory';
    }

    public function getCategory($id, $reload = false) {
        $table = WFactory::getTable('faqcategory');
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

    private function buildQuery() {
        return 'SELECT ' . dbName('f.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ', ' .
                           'COUNT(' . dbName('q.id') . ') AS ' . dbName('pages') . ' ' .
               'FROM ' . dbTable('faq_categories') . ' AS ' . dbName('f') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('f.created_by') . ' ' .
               'LEFT JOIN ' . dbTable('faqs') . ' AS ' . dbName('q') .
               ' ON ' . dbName('f.id') . ' = ' . dbName('q.category') . ' ' .
               $this->buildQueryWhere() . ' ' .
               'GROUP BY ' . dbName('f.id') .
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
            $where[] = dbName('f.published') . ' = 1';
        } elseif ($state == 'U') {
            // items must not be published
            $where[] = dbName('f.published') . ' = 0';
        }

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = 'LOWER(f.name) LIKE ' . $search;
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
     * Deletes the specified FAQ category
     *
     * @param int $id
     * @throws WException
     
    public function delete($id) {
        // make sure ID is an integer
        $id = (int)$id;

        // start with the tree - it's the hardest bit...
        // if this goes wrong an exception will be thrown
        $accessSession = WFactory::getAccessSession();
        $accessSession->removeNode('faqcategory', $id, true);

        // get the FAQ Category table
        $table = WFactory::getTable('faqcategory');
        
        // delete the FAQ category
        if ($table->delete($id)) {
            $exception = WDatabaseConsistencyException('FAQ CATEGORIES', array($id));
            JError::raiseNotice('200', JText::sprintf('WHD FAQ CATEGORY %d DELETED', $id));
        } else {
            JError::raiseWarning('500', JText::sprintf('WHD FAQ CATEGORY %d DELETE FAILED', $id));
        }

        // remove related FAQs
        $sql = 'DELETE FROM ' . dbTable('faqs')
             . ' WHERE ' . dbName('category') . ' = ' . $id;
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $db->query();
    }*/

    public function getFaqs($id) {
        $id = intval($id);
        $db =& JFactory::getDBO();
        $sql = 'SELECT ' . dbName('f.*') . ' '
             . 'FROM ' . dbTable('faqs') . ' AS ' . dbName('f') . ' '
             . 'WHERE ' . dbName('f.category') . ' = ' . $id . ' '
             . 'AND ' . dbName('f.published') . ' = 1 '
             . 'ORDER BY ' . dbName('f.question');
        $db->setQuery($sql);
        return $db->loadObjectList();
    }

    /**
     *
     * @param int $id
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($id, $data) {
        // get the table and reset the data
        $table = WFactory::getTable('faqcategory');
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
        if ($id) {
            if (!$table->revise()) {
                // failed
                WFactory::getOut()->log('Failed to increment revision counter', true);
                return false;
            }
        }

        // add to the tree if necessary
        if (!$id) {
            try {
                $accessSession = WFactory::getAccessSession();
                $accessSession->addNode('faqcategory', $table->id, $table->alias, 'faqcategories', 'faqcategories');
                WFactory::getOut()->log('Commited FAQ category to the tree');
            } catch (Exception $e) {
                WFactory::getOut()->log('Failed to commit FAQ category to the tree', $e->getMessage());
                return false;
            }
        }

        WFactory::getOut()->log('Commited FAQ category to the database');
        return $table->id;
    }

    public function delete($id) {
        // get the record
        $table = WFactory::getTable('faqcategory');
        $table->load($id);

        // attempt to delete the node and FAQ category
        // note that the treeSession will deal with the FAQ category
        // delete as well as the node delete
        $accessSession = WFactory::getAccessSession();
        try {
            // delete the node from the tree that represent the category
            $accessSession->deleteNode('faqcategory', $id, true);
        } catch (Exception $e) {
            return false;
        }

        // success!
        return true;
    }

}

?>