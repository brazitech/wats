<?php
/**
 * @version $Id: glossary.php 120 2009-05-22 14:05:02Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

abstract class DocumentWController extends WController {

    public function __construct() {
        $this->setType('document');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($array) {
        // get the table
        $table = WFactory::getTable('document');

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
        if (!$table->store(true)) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            return false;
        }

        WFactory::getOut()->log('Commited document to the database');

        // add to the tree if necessary
        if (!array_key_exists('id', $array)) {
            try {
                $accessSession = WFactory::getAccessSession();
                $accessSession->addNode('document', $table->id, $table->alias, 'documentcontainer', intval($array['parent']));
                WFactory::getOut()->log('Commited document container to the tree');
            } catch (Exception $e) {
                WFactory::getOut()->log('Failed to commit document to the tree', $e->getMessage());
                return false;
            }
        }

        return true;
    }

}
