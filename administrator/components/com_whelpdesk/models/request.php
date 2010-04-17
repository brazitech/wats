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

class ModelRequest extends WModel {

    private $_repliesList = array();

    private $_isEditForm = false;

    public function  __construct() {
        parent::__construct();
        $this->_tableName = 'request';
    }

    protected function _populateState() {
        parent::_populateState();
    }

    public function getRequest($id, $reload = false) {
        return parent::getTable($id, $reload);
    }

    /**
     * Method to get a list object of replies.
     *
     * @param   boolean     $reset      Optional argument to force load a new form.
     * @return  mixed       WList       object on success, False on error.
     */
    function getRepliesList($id, $reset=false)
    {
        // Check if we can use cached list.
        if ($reset || !$this->_repliesList[$id])
        {
            // Set the request_id filter value.
            JRequest::setVar('request_id', $id);
            
            // Get the list
            wimport('list.list');
            $xml = JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'lists'.DS.'requestreplies.xml';
            $this->_repliesList[$id] = &WList::getInstance($xml);
        }

        return $this->_repliesList[$id];
    }

    public function setIsEditForm($isEditForm)
    {
        $this->_isEditForm = $isEditForm;
    }

    /**
     *
     * @return WForm
     */
    protected function _getFormInstance()
    {
        return WForm::getInstance($this->getName(), $this->getName(), true, array());
    }

    /**
     *
     * @param int $id
     * @param array $data
     * @param string $formType Type of form
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    public function save($id, $data, $formType=null) {
        // get the tables
        $table = $this->getTable();
        $table->id = $id;
        if ($id)
        {
            $replyTable = WFactory::getTable('requestreply');
        }

        // make sure we do not override the supplied ID
        unset($data['id']);

        // load the base values
        if ($id)
        {
            if (!$table->load($id))
            {
                WFactory::getOut()->log('Failed to load base data from table', true);
                return false;
            }
        }

        // bind data with the table
        if (!$table->bind($data, array(), true))
        {
            // failed
            WFactory::getOut()->log('Failed to bind with table', true);
            return false;
        }

        // bind data with the reply table a reply is expected
        if ($id)
        {
            $replyTable->reset();
            $replyTable->set('description', $data['reply_description']);
            $replyTable->set('created_by',  $data['reply_created_by']);
            $replyTable->set('request_id',  $id);
            var_dump($data);
        }

        // deal with created and modified dates
        $date  = new JDate();
        $table->modified = $date->toMySQL();
        if (!$id)
        {
            $table->created = $date->toMySQL();
        }
        else
        {
            $replyTable->set('created',  $date->toMySQL());
        }

        // run advanced validation using JForm object
        $form = $this->getForm($table, false, $formType);
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
        if ($id)
        {
            $baseCheck = $table->check();
            $replyCheck = $replyTable->check();
            $check = null;
            if (is_array($baseCheck) && is_array($replyCheck))
            {
                $check = array_merge($baseCheck, $replyCheck);
            }
            elseif (is_array($baseCheck) && !is_array($replyCheck))
            {
                $check = $baseCheck;
            }
            elseif (!is_array($baseCheck) && is_array($replyCheck))
            {
                $check = $replyCheck;
            }
        }
        else
        {
            $check = $table->check();
        }
        
        // make sure validation was okay
        if (is_array($check))
        {
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

        // store the reply in the database
        if ($id)
        {
            if (!$replyTable->store()) {
                // failed
                WFactory::getOut()->log('Failed to save reply', true);
                return false;
            }
        }

        WFactory::getOut()->log('Commited help request to the database');
        return $table->id;
    }
}
