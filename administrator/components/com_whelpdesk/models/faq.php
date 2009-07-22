<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class FaqWModel extends WModel {

    public function  __construct() {
        $this->setName('faq');
        $this->setDefaultFilterOrder('f.question');
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
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('faqs') . ' AS ' . dbName('f')
             . ' ' . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }

    private $createCategories = null;

    public function getCreateCategories() {
        if ($this->createCategories != null) {
            return $this->createCategories;
        }

        // get all categories
        $sql = 'SELECT ' . dbName('c.name') . ', '
             . dbName('c.alias') . ', '
             . dbName('c.id') . ' '
             . 'FROM ' . dbTable('faq_categories') . ' AS ' . dbName('c') . ' '
             . 'ORDER BY ' . dbName('c.name');
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $this->createCategories =  $db->loadObjectList('id');

        // set the selected category
        $filter = $this->getFilterCategory();
        if ($filter && array_key_exists($filter, $this->createCategories)) {
            $this->createCategories[$filter]->filtering = true;
        }

        if (count($this->createCategories)) {

            $user          = JFactory::getUser();
            $accessSession = WFactory::getAccessSession();

            // itterate over all categories and check for permissions
            foreach (array_keys($this->createCategories) as $id) {
                try {
                    if (!$accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategory', $id,
                                                   'faq', 'create')) {
                        unset($this->createCategories[$id]);
                    }
                } catch (Exception $e) {
                    unset($this->createCategories[$id]);
                }
            }
        }

        return $this->createCategories;
    }

    public function getFilters() {
        $filters = parent::getFilters();
        $filters['categories'] = $this->getFilterCategories();
        return $filters;
    }

    private $filterCategories = null;

    public function getFilterCategories() {
        if ($this->filterCategories != null) {
            return $this->filterCategories;
        }
        
        // get all categories
        $sql = 'SELECT ' . dbName('c.name') . ', '
             . dbName('c.alias') . ', '
             . dbName('c.id') . ' '
             . 'FROM ' . dbTable('faq_categories') . ' AS ' . dbName('c') . ' '
             . 'ORDER BY ' . dbName('c.name');
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $this->filterCategories =  $db->loadObjectList('id');

        // set the selected category
        $filter = $this->getFilterCategory();
        if ($filter && array_key_exists($filter, $this->filterCategories)) {
            $this->filterCategories[$filter]->filtering = true;
        }

        if (count($this->filterCategories)) {

            $user          = JFactory::getUser();
            $accessSession = WFactory::getAccessSession();

            // itterate over all categories and check for permissions
            foreach (array_keys($this->filterCategories) as $id) {
                try {
                    if (!$accessSession->hasAccess('user', $user->get('id'),
                                                   'faqcategory', $id,
                                                   'faq', 'list')) {
                        unset($this->filterCategories[$id]);
                    }
                } catch (Exception $e) {
                    unset($this->filterCategories[$id]);
                }
            }
        }

        return $this->filterCategories;
    }

    private $filterCategory = null;

    public function getFilterCategory() {
        if ($this->filterCategory != null) {
            return $this->filterCategory;
        }

        // determine the catgory we are currently filtering on (if any)
        $this->filterCategory = JFactory::getApplication()->getUserStateFromRequest(
            'com_whelpdesk.model.faq.filter.category',
            'filterCategory',
            0,
            'INTEGER'
        );

        
        // check if this is a legal category to filter by
        if ($this->filterCategory) {
            $user          = JFactory::getUser();
            $accessSession = WFactory::getAccessSession();
            try {
                if (!$accessSession->hasAccess('user', $user->get('id'),
                                               'faqcategory', $this->filterCategory,
                                               'faq', 'list')) {
                    $this->filterCategory = 0;
                }
            } catch (Exception $e) {
                $this->filterCategory = 0;
            }
        }

        return $this->filterCategory;
    }

    private function buildQuery() {
        return 'SELECT ' . dbName('f.*') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ', ' .
                           dbName('c.name') . ' AS ' . dbName('categoryName') . ', ' .
                           dbName('c.alias') . ' AS ' . dbName('categoryAlias') . ' ' .
               'FROM ' . dbTable('faqs') . ' AS ' . dbName('f') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('f.created_by') . ' ' .
               'LEFT JOIN ' . dbTable('faq_categories') . ' AS ' . dbName('c') .
               ' ON ' . dbName('c.id') . ' = ' . dbName('f.category') . ' ' .
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
            $where[] = '(LOWER(f.question) LIKE ' . $search
                     . 'OR LOWER(f.answer) LIKE ' . $search . ')';
        }

        // deal with categories
        $categories = array();
        if ($this->getFilterCategory()) {
            $categories[] = intval($this->getFilterCategory());
        } else {
            $allowedCategories = $this->getFilterCategories();
            if (!count($allowedCategories)) {
                // no categories allowed... make sure we do not select any!
                $categories[] = '-1';
            } else {
                foreach ($allowedCategories AS $allowedCategory) {
                    $categories[] = intval($allowedCategory->id);
                }
            }
        }
        $where[] = '(' . dbName('f.category') . ' = ' .
                   implode(
                       ' OR ' . dbName('f.category') . ' = ',
                       $categories
                   ) . ')';

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

}

?>