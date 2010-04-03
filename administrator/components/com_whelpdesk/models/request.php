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

    private $_repliesList;

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
    function getRepliesList($reset=false)
    {
        // Check if we can use cached list.
        if ($reset || !$this->_repliesList)
        {
            // Get the list
            wimport('list.list');
            $xml = JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'lists'.DS.'requestreplies.xml';
            $this->_repliesList = &WList::getInstance($xml);
        }

        return $this->_repliesList;
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
}
