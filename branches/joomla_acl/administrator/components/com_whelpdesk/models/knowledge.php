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

class KnowledgeWModel extends WModel {

    public function  __construct() {
        $this->setName('knowledge');
        $this->setDefaultFilterOrder('k.name');
    }

    /**
     *
     * @param <type> $domain
     * @param <type> $alias
     * @param <type> $language
     * @param <type> $revision
     * @return <type>
     *
     * @todo implement alias and revision handling
     *
    public function getKnowledge($domain, $alias, $revision=null) {
        $db =& JFactory::getDBO();

        $sql = 'SELECT ' . dbName('k.*') . ', '
             . dbName('c.content') . ', '
             . dbName('c.id') . ' AS ' . dbName('revision') . ' '
             . ' FROM ' . dbTable('knowledge') . ' AS ' . dbName('k')
             . ' JOIN ' . dbTable('knowledge_revision') . ' AS ' . dbName('c')
             . ' ON ' . dbName('c.knowledge') . ' = ' . dbName('k.id') . ' '
             . ' WHERE ' . dbName('k.alias') . ' = ' . $db->Quote($alias)
             . ' AND ' . dbName('c.id') . ' = ('
             . '    SELECT MAX(' . dbName('r.revision') . ')'
             . '    FROM ' . dbTable('knowledge') . ' AS ' . dbName('k2')
             . '    JOIN ' . dbTable('knowledge_revision') . ' AS ' . dbName('r')
             . '    ON ' . dbName('r.knowledge') . ' = ' . dbName('k2.id')
             . '    WHERE ' . dbName('k2.alias') . ' = ' . $db->Quote($alias)
             . '    AND ' . dbName('k2.domain') . ' = ' . intval($domain)
             . ')'
             . ' AND ' . dbName('k.domain') . ' = ' . intval($domain);
        
        $db->setQuery($sql);

        return $db->loadObject();
    }*/

    public function getKnowledge($id, $reload = false) {
        $table = WFactory::getTable('knowledge');
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

    public function getKnowledgeFromAlias($alias, $domain, $reload = false) {
        $table = WFactory::getTable('knowledge');
        if ($alias) {
            if ($reload || $table->alias != $alias) {
                $db = JFactory::getDBO();
                $grouping = array('domain' => '(SELECT ' . dbName('d.id') . ' FROM ' . dbTable('knowledge_domain') . ' AS ' . dbName('d') . ' WHERE ' . dbName('d.alias') . ' = ' . $db->Quote($domain) . ')');
                if (!$table->loadFromAlias($alias, $grouping)) {
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
        $this->getKnowledge($id)->checkIn();
    }

    public function checkOut($id, $uid=0) {
        if (!$uid) {
            $uid = JFactory::getUser()->id;
        }
        $this->getKnowledge($id)->checkOut($uid);
    }

    /**
     * Gets an array of the knowledge
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
        $sql = 'SELECT COUNT(*) FROM ' . dbTable('knowledge') . ' AS ' . dbName('k')
             . ' ' . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);

        return (int)($database->loadResult());
    }

    public function getFilters() {
        $filters = parent::getFilters();
        $filters['categories'] = $this->getFilterDomains();
        return $filters;
    }

    private $filterDomains = null;

    public function getFilterDomains() {
        if ($this->filterDomains != null) {
            return $this->filterDomains;
        }

        // get all categories
        $sql = 'SELECT ' . dbName('d.name') . ', '
             . dbName('d.alias') . ', '
             . dbName('d.id') . ' '
             . 'FROM ' . dbTable('knowledge_domain') . ' AS ' . dbName('d') . ' '
             . 'ORDER BY ' . dbName('d.name');
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $this->filterDomains =  $db->loadObjectList('id');

        // set the selected domain
        $filter = $this->getFilterDomain();
        if ($filter && array_key_exists($filter, $this->filterDomains)) {
            $this->filterDomains[$filter]->filtering = true;
        }

        if (count($this->filterDomains)) {

            $user          = JFactory::getUser();
            $accessSession = WFactory::getAccessSession();

            // itterate over all categories and check for permissions
            foreach (array_keys($this->filterDomains) as $id) {
                try {
                    if (!$accessSession->hasAccess('user', $user->get('id'),
                                                   'knowledgedomain', $id,
                                                   'knowledgedomain', 'list')) {
                        unset($this->filterDomains[$id]);
                    }
                } catch (Exception $e) {
                    unset($this->filterDomains[$id]);
                }
            }
        }

        return $this->filterDomains;
    }

    private $filterDomain = null;

    public function getFilterDomain() {
        if ($this->filterDomain != null) {
            return $this->filterDomain;
        }

        // determine the catgory we are currently filtering on (if any)
        $this->filterDomain = JFactory::getApplication()->getUserStateFromRequest(
            'com_whelpdesk.model.knowledge.filter.category',
            'filterDomain',
            0,
            'INTEGER'
        );

        // check if this is a legal category to filter by
        if ($this->filterDomain) {
            $user          = JFactory::getUser();
            $accessSession = WFactory::getAccessSession();
            try {
                if (!$accessSession->hasAccess('user', $user->get('id'),
                                               'knowledgedomain', $this->filterDomain,
                                               'knowledgedomain', 'list')) {
                    $this->filterDomain = 0;
                }
            } catch (Exception $e) {
                $this->filterDomain = 0;
            }
        }

        return $this->filterDomain;
    }

    private function buildQuery() {
        $db = JFactory::getDBO();
        return 'SELECT ' . dbName('k.*') . ', ' .
                           'MAX(' . dbName('r.revision') . ') AS ' . dbName('latestRevision') . ', ' .
                           'MAX(' . dbName('r.created') . ') AS ' . dbName('lastRevised') . ', ' .
                           dbName('u.name') . ' AS ' . dbName('authorName') . ', ' .
                           dbName('u.username') . ' AS ' . dbName('authorUsername') . ', ' .
                           dbName('d.name') . ' AS ' . dbName('domainName') . ', ' .
                           dbName('d.alias') . ' AS ' . dbName('domainAlias') . ', ' .
                           'IF( (' . dbName('k.id') . ' = ' . dbName('d.default_page') . '), 1, 0) AS ' . dbName('isDefault') . ', ' .
                           'COUNT(' . dbName('lto.target_identifier') . ') AS ' . dbName('linksTo') . ', ' .
                           'COUNT(' . dbName('lfrom.identifier') . ') AS ' . dbName('linksFrom') . ' ' .
               'FROM ' . dbTable('knowledge') . ' AS ' . dbName('k') . ' ' .
               'LEFT JOIN ' . dbTable('knowledge_revision') . ' AS ' . dbName('r') .
               ' ON ' . dbName('r.knowledge') . ' = ' . dbName('k.id') . ' ' .
               'LEFT JOIN ' . dbTable('#__users') . ' AS ' . dbName('u') .
               ' ON ' . dbName('u.id') . ' = ' . dbName('k.created_by') . ' ' .
               'LEFT JOIN ' . dbTable('knowledge_domain') . ' AS ' . dbName('d') .
               ' ON ' . dbName('d.id') . ' = ' . dbName('k.domain') . ' ' .
               'LEFT JOIN ' . dbTable('links') . ' AS ' . dbName('lto') .
               ' ON ' . dbName('lto.target_type') . ' = ' . $db->Quote('knowledge') .
               ' AND ' . dbName('lto.target_identifier') . ' = ' . dbName('k.id') . ' ' .
               'LEFT JOIN ' . dbTable('links') . ' AS ' . dbName('lfrom') .
               ' ON ' . dbName('lfrom.type') . ' = ' . $db->Quote('knowledge') .
               ' AND ' . dbName('lfrom.identifier') . ' = ' . dbName('k.id') . ' ' .
               $this->buildQueryWhere()
               . ' GROUP BY ' . dbName('k.id') . ' ' .
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

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = '(LOWER(k.name) LIKE ' . $search
                     . 'OR LOWER(k.name) LIKE ' . $search . ')';
        }

        // deal with domains
        $domains = array();
        if ($this->getFilterDomain()) {
            $domains[] = intval($this->getFilterDomain());
        } else {
            $allowedCategories = $this->getFilterDomains();
            if (!count($allowedCategories)) {
                // no categories allowed... make sure we do not select any!
                $domains[] = '-1';
            } else {
                foreach ($allowedCategories AS $allowedDomain) {
                    $domains[] = intval($allowedDomain->id);
                }
            }
        }
        $where[] = '(' . dbName('k.domain') . ' = ' .
                   implode(
                       ' OR ' . dbName('k.domain') . ' = ',
                       $domains
                   ) . ')';

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
        // get the table and reset the data
        $table = WFactory::getTable('knowledge');
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

        WFactory::getOut()->log('Commited knowledge to the database');
        return $table->id;
    }

    function revise($id, $content, $comment) {
        $db = JFactory::getDBO();

        $date  = new JDate();
        $sql = 'INSERT INTO ' . dbTable('knowledge_revision')
             . ' SET ' . dbName('knowledge') . ' = ' . intval($id)
             . ', ' . dbName('revision') . ' = ('
             . '    SELECT IFNULL(MAX(' . dbName('r.revision') . '), 0) + 1 '
             . '    FROM ' . dbTable('knowledge_revision') . ' AS ' . dbName('r')
             . '    WHERE ' . dbName('r.knowledge') . ' = ' . intval($id)
             . ')'
             . ', ' . dbName('content') . ' = ' . $db->Quote($content)
             . ', ' . dbName('comment') . ' = ' . $db->Quote($comment)
             . ', ' . dbName('created') . ' = ' . $db->Quote($date->toMySQL())
             . ', ' . dbName('created_by') . ' = ' . JFactory::getUser()->get('id');
        $db->setQuery($sql);

        if ($db->query()) {
            WFactory::getOut()->log('Revised knowledge', true);
            return true;
        } else {
            WFactory::getOut()->log('Failed to revise knowledge', true);
            return false;
        }
    }

}

?>