<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.controller');

abstract class FaqcategoryWController extends WController {

    public function __construct() {
        $this->setType('faqcategory');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return b
     */
    public function commit($id, $array) {
        // get the model
        $model = WModel::getInstanceByName('faqcategory');

        try {
            // attempt to save the data
            $id = $model->save($id, $array);
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_FAQCATEGORY:INVALID CATEGORY DATA'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return $id;









        /*// get the table
        $table = WFactory::getTable('faqcategory');

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

        // add to the tree if necessary
        if (!array_key_exists('id', $array)) {
            try {
                $accessSession = WFactory::getAccessSession();
                $accessSession->addNode('faqcategory', $table->id, $table->alias, 'faqcategories', 'faqcategories');
                WFactory::getOut()->log('Commited FAQ category to the tree');
            } catch (Exception $e) {
                WFactory::getOut()->log('Failed to commit FAQ category to the tree', $e->getMessage());
                return false;
            }
        }

        WFactory::getOut()->log('Commited FAQ category to the database');
        return true;*/
    }

}
