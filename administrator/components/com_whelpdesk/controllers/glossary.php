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

abstract class GlossaryWController extends WController {

    public function __construct() {
        $this->setEntity('glossary');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($array) {
        // get the table
        $table = WFactory::getTable('glossary');

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

        // store the data in the database table and update nulls
        if (!$table->revise()) {
            // failed
            WFactory::getOut()->log('Failed to increment version counter', true);
            return false;
        }

        WFactory::getOut()->log('Commited glossary term to the database');
        return true;
    }
}

?>