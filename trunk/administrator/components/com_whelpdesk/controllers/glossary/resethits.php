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

/**
 * Get the parent class GlossaryWController
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * Resets the hits counter for glossary items. Can only deal with a maximum of
 * one term at a time.
 */
class GlossaryResethitsWController extends GlossaryWController {

    /**
     * @todo document
     */
    public function  __construct() {
        parent::__construct();
        $this->setUsecase('resethits');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD_GLOSSARY:RESET HITS ACCESS DENIED');
            return;
        }
        
        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD_GLOSSARY:TERM DOES NOT EXIST'));
            return;
        }
        $table->load($id);

        // make sure the record isn't already checked out
        // this should never occur unless a user has been very tardy with their
        // session and windows/tabs.
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            JError::raiseWarning('500', 'WHD_GLOSSARY:TERM IS CHECKEDOUT');
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }

        // reset the hit vounter
        $table->resetHits();

        // return to the edit screen
        JRequest::setVar('task', 'glossary.edit.start');
        JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY RESET HITS'));
    }
}

?>