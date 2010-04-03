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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faq.php');

class FaqStateWController extends FaqWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('state');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        
        // get the table
        $table = WFactory::getTable('faq');

        // load the IDs
        $rawCids = WModel::getAllIds();
        if (!count($rawCids)) {
            JRequest::setVar('task', 'faq.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD NO FAQS SELECTED'));
            return;
        }

        $cid = array();
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        foreach($rawCids AS $rawCid) {
            // check if we can allow state changes
            try {
                $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                            'faq', $rawCid,
                                                            'faq', 'state');
            } catch (Exception $e) {
                $canChangeState = false;
            }

            if ($canChangeState) {
                $cid[] = $rawCid;
            } else {
                JError::raiseWarning('403', JText::sprintf('WHD COULD NOT ' . ($stage == 'publish' ? '' : 'UN') . 'PUBLISHED FAQ %s', $cid));
            }
        }

        // publish the identified faqs
        $table->publish($cid, (($stage == 'publish') ? 1 : 0), JFactory::getUser()->get('id'));

        // return to the edit screen
        JRequest::setVar('task', 'faq.list.start');
        JError::raiseNotice('INPUT', JText::_('WHD ' . ($stage == 'publish' ? '' : 'UN') . 'PUBLISHED FAQS'));
    }
}

?>