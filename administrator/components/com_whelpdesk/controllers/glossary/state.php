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
 * Import parent class GlossaryWController
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * Changes the published state of one or more glossary terms.
 */
class GlossaryStateWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('state');
    }

    /**
     * Publishes glossary terms. This controller does not take any account of
     * the checked out status of glossary terms. This is because this is only a
     * simple data edit and users who have checked out a term may not
     * necessarily have the necessary access to change the state of the term
     * anyway.
     *
     * @param string $stage The stage in the use case, this should be 'publish' or 'unpublish'
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('403', 'WHD_GLOSSARY:STATE ACCESS DENIED');
            return;
        }
        
        // get the table
        $table = WFactory::getTable('glossary');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_GLOSSARY:NO TERMS SELECTED'));
            return;
        }

        // publish the identified terms
        $table->publish($cid, (($stage == 'publish') ? 1 : 0));

        // return to the edit screen
        JRequest::setVar('task', 'glossary.list.start');
        if ($stage == 'publish') {
            WMessageHelper::message(JText::_('WHD_GLOSSARY:PUBLISHED TERMS'));
        } else {
            WMessageHelper::message(JText::_('WHD_GLOSSARY:UNPUBLISHED TERMS'));
        }
    }
}

?>