<?php
/**
 * @version $Id: state.php 100 2009-05-03 18:05:10Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomain.php');

class KnowledgedomainStateWController extends KnowledgedomainWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('state');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        /*try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAIN STATE ACCESS DENIED');
            return;
        }*/
        
        // get the table
        $table = WFactory::getTable('knowledgedomain');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY NO TERMS SELECTED'));
            return;
        }

        // itterate over KDs
        foreach ($cid AS $id) {
            // check access control
            if (!$this->hasAccess($id)) {
                // no access :(
                JError::raiseWarning('401', JText::sprintf('WHD KNOWLEDGE DOMAIN %s STATE ACCESS DENIED', $id));
                continue;
            }

            // publish the KD
            if ($table->publish(array($id), (($stage == 'publish') ? 1 : 0))) {
                JError::raiseNotice('200', JText::sprintf('WHD KNOWLEDGE DOMAIN %d STATE CHANGED', $id));
            } else {
                JError::raiseWarning('500', JText::sprintf('WHD KNOWLEDGE DOMAIN %d STATE CHANGE FAILED', $id));
            }
        }

        // return to the list screen
        JRequest::setVar('task', 'knowledgedomains.list.start');
    }
}

?>