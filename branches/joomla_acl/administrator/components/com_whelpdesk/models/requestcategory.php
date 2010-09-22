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

class ModelRequestCategory extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->_tableName = 'requestcategory';
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getRequestCategory($id, $reload = false) {
        return parent::getTable($id, $reload);
    }

    public function getRootRequestCategory()
    {
        wimport('database.query');

        // Prepare the query
        $query = new WDatabaseQuery();
        $query->select('*');
        $query->from(dbTable('request_categories'));
        $query->order(dbName('lft'));

        // Execute the query.
        $db = &JFactory::getDbo();
        $db->setQuery($query);
        $results = $db->loadObjectList('id');

        // Set children categories.
        $root;
        foreach($results AS $category)
        {
            if ($category->parent_id > 0)
            {
                $results[$category->parent_id]->children[] = $category;
            }
            else
            {
                $root = $category;
            }
        }

        // Sort the categories by name.
        $this->_sortChildrenByName($root);

        // return the root category.
        return $root;
    }

    private function _sortChildrenByName($category)
    {
        if (is_array($category->children))
        {
            JArrayHelper::sortObjects(
                $category->children,
                'name'
            );

            foreach($category->children AS $child)
            {
                $this->_sortChildrenByName($child);
            }
        }
    }

    public function delete($id) {
        // @todo
        
        /*if (is_array($id)) {
            for ($i = 0, $c = count($id); $i < $c; $i++) {
                $id[$i] = $this->delete($id[$i]);
            }
            return $id;
        } else {
            return $table = WFactory::getTable('requestcategory')->delete($id);
        }*/
    }

    /**
     *
     * @param int $id
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($id, $data, $formType) {
        // get the table and reset the data
        $table = $this->getTable();
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

        // run advanced validation using JForm object
        $form = $this->getForm($table, false, $formType);
        $check = $form->validate($table);
        if (!$check)
        {
            $check = array();
            $errors = $form->getErrors();
            $totalErrors = count($form->getErrors());
            for ($i = 0; $i < $totalErrors; $i++)
            {
                $check[] = $errors[$i];
            }
            WFactory::getOut()->log('Form data failed to check', true);
            throw new WCompositeException($check);
        }

        // run simple validation (very loose rules)
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
        if (!$table->revise()) {
            // failed
            WFactory::getOut()->log('Failed to increment revision counter', true);
            return false;
        }

        WFactory::getOut()->log('Commited request category to the database');
        return $table->id;
    }

}
