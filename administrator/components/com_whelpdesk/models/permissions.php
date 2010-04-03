<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class ModelPermissions extends WModel {

    private $targetIdentifier = null;
    private $targetType       = null;

    public function  __construct() {
        parent::__construct();
        $this->setDefaultFilterOrder('');
    }

    /**
     * Gets an array of the permissions
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

        // get the rules
        $sql = $this->buildQuery();
        echo $sql;
        $database = JFactory::getDBO();
        $database->setQuery($sql, $limitstart, $limit);
        $list = $database->loadObjectList();

        // check the rules
        $accessSession = WFactory::getAccessSession();
        for ($i = 0, $c = count($list) ; $i < $c ; $i++) {
            $rule = $list[$i];
            if ($rule->allow == '0') {
                $rule->warning = 0;
            } else {
                $rule->warning = intval(!$accessSession->hasAccess($rule->request_type, $rule->request_identifier,
                                                                   $this->targetType, $this->targetIdentifier,
                                                                   $rule->type, $rule->control));
            }
        }

        return $list;
    }

    public function getTotal() {
        // get the total number of terms in the glossary
        $database = JFactory::getDBO();
        $sql = 'SELECT COUNT(*) ' .
               'FROM ' . dbTable('access_map') . ' AS ' . dbName('map') . ' ' .
               'JOIN ' . dbTable('tree') . ' AS ' . dbName('controls') .
               '  ON controls.identifier = map.control AND controls.`type` = map.`type` AND controls.grp = ' . $database->Quote('component-access') . ' ' .
               'LEFT JOIN jos_users AS ' . dbName('users') .
               '  ON users.id = map.request_identifier AND map.request_type = ' . $database->Quote('user') . ' ' .
               'LEFT JOIN ' . dbTable('user_groups') . ' AS ' . dbName('groups') .
               '  ON groups.id = map.request_identifier AND map.request_type = ' . $database->Quote('usergroup') . ' ' .
               $this->buildQueryWhere();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }

    private function buildQuery() {
        $database = JFactory::getDBO();

        return 'SELECT ' . dbName('map.*') . ', '.
               'IF( '.
               '  (' . dbName('map.request_type') . ' = ' . $database->Quote('user') . '), '.
               '  ' . dbName('users.name') . ', '.
               '  IF ( '.
               '    (' . dbName('map.request_type') . ' = ' . $database->Quote('usergroup') . '), '.
               '    ' . dbName('groups.name') . ', '.
               '    NULL '.
               '  ) '.
               ') AS ' . dbName('request_name') . ', '.
               'IF( '.
               '  (' . dbName('map.request_type') . ' = ' . $database->Quote('user') . '), '.
               '  ' . dbName('users.username') . ', '.
               '  IF ( '.
               '    (' . dbName('map.request_type') . ' = ' . $database->Quote('usergroup') . '), '.
               '    ' . dbName('groups.alias') . ', '.
               '    NULL '.
               '  ) '.
               ') AS ' . dbName('request_alias') . ', '.
               '' . dbName('controls.description') . ' AS ' . dbName('control_description') . ' '.
               'FROM ' . dbTable('access_map') . ' AS ' . dbName('map') . ' '.
               'JOIN ' . dbTable('tree') . ' AS ' . dbName('controls') . 
               '  ON controls.identifier = map.control AND controls.`type` = map.`type` AND controls.grp = ' . $database->Quote('component-access') . ' '.
               'LEFT JOIN jos_users AS ' . dbName('users') . 
               '  ON users.id = map.request_identifier AND map.request_type = ' . $database->Quote('user') . ' '.
               'LEFT JOIN ' . dbTable('user_groups') . ' AS ' . dbName('groups') . 
               '  ON groups.id = map.request_identifier AND map.request_type = ' . $database->Quote('usergroup') . ' ' .
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

        // limit to selected target
        $where[] = dbName('map.target_type') . ' = ' . $db->Quote($this->targetType);
        $where[] = dbName('map.target_identifier') . ' = ' . $db->Quote($this->targetIdentifier);

        $filterAllow = $this->getFilterAllow();
        if ($filterAllow == '1') {
            $where[] = dbName('map.allow') . ' = ' . $db->Quote('1');
        } elseif ($filterAllow == '0') {
            $where[] = dbName('map.allow') . ' = ' . $db->Quote('0');
        }

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = '( LOWER(' . dbName('users.name')           . ') LIKE ' . $search . ' OR '
                     . '  LOWER(' . dbName('users.username')       . ') LIKE ' . $search . ' OR '
                     . '  LOWER(' . dbName('groups.name')          . ') LIKE ' . $search . ' OR '
                     . '  LOWER(' . dbName('groups.alias')         . ') LIKE ' . $search . ' OR '
                     . '  LOWER(' . dbName('controls.description') . ') LIKE ' . $search . ')';
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
        // ordering
        $order = $this->getFilterOrder();

        // ordering direction
        $orderDirection = $this->getFilterOrderDirection();

        if ($order == '') {
            return ' ORDER BY' . dbname('users.name') . $orderDirection . ', '
                               . dbname('users.username') . $orderDirection . ', '
                               . dbname('groups.name') . $orderDirection . ', '
                               . dbName('groups.alias') . $orderDirection;
        }

        print_r($order);
        //return ' ORDER BY ' . dbName($order) . ' ' . $orderDirection;
    }

    public function setTargetIdentifier($targetIdentifier) {
        $this->targetIdentifier = $targetIdentifier;
    }

    public function setTargetType($targetType) {
        $this->targetType = $targetType;
    }

    /**
     * Get the current search filter for this model.
     *
     * @param string $default
     * @return string
     */
    public function getFilterAllow($default='') {
        // 1 == ALLOW, 0 == DENY, NULL == no allow status filter
        return JFactory::getApplication()->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.filter.allow',
                                                                   'filter_allow',
                                                                   $default,
                                                                   'cmd');
    }

    /**
     *
     * @return array
     */
    public function getFilters() {
        $filters = parent::getFilters();

        // add allow status
        $filters['allow'] = $this->getFilterAllow();

        return $filters;
    }

}

?>