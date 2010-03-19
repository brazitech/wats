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

class ModelRequestPriority extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->_tableName = 'requestpriority';
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getRequestPriority($id, $reload = false) {
        $table = WFactory::getTable('requestpriority');
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

    /**
     *
     * @param int $id
     * @param array $data
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    function save($id, $data) {
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
        $form = $this->_form;
        $form->bind($table);
        $check = $form->validate($table);
        if (!$check)
        {
            $check = array();
            $totalErrors = count($form->getErrors());
            for ($i = 0; $i < $totalErrors; $i++)
            {
                $check[] = $form->getError($i, true);
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

        // store the data in the database table
        if (!$table->store()) {
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

        WFactory::getOut()->log('Commited request priority to the database');
        return $table->id;
    }

}
