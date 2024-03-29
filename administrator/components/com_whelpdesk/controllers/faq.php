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

abstract class FaqWController extends WController {

    public function __construct() {
        $this->setType('faq');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($array) {
        // get the table
        $table = WFactory::getTable('faq');

        // allow raw untrimmed value for answer
        $array['answer'] = JRequest::getString('answer', '', 'POST', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM);

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

        // add to the tree if necessary
        if (!array_key_exists('id', $array)) {
            try {
                $accessSession = WFactory::getAccessSession();
                $accessSession->addNode('faq', $table->id, $table->alias, 'faqcategory', $table->category);
                WFactory::getOut()->log('Commited FAQ to the tree');
            } catch (Exception $e) {
                WFactory::getOut()->log('Failed to commit FAQ category to the tree', $e->getMessage());
                return false;
            }
        }

        WFactory::getOut()->log('Commited FAQ to the database');
        return true;
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        return ($id) ? $id : 'faqcategories';
    }
}

?>