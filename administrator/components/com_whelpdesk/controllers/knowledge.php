<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

abstract class KnowledgeWController extends WController {

    public function __construct() {
        $this->setType('knowledge');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @todo
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($array) {
        /*// get the table
        $table = WFactory::getTable('knowledgedomain');

        // allow raw untrimmed value for description
        $array['description'] = JRequest::getString('description', '', 'POST', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM);

        // bind $post with $table
        if (!$table->bind($array)) {
            // failed
            WFactory::getOut()->log('Failed to bind with table', true);
            return false;
        }

        // check the data is valid
        if (!$table->check()) {
            // failed
            WFactory::getOut()->log('Table data failed to check', true);
            return false;
        }

        // store the data in the database table and update nulls
        if (!$table->store()) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            return false;
        }

        WFactory::getOut()->log('Commited knowledge domain to the database');
        return true;*/
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'knowledgedomain.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN UNKNOWN'));
        }
        return $id;
    }
}

?>