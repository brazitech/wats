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

class DatagroupWModel extends WModel {

    public function  __construct() {
        $this->setName('datagroup');
        $this->setDefaultFilterOrder('g.ordering');
    }

    public function getGroup($id, $reload = false) {
        $table = WFactory::getTable('datagroup');
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

    function getAllGroups() {
        $db = JFactory::getDBO();
        $query = new JQuery;

        $query->select('g.*');
		$query->from('#__whelpdesk_data_groups AS g');

        $query->select('t.name AS tableName');
        $query->join('LEFT', '#__whelpdesk_data_tables AS t ON t.id = g.table');

        $query->order('t.name, g.ordering');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

}
