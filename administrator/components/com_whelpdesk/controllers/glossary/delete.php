<?php
/**
 * @version $Id: edit.php 127 2009-06-11 13:57:35Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

class GlossaryDeleteWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
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
            JError::raiseWarning('401', 'WHD_GLOSSARY:EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_GLOSSARY:NO TERMS SELECTED'));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }
        
        // itterate over glossary terms
        $unknownTerms = 0;
        foreach($cid AS $id) {
            if(!$table->load($id)) {
                // term failed to load, assume it is unknown
                $unknownTerms++;
            } elseif($table->isCheckedOut(JFactory::getUser()->get('id'))) {
                // term is checked out - cannot delete
                JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %S IS CHECKEDOUT', $table->term));
            } else {
                // okay to delete the term!
                $table->delete();
                WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:DELETED TERM %s', $table->term));
            }
        }

        if ($unknownTerms == 1) {
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM DOES NOT EXIST', $unknownTerms));
        } elseif ($unknownTerms > 1) {
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:%D TERMS DO NOT EXIST', $unknownTerms));
        }

        // all done, move to the list
        JRequest::setVar('task', 'glossary.list.start');
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