<?php
/**
 * @version $Id: edit.php 149 2009-07-24 15:17:48Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

class GlossaryCopyWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        // this is a bit odd - in that the usecase is actually 'copy'. However
        // this would lead to unnecesary complication of the permissions, so we
        // may as well lump these in together
        $this->setUsecase('edit');
    }

    /**
     * @todo check token
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD_GLOSSARY:COPY ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data so as we can checkin the record we are copying from
        $id = WModel::getId();
        if ($id) {
            if($table->load($id)) {
                // make sure the record isn't checked out by anyone else
                if (!$table->isCheckedOut(JFactory::getUser()->get('id'))) {
                    // stop editing, checkin the record
                    $table->checkIn();
                }
            }
        }

        // reset the data - we have to reset id manually because the
        // JTable::reset() method ignores the PK... for some unknown reason...
        JRequest::setVar('id', null);
        $table->id = null;
        $table->reset();

        // now we can create the copy!
        JRequest::setVar('task', 'glossary.create.apply');
    }

    /**
     * Overrides GlossaryWController::commit() and automatically pulls in the
     * new data from the request (this must be POST data). This takes account of
     * state change permissions such that the state cannot be changed through
     * this method unless the user has the necessary permissions.
     *
     * @return bool|int On fail returns boolean false, on success returns the PK value
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        // check if we should allow state change
        $canChangeState = false;
        try {
            $canChangeState = WFactory::getAccessSession()->hasAccess('user', JFactory::getUser()->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        if (!$canChangeState) {
            unset($post['published']);
        }

        // commit the changes
        return parent::commit($post);
    }
}

?>