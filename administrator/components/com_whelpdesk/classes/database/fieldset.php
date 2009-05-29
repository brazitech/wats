<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 * @subpackage classes
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.field');

class WFieldset {

    private $groups = array();

    private $data;

    private static $instances = array();

    /**
     *
     * @param string $table
     * @return WFieldset
     */
    public function getInstance($table) {
        if (!array_key_exists($table, self::$instances)) {
            self::$instances[$table] = new WFieldset($table);
        }

        return self::$instances[$table];
    }

    public function  __construct($table) {
        // get ready
        $db = JFactory::getDBO();
        $this->table = $table;
        
        // get the groups
        $sql = 'SELECT * FROM ' . dbTable('data_groups')
             . ' WHERE ' . dbName('table') . ' = ' . $db->Quote($table)
             . ' ORDER BY ' . dbName('ordering');
        $db->setQuery($sql);
        $this->groups = $db->loadObjectList('name');

        // get the fields
        $groupNames = $this->getGroupNames();
        for ($i = 0, $c = count($groupNames); $i < $c; $i++) {
            // do some prep work
            $groupName = $groupNames[$i];
            $this->groups[$groupName]->fields  = array();

            // query the database for fields
            $sql = 'SELECT * FROM ' . dbTable('data_fields')
                 . ' WHERE ' . dbName('group') . ' = ' . $db->Quote($this->groups[$groupName]->id)
                 . ' ORDER BY ' . dbName('ordering');
            $db->setQuery($sql);
            $fields = $db->loadAssocList();

            // add the fields to the group
            for ($z = 0, $t = count($fields); $z < $t; $z++) {
                $field = WField::getInstance($fields[$z]['type'], $fields[$z]);
                $this->groups[$groupName]->fields[] = $field;
            }
        }
    }

    /**
     * Gets the specified group.
     *
     * @throws WException
     * @param string $group
     * @return WField[]
     */
    public function getGroup($group) {
        if (!array_key_exists($group, $this->groups)) {
            throw new WException('UNKNOWN DATASET GROUP %s FOR TABLE %s', $group, $this->table);
        }

        return $this->groups[$group];
    }

    /**
     * Gets a list of names of each group defined in this dataset
     *
     * @return string[]
     */
    public function getGroupNames() {
        return array_keys($this->groups);
    }


    /**
     * Gets an array of fields. If we provide a group name the fields that are
     * returned will be limited to that group.
     *
     * @throws WException
     * @param string $group Name of the group we want the fields for
     * @return WField[]
     */
    public function getFields($group=null) {
        // check if we are only dealing with a single group
        if ($group != null) {
            return $this->getGroup($group)->fields;
        }

        // build an array of all fields from all the groups
        $allFields = array();
        if (count($this->groups)) {
            foreach ($this->groups AS $group) {
                $allFields = array_merge($allFields, $group->fields);
            }
        }

        return $allFields;
    }

    /**
     *
     * @param object|array $data
     */
    public function setData($data) {
        // covert data to object if necessary
        if (is_array($data)) {
            $data = JArrayHelper::toObject();
        }

        $this->data = $data;
    }

    public function getListFields() {
        $fields = $this->getFields();
        $listFields = array();

        // itterate over all fields
        if (count($fields)) {
            foreach ($fields as $field) {
                if ($field->isListField()) {
                    // is a listable field, add to the array
                    $listFields[] = $field;
                }
            }
        }

        // all done!
        return $listFields;
    }
}
