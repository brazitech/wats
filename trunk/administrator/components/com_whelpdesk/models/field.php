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
wimport('database.field');

class FieldWModel extends WModel {

    public function  __construct() {
        $this->setName('field');
        $this->setDefaultFilterOrder('f.name');
    }

    private static $fields = array();

    public function getField($group, $name) {
        $cacheKey = intval($group) . '.' . $name;

        // check if cache needs building
        if (!array_key_exists($cacheKey, self::$fields)) {
            $db = JFactory::getDBO();
            $sql = 'SELECT ' . dbName('f.*')
                 . ' , ' . dbName('g.name')  . ' AS ' . dbName('groupName')
                 . ' , ' . dbName('g.label') . ' AS ' . dbName('groupLabel')
                 . ' , ' . dbName('t.name')  . ' AS ' . dbName('tableName')
                 . ' FROM ' . dbTable('data_fields') . ' AS ' . dbName('f')
                 . ' LEFT JOIN ' . dbTable('data_groups') . ' AS ' . dbName('g')
                 . ' ON ' . dbName('g.id') . ' = ' . dbName('f.group')
                 . ' LEFT JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t')
                 . ' ON ' . dbName('t.id') . ' = ' . dbName('g.table')
                 . ' WHERE ' . dbName('f.group') . ' = ' . intval($group)
                 . ' AND ' . dbName('f.name') . ' = ' . $db->Quote($name);
            $db->setQuery($sql);
            $field = $db->loadObject();

            // load params object if we need it
            if ($field) {
                $field->params = new JParameter(
                    $field->params,
                    JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'database' . DS . 'fields' . DS . preg_replace('(^a-z)', '', strtolower($field->type)) . '.xml'
                );
            }

            self::$fields[$cacheKey] = $field;
        }

        return self::$fields[$cacheKey];
    }

    private static $newFields = array();

    public function getNewField($group) {
        $group = intval($group);
        // check if cache needs building
        if (!array_key_exists($group, self::$newFields)) {
            self::$newFields[$group] = new stdClass();
            self::$newFields[$group]->group    = $group;
            self::$newFields[$group]->name     = '';
            self::$newFields[$group]->label    = '';
            self::$newFields[$group]->description  = '';
            self::$newFields[$group]->default  = '';
            self::$newFields[$group]->params   = new JParameter();
            self::$newFields[$group]->type     = '';
            self::$newFields[$group]->ordering = null;
            self::$newFields[$group]->list     = 0;
            self::$newFields[$group]->checked_out = 0;
            self::$newFields[$group]->checked_out_time = null;
            self::$newFields[$group]->version  = null;
            self::$newFields[$group]->created  = null;
            self::$newFields[$group]->modified = null;

            $db = JFactory::getDBO();
            $db->setQuery(
                'SELECT ' . dbName('t.name') . ' AS ' . dbName('tableName') .
                ' , ' . dbName('g.name') . ' AS ' . dbName('groupName') .
                ' , ' . dbName('g.label') . ' AS ' . dbName('groupLabel') .
                ' FROM ' . dbTable('data_tables') . ' AS ' . dbName('t') .
                ' JOIN ' . dbTable('data_groups') . ' AS ' . dbName('g') .
                ' ON ' . dbName('t.id') . ' = ' . dbName('g.table') .
                ' WHERE ' . dbName('g.id') . ' = ' . $group
            );
            $result = $db->loadObject();
            
            self::$newFields[$group]->tableName  = $result->tableName;
            self::$newFields[$group]->groupName  = $result->groupName;
            self::$newFields[$group]->groupLabel = $result->groupLabel;
        }

        return self::$newFields[$group];
    }

    /**
     * Gets an array of fields
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
        $sql = 'SELECT COUNT(*) '
             . 'FROM ' . dbTable('data_fields') . ' AS ' . dbName('f')
             . 'LEFT JOIN ' . dbTable('data_groups') . ' AS ' . dbName('g')
             . ' ON ' . dbName('g.id') . ' = ' . dbName('f.group')
             . 'LEFT JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t')
             . ' ON ' . dbName('t.id') . ' = ' . dbName('g.table')
             . ' ' . $this->buildQueryWhere();
        $database = JFactory::getDBO();
        $database->setQuery($sql);
        
        return (int)($database->loadResult());
    }

    public function getFilters() {
        $filters = parent::getFilters();
        $filters['tables'] = $this->getFilterTables();
        $filters['groups'] = $this->getFilterGroups();

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
            'com_whelpdesk.model.field.filter.table',
            'filterTable',
            0,
            'INTEGER'
        );

        return $this->filterTable;
    }

    private $filterGroups = null;

    public function getFilterGroups() {
        if ($this->filterGroups != null) {
            return $this->filterGroups;
        }

        // get all tables
        $where = '';
        $table = $this->getFilterTable();
        if ($table) {
            $where = ' WHERE ' . dbName('g.table') . ' = ' . intval($table);
        }
        $sql = 'SELECT ' . dbName('g.*')
             . ' FROM ' . dbTable('data_groups') . ' AS ' . dbName('g')
             . $where
             . ' ORDER BY ' . dbName('g.name');
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $this->filterGroups =  $db->loadObjectList('id');

        // set the selected table
        $filter = $this->getFilterGroup();
        if ($filter && array_key_exists($filter, $this->filterGroups)) {
            $this->filterGroups[$filter]->filtering = true;
        }

        return $this->filterGroups;
    }

    private $filterGroup = null;

    public function getFilterGroup() {
        if ($this->filterGroup != null) {
            return $this->filterGroup;
        }

        // determine the group we are currently filtering on (if any)
        $this->filterGroup = JFactory::getApplication()->getUserStateFromRequest(
            'com_whelpdesk.model.field.filter.group',
            'filterGroup',
            0,
            'INTEGER'
        );

        // make sure the filter is valid
        $db = JFactory::getDBO();
        $filterTable = $this->getFilterTable();
        $db->setQuery(
            'SELECT COUNT(*) ' .
            ' FROM ' . dbTable('data_groups') . ' AS ' . dbName('g') .
            ' JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t') .
            ' ON ' . dbName('g.table') . ' = ' . dbName('t.id') .
            ' WHERE ' . dbName('g.id') . ' = ' . intval($this->filterGroup) .
            (($filterTable) ? ' AND ' . dbName('t.id') . ' = ' . intval($filterTable) : '')
        );
        if ($db->loadResult() == 0) {
            // group is not valid
            // group does not exist or is not linked to the filter table
            $this->filterGroup = null;
        }

        return $this->filterGroup;
    }

    private function buildQuery() {
        return 'SELECT ' . dbName('f.*') . ', ' .
               dbName('g.name') . ' AS ' . dbName('groupName') . ', ' .
               dbName('t.name') . ' AS ' . dbName('tableName') . ' ' .
               'FROM ' . dbTable('data_fields') . ' AS ' . dbName('f') . ' ' .
               'LEFT JOIN ' . dbTable('data_groups') . ' AS ' . dbName('g') .
               ' ON ' . dbName('g.id') . ' = ' . dbName('f.group') . ' ' .
               'LEFT JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t') .
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
            $where[] = dbName('t.id') . ' = ' . $filterTable;
        }

        // deal with group filter
        $filterGroup = intval($this->getFilterGroup());
        if ($filterGroup) {
            $where[] = dbName('g.id') . ' = ' . $filterGroup;
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

        return ' ORDER BY ' . dbName($order) . ' ' . $orderDirection;
    }

    /**
     *
     * @param int $group
     * @param string $name
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($group, $name, $data, $isNew=false) {
        // load the base values
        $group = intval($group);
        if ($isNew) {
            // we are dealing with a new field
            $field = $this->getNewField($group);
        } else {
            // we are dealing with an existing field
            $field = $this->getField($group, $name);
            if (!$field) {
                // field does not exist
                throw new WCompositeException(
                    array(
                        JText::_('WHD_CD:UNKNOWN FIELD')
                    )
                );
            }
        }

        // update values in the record object
        $date  = new JDate();
        $field->label       = array_key_exists('label', $data)       ? $data['label']          : $field->label;
        $field->description = array_key_exists('description', $data) ? $data['description']    : $field->description;
        $field->default     = array_key_exists('default', $data)     ? $data['default']        : $field->default;
        $field->list        = array_key_exists('list', $data)        ? ($data['list'] ? 1 : 0) : $field->list;
        $field->version     = $isNew                                 ? null                    : $field->version + 1;
        $field->created     = $isNew                                 ? $date->toMySQL()        : $field->created;
        $field->modified    = $date->toMySQL();
        // update params if defined and defined as an array and array is not empty
        if (array_key_exists('params', $data) && is_array($data['params']) && count($data['params'])) {
            foreach(array_keys($data['params']) AS $parameterKey) {
                $field->params->set($parameterKey, $data['params'][$parameterKey]);
            }
        }

        // get the group and table data
        $db = JFactory::getDBO();
        $db->setQuery(
            'SELECT ' . dbName('g.name') . ' AS ' . dbName('groupName') .
            ', ' . dbName('t.table') . ' AS ' . dbName('table') .
            ' FROM ' . dbTable('data_groups') . ' AS ' . dbName('g') .
            ' JOIN ' . dbTable('data_tables') . ' AS ' . dbName('t') .
            ' ON ' . dbName('t.id') . ' = ' . dbName('g.table') .
            ' WHERE ' . dbName('g.id') . ' = ' . intval($field->group)
        );
        $tableAndGroupName = $db->loadObject();
        if (!$db->getAffectedRows()) {
            // no rows returned - the group must be invalid
            throw new WCompositeException(
                array(
                    JText::_('WHD_CD:GROUP DOES NOT EXIST')
                )
            );
        }
        $field->groupName = $tableAndGroupName->groupName;
        $field->tableName = $tableAndGroupName->table;
        unset($tableAndGroupName);

        // check the data is valid
        $check = $this->check($field);
        if (is_array($check)) {
            // failed
            WFactory::getOut()->log('Table data failed to check', true);
            throw new WCompositeException($check);
        }

        // store the data in the database table and update nulls
        $sql = 'REPLACE INTO ' . dbTable('data_fields')
             . ' SET ' . dbName('label')     . ' = ' . $db->Quote($field->label)
             . ' , ' . dbName('description') . ' = ' . $db->Quote($field->description)
             . ' , ' . dbName('default')     . ' = ' . $db->Quote($field->default)
             . ' , ' . dbName('params')      . ' = ' . $db->Quote($field->params)
             . ' , ' . dbName('list')        . ' = ' . intval($field->list)
             . ' , ' . dbName('version')     . ' = ' . $db->Quote($field->version)
             . ' , ' . dbName('created')     . ' = ' . $db->Quote($field->created)
             . ' , ' . dbName('modified')    . ' = ' . $db->Quote($field->modified)
             . ' , ' . dbName('type')        . ' = ' . $db->Quote($field->type)
             . ' , ' . dbName('system')      . ' = 0 ' // system will always be 0 because we cannto create or edit system fields
             . ' , ' . dbName('ordering')    . ' = ' . intval($field->ordering)
             . ' , ' . dbName('checked_out') . ' = ' . intval($field->checked_out)
             . ' , ' . dbName('checked_out_time') . ' = ' . intval($field->checked_out_time)
             . ' , ' . dbName('group')       . ' = ' . intval($field->group)
             . ' , ' . dbName('name')        . ' = ' . $db->Quote($field->name);
        $db->setQuery($sql);
        if (!$db->query()) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            throw new WCompositeException(
                array(
                    JText::sprintf('WHD_CD:COULD NOT CREATE FIELD DEFINITION %s IN TABLE %s', $field->label, $field->tableName)
                )
            );
        }

        // update the physical table
        if ($isNew) {
            if(!WField::addToTable($field->type, $field->tableName, $field->groupName, $field)) {
                // uh oh - failed to add to the table
                // remove from fields table before continuing
                $db->setQuery(
                    'DELETE FROM ' . dbTable('data_fields') .
                    ' WHERE ' . dbName('group') . ' = ' . intval($field->group) .
                    ' AND '   . dbName('name')  . ' = ' . $db->Quote($field->name)
                );
                throw new WCompositeException(
                    array(
                        JText::sprintf('WHD_CD:FAILED TO CREATE FIELD %s IN TABLE %s', $field->label, $field->tableName),
                        JText::sprintf('WHD_CD:COULD NOT CREATE FIELD DEFINITION %s IN TABLE %s', $field->label, $field->tableName)
                    )
                );
            }
        } else {
            WField::updateTable($field->type, $field->tableName, $field->groupName, $field);
        }

        WFactory::getOut()->log('Commited field to the database');
        return $field;
    }

    private function check($data) {
        // initialise return value
        $messages = array();
        $db = JFactory::getDBO();

        // check the name is valid
        if (!preg_match('~^[a-z]+$~', $data->name)) {
            $messages[] = JText::sprintf('WHD_CD:FIELD NAME %s IS NOT VALID', $data->name);
        } elseif ($data->version == null) {
            // if the field is new, check the name is unique
            $db->setQuery('DESCRIBE ' . dbTable($data->tableName));
            $tableFields = $db->loadObjectList();
            foreach ($tableFields AS $tableField) {
                if ($tableField->Field == 'field_'.$data->groupName.'_'.$data->name) {
                    $messages[] = JText::sprintf('WHD_CD:FIELD NAME %s MUST BE UNIQUE TO THE TABLE', $data->name);
                    continue;
                }
            }
        }

        // check for label
        if (trim($data->label) == '') {
            $messages[] = JText::_('WHD_CD:FIELD LABEL MISSING');
        }

        // ensure list value is 1 or 0
        $data->list   = $data->list   ? 1 : 0;

        // @todo check the type is valid
        $fieldTypeMessages = WField::check($data->type, $data->params);
        if (is_array($fieldTypeMessages)) {
            $messages = array_merge($messages, $fieldTypeMessages);
        }

        return count($messages) ? $messages : true;
    }

    /**
     * Checks in a field so as other users can edit it
     *
     * @param int $group
     * @param string $name
     */
    public function checkIn($group, $name) {
        $db = JFactory::getDBO();
        $db->setQuery(
            'UPDATE ' . dbTable('data_fields') .
            ' SET ' . dbName('checked_out') . ' = 0,' .
            dbName('checked_out_time') . ' = ' . $db->Quote($db->getNullDate()) .
            ' WHERE ' . dbName('group') . ' = ' . intval($group) .
            ' AND ' . dbName('name') . ' = ' . $db->Quote($name)
        );
        $db->query();
    }

}
