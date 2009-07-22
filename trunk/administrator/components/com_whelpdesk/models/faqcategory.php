<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class FaqcategoryWModel extends WModel {

    public function  __construct() {
        $this->setName('faqcategory');
        $this->setDefaultFilterOrder('f.name');
    }

    /**
     * Gets an array of the faq categpries
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
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('faq_categories') . ' AS ' . dbName('f')
             . ' ' . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
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
     */
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
    }

    public function getFaqs($id) {
        $id = intval($id);
        $db =& JFactory::getDBO();
        $sql = 'SELECT ' . dbName('f.*') . ' '
             . 'FROM ' . dbTable('faqs') . ' AS ' . dbName('f') . ' '
             . 'WHERE ' . dbName('f.category') . ' = ' . $id . ' '
             . 'AND ' . dbName('f.published') . ' = 1';
        $db->setQuery($sql);
        return $db->loadObjectList();
    }

}

?>