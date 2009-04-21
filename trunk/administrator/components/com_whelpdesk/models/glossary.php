<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class GlossaryWModel extends WModel {

    public function  __construct() {
        $this->setName('glossary');
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
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('glossary');
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }

    private function buildQuery() {
        return 'SELECT ' . dbName('g.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ' ' .
               'FROM ' . dbTable('glossary') . ' AS ' . dbName('g') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('g.author') . ' ' .
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
        $state = $application->getUserStateFromRequest('com_whelpdesk.glossary.filter.state',
                                                       'filter_state',
                                                       '',
                                                       'word');

        // get the free text search filter
        $search = $application->getUserStateFromRequest('com_whelpdesk.glossary.filter.search',
                                                        'search',
                                                        '',
                                                        'string');
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
            $where = " WHERE ". implode(" AND ", $where);
        } else {
            // array is empty... nothing to do!
            $where = "";
        }

        // all done, send the result back
        return $where;
    }

    private function buildQueryOrderBy() {
        // get everything we need
        $application = JFactory::getApplication();
        
        // ordering
        $order = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.order",
                                                       "filter_order",
                                                       "term",
                                                       "cmd");

        // ordering direction
        $orderDirection = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.orderDirection",
                                                                "filter_order_Dir",
                                                                "ASC",
                                                                "cmd");
        $orderDirection = strtoupper($orderDirection) == 'ASC' ? 'ASC' : 'DESC';

        return ' ORDER BY ' . JFactory::getDBO()->nameQuote($order) . ' ' . $orderDirection;
    }

}

?>