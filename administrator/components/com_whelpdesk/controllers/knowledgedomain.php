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

abstract class KnowledgedomainWController extends WController {

    public function __construct() {
        $this->setType('knowledgedomain');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($id, $data) {
        // get the model
        $model = WModel::getInstanceByName('knowledgedomain');

        try {
            // attempt to save the data
            $id = $model->save($id, $data);
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_KD:INVALID KNOWLEDGE DATA'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return $id;




        // get the table
        $table = WFactory::getTable('knowledgedomain');

        // allow raw untrimmed value for description
        $data['description'] = JRequest::getString('description', '', 'POST', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM);

        // bind $post with $table
        if (!$table->bind($data)) {
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
        return true;
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            // try getting id from alias instead
            $domain = JRequest::getVar('domain');
            if ($domain) {
                $db = JFactory::getDBO();
                $sql = 'SELECT ' . dbName('id')
                     . ' FROM ' . dbTable('knowledge_domain')
                     . ' WHERE ' . dbName('alias') . ' = ' . $db->Quote($domain);
                $db->setQuery($sql);
                $id = $db->loadResult();
            }

            if (!$id) {
                // still don't have the ID? Then, give up!
                JRequest::setVar('task', 'knowledgedomain.list.start');
                JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN UNKNOWN'));
            }
        }
        return $id;
    }
}

?>