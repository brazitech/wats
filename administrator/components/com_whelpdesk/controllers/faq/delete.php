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

class FaqDeleteWController extends FaqWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('delete');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the table
        $table = WFactory::getTable('faq');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'faq.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD NO FAQS SELECTED'));
            return;
        }

        foreach ($cid AS $id) {
            // check access control
            $table->load($id);
            if (!$this->hasAccess($id)) {
                // no access :(
                JError::raiseWarning('401', JText::sprintf('WHD FAQ %s DELETE ACCESS DENIED', $table->question));
                continue;
            }
            
            // make sure the FAQ isn't checked out
            if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
                JError::raiseWarning('500', 'WHD FAQ CANNOT DELETE FAQ %s FAQ IS CHECKED OUT', $table->question);
            } else {
                // attempt to delete the node and FAQ
                // note that the treeSession will deal with the FAQ delete as well
                // as the node delete
                $accessSession = WFactory::getAccessSession();
                try {
                    $accessSession->deleteNode('faq', $id, true);
                    WMessageHelper::message(JText::sprintf('WHD DELETED FAQ %s', $table->question));
                } catch (Exception $e) {
                    JError::raiseWarning('500', 'WHD DELETE FAQ %s FAILED', $table->question);
                }
            }
        }

        // list the remianing FAQs
        JRequest::setVar('task', 'faq.list');
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // commit the changes
        return parent::commit($post);
    }
}

?>