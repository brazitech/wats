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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

class GlossaryStateWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('state');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD GLOSSARY STATE ACCESS DENIED');
            return;
        }
        
        // get the table
        $table = WFactory::getTable('glossary');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY NO TERMS SELECTED'));
            return;
        }

        // publish the identified terms
        $table->publish($cid, (($stage == 'publish') ? 1 : 0));

        // return to the edit screen
        JRequest::setVar('task', 'glossary.list.start');
        JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY PUBLISHED TERMS'));
    }
}

?>